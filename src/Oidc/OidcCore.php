<?php declare(strict_types=1);
namespace Logto\Sdk\Oidc;

use Firebase\JWT\CachedKeySet;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Client;
use Logto\Sdk\LogtoException;
use Logto\Sdk\Models\OidcProviderMetadata;
use Logto\Sdk\Utilities;
use Phpfastcache\CacheManager;
use Firebase\JWT\JWT;

/**
 * The core OIDC functions for the Logto client. Provider-agonistic functions
 * are implemented as static methods, while other functions are implemented as
 * instance methods.
 */
class OidcCore
{
  public const DEFAULT_SCOPES = ['openid', 'offline_access', 'profile'];

  /**
   * Create a OidcCore instance for the given Logto endpoint using the discovery URL.
   * Note it may take a few time to fetch the provider metadata since it will send a
   * network request.
   */
  static function create(string $logtoEndpoint): OidcCore
  {
    $client = new Client();
    $body = $client->get(
      $logtoEndpoint . '/oidc/.well-known/openid-configuration',
      ['headers' => ['user-agent' => '@logto/php', 'accept' => '*/*']]
    )->getBody()->getContents();

    return new OidcCore(new OidcProviderMetadata(...json_decode($body, true)));
  }

  /** Generate a random string (32 bytes) for the state parameter. */
  static function generateState(): string
  {
    return JWT::urlsafeB64Encode(random_bytes(32));
  }

  /**
   * Generate a random code verifier string (32 bytes) for PKCE.
   * 
   * See [Client Creates a Code Verifier](https://www.rfc-editor.org/rfc/rfc7636.html#section-4.1) to learn more.
   */
  static function generateCodeVerifier(): string
  {
    return JWT::urlsafeB64Encode(random_bytes(32));
  }

  /**
   * Generate a code challenge string for the given code verifier string.
   * 
   * See [Client Creates the Code Challenge](https://www.rfc-editor.org/rfc/rfc7636.html#section-4.2) to learn more.
   */
  static function generateCodeChallenge(string $codeVerifier): string
  {
    return JWT::urlsafeB64Encode(hash('sha256', $codeVerifier, true));
  }

  // ==================== End of static members ====================

  protected CachedKeySet $jwkSet;

  /**
   * Initialize the OIDC core with the provider metadata. You can use the
   * static create method to create an instance for the given Logto endpoint.
   * 
   * @see OidcCore::create()
   */
  public function __construct(public OidcProviderMetadata $metadata, protected Client $client = new Client())
  {
    $this->jwkSet = new CachedKeySet(
      $this->metadata->jwks_uri,
      $client,
      new HttpFactory(),
      CacheManager::getInstance('files'),
      300,
      true
    );
  }

  /**
   * Verify the ID Token signature and its issuer and client ID, throw an exception
   * if the verification fails.
   */
  public function verifyIdToken(string $idToken, string $clientId)
  {
    $assertions = [
      'aud' => $clientId,
      'iss' => $this->metadata->issuer,
    ];
    $decoded = JWT::decode(
      $idToken,
      $this->jwkSet,
    );

    foreach ($assertions as $key => $value) {
      if ($decoded->$key !== $value) {
        throw new LogtoException("Invalid ID token: $key is not $value.");
      }
    }
  }

  /** Fetch the token from the token endpoint using the authorization code. */
  public function fetchTokenByCode(string $clientId, ?string $clientSecret, string $redirectUri, string $code, string $codeVerifier): TokenResponse
  {
    $response = $this->client->post($this->metadata->token_endpoint, [
      'form_params' => [
        'grant_type' => 'authorization_code',
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri' => $redirectUri,
        'code' => $code,
        'code_verifier' => $codeVerifier,
      ],
    ])->getBody()->getContents();
    return new TokenResponse(...json_decode($response, true));
  }

  /** Fetch the token for the given resource from the token endpoint using the refresh token. */
  public function fetchTokenByRefreshToken(string $clientId, ?string $clientSecret, string $refreshToken, string $resource = ''): TokenResponse
  {
    $response = $this->client->post($this->metadata->token_endpoint, [
      'form_params' => [
        'grant_type' => 'refresh_token',
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'refresh_token' => $refreshToken,
        'resource' => $resource ?: null,
      ],
    ])->getBody()->getContents();
    return new TokenResponse(...json_decode($response, true));
  }

  /** 
   * Fetch the user info from the OpenID Connect UserInfo endpoint. 
   * 
   * @see [UserInfo Endpoint](https://openid.net/specs/openid-connect-core-1_0.html#UserInfo]
   */
  public function fetchUserInfo(string $accessToken): UserInfoResponse
  {
    $userInfoEndpoint = $this->metadata->userinfo_endpoint;
    $response = $this->client->get($userInfoEndpoint, [
      'headers' => [
        'Authorization' => "Bearer $accessToken",
      ]
    ])->getBody()->getContents();
    return new UserInfoResponse(...json_decode($response, true));
  }
}
