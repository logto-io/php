<?php declare(strict_types=1);
namespace Logto\Sdk\Oidc;

use Firebase\JWT\CachedKeySet;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Client;
use Logto\Sdk\LogtoException;
use Logto\Sdk\Constants\ReservedScope;
use Logto\Sdk\Constants\UserScope;
use Logto\Sdk\Models\OidcProviderMetadata;
use Phpfastcache\CacheManager;
use Firebase\JWT\JWT;
use Phpfastcache\Helper\Psr16Adapter;

/**
 * The core OIDC functions for the Logto client. Provider-agonistic functions
 * are implemented as static methods, while other functions are implemented as
 * instance methods.
 */
class OidcCore
{
  public const DEFAULT_SCOPES = [ReservedScope::openId, ReservedScope::offlineAccess, UserScope::profile];
  public const ORGANIZATION_URN_PREFIX = 'urn:logto:organization:';

  /**
   * Create a OidcCore instance for the given Logto endpoint using the discovery URL.
   * 
   * Note it may take a while to fetch the metadata from the endpoint for the first time.
   * After that, the metadata will be cached for 1 hour.
   */
  static function create(string $logtoEndpoint): OidcCore
  {
    $defaultDriver = 'Files';
    $Psr16Adapter = new Psr16Adapter($defaultDriver);
    $client = new Client();
    $cacheKey = 'logto_oidc_metadata.' . urlencode($logtoEndpoint);

    if ($metadata = $Psr16Adapter->get($cacheKey)) {
      return new OidcCore(new OidcProviderMetadata(...$metadata));
    }

    $body = $client->get(
      $logtoEndpoint . '/oidc/.well-known/openid-configuration',
      ['headers' => ['user-agent' => '@logto/php', 'accept' => '*/*']]
    )->getBody()->getContents();
    $metadata = json_decode($body, true);
    $Psr16Adapter->set($cacheKey, $metadata, 3600);

    return new OidcCore(new OidcProviderMetadata(...$metadata));
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

  /**
   * Build the organization URN for the given organization ID.
   * 
   * For example, if the organization ID is `123`, the organization URN will be
   * `urn:logto:organization:123`.
   * 
   * @see [RFC 0001](https://github.com/logto-io/rfcs) to learn more.
   */
  static function buildOrganizationUrn(string $organizationId): string
  {
    return self::ORGANIZATION_URN_PREFIX . $organizationId;
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

  /** 
   * Fetch the token for the given resource from the token endpoint using the refresh token.
   * 
   * If the resource is an organization URN, the organization ID will be extracted from
   * the URN and the `organization_id` parameter will be sent to the token endpoint.
   */
  public function fetchTokenByRefreshToken(string $clientId, ?string $clientSecret, string $refreshToken, string $resource = ''): TokenResponse
  {
    $isOrganizationResource = str_starts_with($resource, self::ORGANIZATION_URN_PREFIX);
    $response = $this->client->post($this->metadata->token_endpoint, [
      'form_params' => [
        'grant_type' => 'refresh_token',
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'refresh_token' => $refreshToken,
        'resource' => $isOrganizationResource ? null : ($resource ?: null),
        'organization_id' => $isOrganizationResource ? substr($resource, strlen(self::ORGANIZATION_URN_PREFIX)) : null,
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
