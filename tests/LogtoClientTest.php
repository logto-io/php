<?php declare(strict_types=1);

require_once __DIR__ . '/Oidc/Mocks.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use Logto\Sdk\LogtoClient;
use Logto\Sdk\LogtoConfig;
use Logto\Sdk\Oidc\OidcCore;
use Logto\Sdk\InteractionMode;
use Logto\Sdk\Storage\Storage;
use Logto\Sdk\Prompt;
use Logto\Sdk\Storage\StorageKey;
use Logto\Sdk\LogtoException;
use Logto\Sdk\Oidc\TokenResponse;
use Logto\Sdk\Models\AccessTokenClaims;
use Logto\Sdk\Models\IdTokenClaims;
use Logto\Sdk\Oidc\UserInfoResponse;
use Logto\Sdk\Constants\UserScope;

class MemoryStorage implements Storage
{
  private array $data = [];
  public function get(StorageKey $key): ?string
  {
    return $this->data[$key->value] ?? null;
  }
  public function set(StorageKey $key, ?string $value): void
  {
    $this->data[$key->value] = $value;
  }
  public function delete(StorageKey $key): void
  {
    unset($this->data[$key->value]);
  }
}

class FakeOidcCore extends MockOidcCore
{
  function verifyIdToken(string $a, string $b)
  {
    // Pass
  }

  function fetchUserInfo(string $accessToken): UserInfoResponse
  {
    return new UserInfoResponse(
      sub: "user1",
      name: "John Wick",
      username: "john",
      email: "john@wick.com",
    );
  }
}

class MockLogtoClient extends LogtoClient
{
  public Storage $storage;
  function __construct(public LogtoConfig $config, array ...$responses)
  {
    $this->storage = new MemoryStorage();
    $this->oidcCore = new FakeOidcCore(
      Mocks::getOidcProviderMetadata(),
      new Client(['handler' => new MockHandler(...array_values($responses))])
    );
  }

  // Promote visibility for testing
  public OidcCore $oidcCore;
}

final class LogtoClientTest extends TestCase
{
  protected function getInstance(LogtoConfig $config = new LogtoConfig(endpoint: "http://localhost:3001", appId: "app-id"), array ...$responses)
  {
    return new MockLogtoClient($config, ...array_values($responses));
  }

  function test_signIn()
  {
    $client = $this->getInstance();
    $this->assertSame(
      $client->signIn("redirectUri", InteractionMode::signUp),
      "https://logto.app/oidc/auth?client_id=app-id&redirect_uri=redirectUri&response_type=code&scope=openid+offline_access+profile&prompt=consent&code_challenge=codeChallenge&code_challenge_method=S256&state=state&interaction_mode=signUp"
    );
  }

  function test_signIn_multipleResources()
  {
    $client = $this->getInstance(new LogtoConfig(endpoint: "http://localhost:3001", appId: "app-id", resources: ["https://resource1", "https://resource2"]));
    $this->assertSame(
      $client->signIn("redirectUri", InteractionMode::signUp),
      "https://logto.app/oidc/auth?client_id=app-id&redirect_uri=redirectUri&response_type=code&scope=openid+offline_access+profile&prompt=consent&code_challenge=codeChallenge&code_challenge_method=S256&state=state&interaction_mode=signUp&resource=https%3A%2F%2Fresource1&resource=https%3A%2F%2Fresource2"
    );
  }

  function test_signIn_multipleScopes()
  {
    $client = $this->getInstance(new LogtoConfig(endpoint: "http://localhost:3001", appId: "app-id", scopes: ["email", "phone", "email"]));
    $this->assertSame(
      $client->signIn("redirectUri"),
      "https://logto.app/oidc/auth?client_id=app-id&redirect_uri=redirectUri&response_type=code&scope=email+phone+openid+offline_access+profile&prompt=consent&code_challenge=codeChallenge&code_challenge_method=S256&state=state"
    );
  }

