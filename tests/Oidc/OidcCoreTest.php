<?php declare(strict_types=1);

require_once __DIR__ . '/Mocks.php';

use Logto\Sdk\Oidc\OidcCore;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Logto\Sdk\Oidc\TokenResponse;
use GuzzleHttp\Client;
use Logto\Sdk\LogtoException;

final class OidcCoreTest extends TestCase
{
  function test_generateState()
  {
    $this->assertSame(
      strlen(OidcCore::generateState()),
      43,
    );
  }

  function test_generateCodeVerifier()
  {
    $this->assertSame(
      strlen(OidcCore::generateCodeVerifier()),
      43,
    );
  }

  function test_generateCodeChallenge()
  {
    $this->assertSame(
      OidcCore::generateCodeChallenge('12345678901234567890123456789012345678901234567890'),
      '9Y__uhKapn7GO_ElcaQpd8C3hdOyqTzAU4VXyR2iEV0',
    );
  }

  protected function getInstance(Response ...$responses)
  {
    return new MockOidcCore(Mocks::getOidcProviderMetadata(), new Client(['handler' => new MockHandler($responses)]));
  }
  protected function getTokenResponse()
  {
    return new TokenResponse(access_token: 'access_token', token_type: 'Bearer', expires_in: 3600);
  }

  function test_fetchTokenByCode()
  {
    $client = $this->getInstance(Mocks::mockResponse($this->getTokenResponse()));
    $result = $client->fetchTokenByCode(
      clientId: 'client-id',
      clientSecret: 'client-secret',
      redirectUri: 'http://localhost:5000/callback',
      code: 'code',
      codeVerifier: 'code-verifier',
    );
    $this->assertEquals($result, $this->getTokenResponse());
  }

  function test_fetchTokenByCode_failure()
  {
    $client = $this->getInstance(Mocks::mockResponse(["error" => "invalid_target"], 400));

    // Unable to construct token response
    $this->expectException(ArgumentCountError::class);
    $client->fetchTokenByCode(
      clientId: 'client-id',
      clientSecret: 'client-secret',
      redirectUri: 'http://localhost:5000/callback',
      code: 'code',
      codeVerifier: 'code-verifier',
    );
  }

  function test_fetchTokenByRefreshToken()
  {
    $client = $this->getInstance(Mocks::mockResponse($this->getTokenResponse()));
    $result = $client->fetchTokenByRefreshToken(
      clientId: 'client-id',
      clientSecret: 'client-secret',
      refreshToken: 'refresh-token',
    );
    $this->assertEquals($result, $this->getTokenResponse());
  }

  function test_fetchTokenByRefreshToken_failure()
  {
    $client = $this->getInstance(Mocks::mockResponse(["error" => "invalid_target"], 400));

    // Unable to construct token response
    $this->expectException(ArgumentCountError::class);
    $client->fetchTokenByRefreshToken(
      clientId: 'client-id',
      clientSecret: 'client-secret',
      refreshToken: 'refresh-token',
    );
  }

  function test_verifyIdToken()
  {
    $client = $this->getInstance();
    $client->jwkSet = new MockKeySet();
    $this->expectNotToPerformAssertions();
    $client->verifyIdToken(MockKeySet::$signedIdToken, "foo");
  }

  function test_verifyIdToken_failure()
  {
    $client = $this->getInstance();
    $client->jwkSet = new MockKeySet();
    $this->expectException(LogtoException::class);
    $this->expectExceptionMessage('Invalid ID token: aud is not foo1.');
    $client->verifyIdToken(MockKeySet::$signedIdToken, "foo1");
  }
}
