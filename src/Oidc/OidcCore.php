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

class OidcCore
{
  public const DEFAULT_SCOPES = ['openid', 'offline_access', 'profile'];

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
    return Utilities::urlSafeEncode(random_bytes(32));
  }

  /**
   * Generate a random code verifier string (32 bytes) for PKCE.
   * 
   * See {@link https://www.rfc-editor.org/rfc/rfc7636.html#section-4.1 Client Creates a Code Verifier} to learn more.
   */
  static function generateCodeVerifier(): string
  {
    return Utilities::urlSafeEncode(random_bytes(32));
  }

  /**
   * Generate a code challenge string for the given code verifier string.
   * 
   * See {@link https://www.rfc-editor.org/rfc/rfc7636.html#section-4.2 Client Creates the Code Challenge} to learn more.
   */
  static function generateCodeChallenge(string $codeVerifier): string
  {
    return Utilities::urlSafeEncode(hash('sha256', $codeVerifier, true));
  }

  // ==================== End of static members ====================

  protected CachedKeySet $jwkSet;

  public function __construct(public OidcProviderMetadata $metadata)
  {
    $this->jwkSet = new CachedKeySet(
      $this->metadata->jwks_uri,
      new Client(),
      new HttpFactory(),
      CacheManager::getInstance('files'),
      300,
      true
    );
  }

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

  public function fetchTokenByCode(string $clientId, ?string $clientSecret, string $redirectUri, string $code, string $codeVerifier): TokenResponse
  {
    $client = new Client();
    $response = $client->post($this->metadata->token_endpoint, [
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
    $client = new Client();
    $response = $client->post($this->metadata->token_endpoint, [
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

  /** Fetch the user info from the OpenID Connect UserInfo endpoint. */
  public function fetchUserInfo(string $accessToken): UserInfoResponse
  {
    $userInfoEndpoint = $this->metadata->userinfo_endpoint;
    $client = new Client();
    $response = $client->get($userInfoEndpoint, [
      'headers' => [
        'Authorization' => "Bearer $accessToken",
      ]
    ])->getBody()->getContents();
    return new UserInfoResponse(...json_decode($response, true));
  }
}