  function test_signIn_allConfigs()
  {
    $client = $this->getInstance(new LogtoConfig(endpoint: "http://localhost:3001", appId: "app-id", scopes: ["email", UserScope::phone], resources: ["https://resource1", "https://resource2"], prompt: Prompt::login));
    $this->assertSame(
      $client->signIn("redirectUri", InteractionMode::signUp),
      "https://logto.app/oidc/auth?client_id=app-id&redirect_uri=redirectUri&response_type=code&scope=email+phone+openid+offline_access+profile&prompt=login&code_challenge=codeChallenge&code_challenge_method=S256&state=state&interaction_mode=signUp&resource=https%3A%2F%2Fresource1&resource=https%3A%2F%2Fresource2"
    );
  }

  function test_signIn_organizationScope()
  {
    $client = $this->getInstance(new LogtoConfig(endpoint: "http://localhost:3001", appId: "app-id", scopes: ["email", UserScope::organizations]));
    $this->assertSame(
      $client->signIn("redirectUri"),
      "https://logto.app/oidc/auth?client_id=app-id&redirect_uri=redirectUri&response_type=code&scope=email+urn%3Alogto%3Ascope%3Aorganizations+openid+offline_access+profile&prompt=consent&code_challenge=codeChallenge&code_challenge_method=S256&state=state&resource=urn%3Alogto%3Aresource%3Aorganizations"
    );
  }

  function test_signOut()
  {
    $client = $this->getInstance();

    $client->storage->set(StorageKey::idToken, "idToken");
    $client->storage->set(StorageKey::refreshToken, "refreshToken");
    $client->storage->set(StorageKey::accessTokenMap, "accessTokenMap");

    $this->assertSame(
      $client->signOut("redirectUri"),
      "https://logto.app/oidc/logout?client_id=app-id&post_logout_redirect_uri=redirectUri"
    );

    $this->assertNull($client->storage->get(StorageKey::idToken));
    $this->assertNull($client->storage->get(StorageKey::refreshToken));
    $this->assertNull($client->storage->get(StorageKey::accessTokenMap));
  }

  function test_handleSignInCallback_sessionNotFound()
  {
    $client = $this->getInstance();
    $this->expectException(LogtoException::class);
    $this->expectExceptionMessage("Sign-in session not found.");
    $client->handleSignInCallback();
  }

  function test_handleSignInCallback_pathDoesNotMatch()
  {
    $_SERVER['SERVER_NAME'] = 'localhost';
    $_SERVER['REQUEST_URI'] = '/foo';
    $client = $this->getInstance();
    $client->storage->set(
      StorageKey::signInSession,
      '{"redirectUri": "https://redirect_uri/some_path", "codeVerifier": "codeVerifier", "state": "state"}',
    );
    $this->expectException(LogtoException::class);
    $this->expectExceptionMessage("The redirect URI in the sign-in session does not match the current request.");
    $client->handleSignInCallback();
  }

  function test_handleSignInCallback_stateDoesNotMatch()
  {
    $_SERVER['SERVER_NAME'] = 'redirect_uri';
    $_SERVER['REQUEST_URI'] = '/some_path';
    $_SERVER['QUERY_STRING'] = null;
    $client = $this->getInstance();
    $client->storage->set(
      StorageKey::signInSession,
      '{"redirectUri": "https://redirect_uri/some_path", "codeVerifier": "codeVerifier", "state": "state"}',
    );
    $this->expectException(LogtoException::class);
    $this->expectExceptionMessage("State mismatch.");
    $client->handleSignInCallback();
  }

  function test_handleSignInCallback_codeNotFound()
  {
    $_SERVER['SERVER_NAME'] = 'redirect_uri';
    $_SERVER['REQUEST_URI'] = '/some_path';
    $_SERVER['QUERY_STRING'] = 'state=state';
    $client = $this->getInstance();
    $client->storage->set(
      StorageKey::signInSession,
      '{"redirectUri": "https://redirect_uri/some_path", "codeVerifier": "codeVerifier", "state": "state"}',
    );
    $this->expectException(LogtoException::class);
    $this->expectExceptionMessage("Code not found.");
    $client->handleSignInCallback();
  }

  function test_handleSignInCallback()
  {
    $_SERVER['SERVER_NAME'] = 'redirect_uri';
    $_SERVER['REQUEST_URI'] = '/some_path';
    $_SERVER['QUERY_STRING'] = 'state=state&code=code';
    $tokenResponse = new TokenResponse(
      access_token: 'access_token',
      token_type: 'Bearer',
      expires_in: 3600,
      refresh_token: "refreshToken",
      id_token: "idToken",
    );
    $client = $this->getInstance(responses: [new Response(body: json_encode($tokenResponse))]);
    $client->storage->set(
      StorageKey::signInSession,
      '{"redirectUri": "https://redirect_uri/some_path", "codeVerifier": "codeVerifier", "state": "state"}',
    );

    $this->expectNotToPerformAssertions();
    $client->handleSignInCallback();
  }

  function test_getAccessToken_cached()
  {
    $client = $this->getInstance();
    $this->assertNull($client->getAccessToken());
    $client->storage->set(
      StorageKey::accessTokenMap,
      '{"":{"token":"access_token","expiresAt": 9999999999}, "foo":{"token":"access_token_foo","expiresAt": 9999999999}}',
    );
    $this->assertSame($client->getAccessToken(), "access_token");
    $this->assertSame($client->getAccessToken(resource: "foo"), "access_token_foo");
  }

  function test_getAccessToken_noRefreshToken()
  {
    $client = $this->getInstance();
    $client->storage->set(StorageKey::accessTokenMap, '{}');
    $client->storage->set(StorageKey::refreshToken, null);
    $this->assertNull($client->getAccessToken());
  }

  function test_getAccessToken_useRefreshToken()
  {
    $client = $this->getInstance(responses: [
      new Response(
        body: json_encode(
          new TokenResponse(
            access_token: "accessToken",
            token_type: "Bearer",
            expires_in: 3600,
            refresh_token: "refreshToken"
          )
        )
      )
    ]);
    $client->storage->set(StorageKey::accessTokenMap, '{}');
    $client->storage->set(StorageKey::refreshToken, 'refreshToken');
    $this->assertSame($client->getAccessToken(), "accessToken");
  }

  function test_getOrganizationToken()
  {
    $client = $this->getInstance();
    $this->assertNull($client->getOrganizationToken('1'));
    $client->storage->set(
      StorageKey::accessTokenMap,
      '{"":{"token":"access_token","expiresAt": 9999999999}, "urn:logto:organization:1":{"token":"access_token_foo","expiresAt": 9999999999}}',
    );
    $this->assertSame($client->getOrganizationToken('1'), "access_token_foo");
  }

  function test_getAccessTokenClaims()
  {
    $client = $this->getInstance();

    // Not able to parse null
    $this->expectException(TypeError::class);
    $this->assertNull($client->getAccessTokenClaims());

    // Assign a valid access token raw string
    $accessToken = "eyJhbGciOiJSUzI1NiIsImtpZCI6IjEifQ.eyJpc3MiOiJodHRwczovL2xvZ3RvLmFwcCIsImF1ZCI6Imh0dHBzOi8vbG9ndG8uYXBwL2FwaSIsImV4cCI6OTk5OTk5OTk5OSwiaWF0IjoxNjE2NDQ2MzAwLCJzdWIiOiJ1c2VyMSIsInNjb3BlIjoiYWRtaW4gdXNlciIsImNsaWVudF9pZCI6InNhcXJlMW9xYmtwajZ6aHE4NWhvMCJ9.12345678901234567890123456789012345678901234567890";
    $client->storage->set(
      StorageKey::accessTokenMap,
      '{"":{"token":"' . $accessToken . '","expiresAt": 9999999999}}',
    );
    $this->assertEquals(
      $client->getAccessTokenClaims(),
      new AccessTokenClaims(
        iss: "https://logto.app",
        aud: "https://logto.app/api",
        exp: 9999999999,
        iat: 1616446300,
        sub: "user1",
        scope: "admin user",
        client_id: "saqre1oqbkpj6zhq85ho0",
      )
    );
  }

  function test_getOrganizationTokenClaims()
  {
    $client = $this->getInstance();

    // Not able to parse null
    $this->expectException(TypeError::class);
    $this->assertNull($client->getOrganizationTokenClaims('1'));

    // Assign a valid access token raw string
    $accessToken = "eyJhbGciOiJSUzI1NiIsImtpZCI6IjEifQ.eyJpc3MiOiJodHRwczovL2xvZ3RvLmFwcCIsImF1ZCI6Imh0dHBzOi8vbG9ndG8uYXBwL2FwaSIsImV4cCI6OTk5OTk5OTk5OSwiaWF0IjoxNjE2NDQ2MzAwLCJzdWIiOiJ1c2VyMSIsInNjb3BlIjoiYWRtaW4gdXNlciIsImNsaWVudF9pZCI6InNhcXJlMW9xYmtwajZ6aHE4NWhvMCJ9.12345678901234567890123456789012345678901234567890";
    $client->storage->set(
      StorageKey::accessTokenMap,
      '{"urn:logto:organization:1":{"token":"' . $accessToken . '","expiresAt": 9999999999}}',
    );
    $this->assertEquals(
      $client->getOrganizationTokenClaims('1'),
      new AccessTokenClaims(
        iss: "https://logto.app",
        aud: "https://logto.app/api",
        exp: 9999999999,
        iat: 1616446300,
        sub: "user1",
        scope: "admin user",
        client_id: "saqre1oqbkpj6zhq85ho0",
      )
    );
  }

  function test_getIdToken()
  {
    $client = $this->getInstance();
    $this->assertNull($client->getIdToken());
    $client->storage->set(StorageKey::idToken, "idToken");
    $this->assertSame($client->getIdToken(), "idToken");
  }

  function test_getIdTokenClaims()
  {
    $client = $this->getInstance();

    // Not able to parse null
    $this->expectException(TypeError::class);
    $this->assertNull($client->getIdTokenClaims());

    // Assign a valid access token raw string
    $idTokenString = "eyJhbGciOiJSUzI1NiIsImtpZCI6IjEifQ.eyJpc3MiOiJodHRwczovL2xvZ3RvLmFwcCIsImF1ZCI6ImZvbyIsImV4cCI6MTYxNjQ0NjQwMCwiaWF0IjoxNjE2NDQ2MzAwLCJzdWIiOiJ1c2VyMSIsIm5hbWUiOiJKb2huIFdpY2siLCJ1c2VybmFtZSI6ImpvaG4iLCJlbWFpbCI6ImpvaG5Ad2ljay5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZX0.12345678901234567890123456789012345678901234567890";
    $client->storage->set(
      StorageKey::idToken,
      $idTokenString,
    );
    $this->assertEquals(
      $client->getIdTokenClaims(),
      new IdTokenClaims(
        iss: "https://logto.app",
        aud: "foo",
        exp: 1616446400,
        iat: 1616446300,
        sub: "user1",
        name: "John Wick",
        username: "john",
        email: "john@wick.com",
        email_verified: true,
      )
    );
  }

  function test_getRefreshToken()
  {
    $client = $this->getInstance();
    $this->assertNull($client->getRefreshToken());
    $client->storage->set(StorageKey::refreshToken, "refreshToken");
    $this->assertSame($client->getRefreshToken(), "refreshToken");
  }

  function test_isAuthenticated()
  {
    $client = $this->getInstance();
    $this->assertFalse($client->isAuthenticated());
    $client->storage->set(StorageKey::idToken, "idToken");
    $this->assertTrue($client->isAuthenticated());
  }

  function test_fetchUserInfo()
  {
    $client = $this->getInstance();

    // Assign a valid access token raw string
    $accessToken = "eyJhbGciOiJSUzI1NiIsImtpZCI6IjEifQ.eyJpc3MiOiJodHRwczovL2xvZ3RvLmFwcCIsImF1ZCI6Imh0dHBzOi8vbG9ndG8uYXBwL2FwaSIsImV4cCI6OTk5OTk5OTk5OSwiaWF0IjoxNjE2NDQ2MzAwLCJzdWIiOiJ1c2VyMSIsInNjb3BlIjoiYWRtaW4gdXNlciIsImNsaWVudF9pZCI6InNhcXJlMW9xYmtwajZ6aHE4NWhvMCJ9.12345678901234567890123456789012345678901234567890";
    $client->storage->set(
      StorageKey::accessTokenMap,
      '{"":{"token":"' . $accessToken . '","expiresAt": 9999999999}}',
    );
    $this->assertEquals(
      $client->fetchUserInfo(),
      new UserInfoResponse(
        sub: "user1",
        name: "John Wick",
        username: "john",
        email: "john@wick.com"
      )
    );
  }

}
