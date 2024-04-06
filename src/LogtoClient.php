<?php declare(strict_types=1);
namespace Logto\Sdk;

use Logto\Sdk\Constants\ReservedResource;
use Logto\Sdk\Models\AccessTokenClaims;
use Logto\Sdk\Models\IdTokenClaims;
use Logto\Sdk\Oidc\OidcCore;
use Logto\Sdk\Oidc\TokenResponse;
use Logto\Sdk\Oidc\UserInfoResponse;
use Logto\Sdk\Storage\SessionStorage;
use Logto\Sdk\Storage\Storage;
use Logto\Sdk\Storage\StorageKey;

/**
 * The sign-in session that stores the information for the sign-in callback.
 * Should be stored before redirecting the user to Logto.
 */
class SignInSession
{
  function __construct(
    /** The redirect URI for the current sign-in session. */
    public string $redirectUri,
    /** The code verifier of Proof Key for Code Exchange (PKCE). */
    public string $codeVerifier,
    /** The state for OAuth 2.0 authorization request. */
    public string $state,
  ) {
  }
}

/** The access token class for a resource. */
class AccessToken
{
  function __construct(
    /** The access token string. */
    public string $token,
    /**
     * The timestamp (in seconds) when the access token will expire.
     * Note this is not the expiration time of the access token itself, but the
     * expiration time of the access token cache.
     */
    public int $expiresAt,
  ) {
  }
}

/**
 * The interaction mode for the sign-in request. Note this is not a part of the OIDC
 * specification, but a Logto extension.
 */
enum InteractionMode: string
{
  case signUp = 'signUp';
  case signIn = 'signIn';
}

/**
 * The main class of the Logto client. You should create an instance of this class
 * and use it to sign in, sign out, get access token, etc.
 */
class LogtoClient
{
  protected OidcCore $oidcCore;

  function __construct(public LogtoConfig $config, public Storage $storage = new SessionStorage())
  {
    $this->oidcCore = OidcCore::create(rtrim($config->endpoint, "/"));
  }

  /**
   * Check if the user is authenticated by checking if the ID Token exists.
   */
  function isAuthenticated(): bool
  {
    return boolval($this->storage->get(StorageKey::idToken));
  }

  /**
   * Get the ID Token string. If you need to get the claims in the ID Token, use
   * `getIdTokenClaims` instead.
   */
  function getIdToken(): ?string
  {
    return $this->storage->get(StorageKey::idToken);
  }

  /**
   * Get the claims in the ID Token. If the ID Token does not exist, an exception
   * will be thrown.
   */
  function getIdTokenClaims(): IdTokenClaims
  {
    return new IdTokenClaims(...json_decode(base64_decode(explode('.', $this->getIdToken())[1]), true));
  }

  /**
   * Get the access token for the given resource. If the access token is expired,
   * it will be refreshed automatically. If no refresh token is found, null will
   * be returned.
   */
  function getAccessToken(string $resource = ''): ?string
  {
    $accessToken = $this->_getAccessToken($resource);

    if ($accessToken) {
      return $accessToken;
    }

    $refreshToken = $this->storage->get(StorageKey::refreshToken);

    if (!$refreshToken) {
      return null;
    }

    $tokenResponse = $this->oidcCore->fetchTokenByRefreshToken(
      clientId: $this->config->appId,
      clientSecret: $this->config->appSecret,
      refreshToken: $refreshToken,
      resource: $resource,
    );

    $this->handleTokenResponse($resource, $tokenResponse);
    return $tokenResponse->access_token;
  }

  /**
   * Get the access token for the given organization ID. If the access token is
   * expired, it will be refreshed automatically. If no refresh token is found,
   * null will be returned.
   */
  function getOrganizationToken(string $organizationId): ?string
  {
    return $this->getAccessToken(OidcCore::buildOrganizationUrn($organizationId));
  }

  /**
   * Get the claims in the access token for the given resource. If the access token
   * is expired, it will be refreshed automatically. If it's unable to refresh the
   * access token, an exception will be thrown.
   */
  function getAccessTokenClaims(string $resource = ''): AccessTokenClaims
  {
    return new AccessTokenClaims(...json_decode(base64_decode(explode('.', $this->getAccessToken($resource))[1]), true));
  }

  /**
   * Get the claims in the access token for the given organization ID. If the access
   * token is expired, it will be refreshed automatically. If it's unable to refresh
   * the access token, an exception will be thrown.
   */
  function getOrganizationTokenClaims(string $organizationId): AccessTokenClaims
  {
    return $this->getAccessTokenClaims(OidcCore::buildOrganizationUrn($organizationId));
  }

  /**
   * Get the refresh token string.
   */
  function getRefreshToken(): ?string
  {
    return $this->storage->get(StorageKey::refreshToken);
  }

  /**
   * Returns the sign-in URL for the given redirect URI. You should redirect the user
   * to the returned URL to sign in.
   *
   * By specifying the interaction mode, you can control whether the user will be
   * prompted for sign-in or sign-up on the first screen. If the interaction mode is
   * not specified, the default one will be used.
   *
   * @example
   * ```php
   * header('Location: ' . $client->signIn("https://example.com/callback"));
   * ```
   */
  function signIn(string $redirectUri, ?InteractionMode $interactionMode = null): string
  {
    $codeVerifier = $this->oidcCore::generateCodeVerifier();
    $codeChallenge = $this->oidcCore::generateCodeChallenge($codeVerifier);
    $state = $this->oidcCore::generateState();
    $signInUrl = $this->buildSignInUrl($redirectUri, $codeChallenge, $state, $interactionMode);

    foreach (StorageKey::cases() as $key) {
      $this->storage->delete($key);
    }

    $this->setSignInSession(
      new SignInSession(
        redirectUri: $redirectUri,
        codeVerifier: $codeVerifier,
        state: $state,
      )
    );

    return $signInUrl;
  }

  /**
   * Returns the sign-out URL for the given post-logout redirect URI. You should
   * redirect the user to the returned URL to sign out.
   *
   * If the post-logout redirect URI is not provided, the Logto default post-logout
   * redirect URI will be used.
   *
   * Note:
   *   If the OpenID Connect server does not support the end session endpoint
   *   (i.e. OpenID Connect RP-Initiated Logout), the function will throw an
   *   exception. Logto supports the end session endpoint.
   *
   * Example:
   * ```php
   * header('Location: ' . $client->signIn("https://example.com/"));
   * ```
   */
  function signOut(?string $postLogoutRedirectUri = null): string
  {
    $this->storage->delete(StorageKey::idToken);
    $this->storage->delete(StorageKey::refreshToken);
    $this->storage->delete(StorageKey::accessTokenMap);

    $query = http_build_query([
      'client_id' => $this->config->appId,
      'post_logout_redirect_uri' => $postLogoutRedirectUri,
    ]);

    $end_session_endpoint = $this->oidcCore->metadata->end_session_endpoint;

    if (!$end_session_endpoint) {
      throw new LogtoException('End session endpoint not found.');
    }

    return $end_session_endpoint . '?' . $query;
  }

  /**
   * Handle the sign-in callback from the Logto server. This method should be called
   * in the callback route handler of your application.
   */
  public function handleSignInCallback(): void
  {
    $signInSession = $this->getSignInSession();

    if (!$signInSession) {
      throw new LogtoException('Sign-in session not found.');
    }

    // Some loose checks
    if (
      parse_url($signInSession->redirectUri, PHP_URL_HOST) !== ($_SERVER['SERVER_NAME'] ?? null) ||
      parse_url($signInSession->redirectUri, PHP_URL_PATH) !== parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
    ) {
      throw new LogtoException('The redirect URI in the sign-in session does not match the current request.');
    }

    $params = [];

    if (!empty($_SERVER['QUERY_STRING'])) {
      parse_str($_SERVER['QUERY_STRING'], $params);
    }

    if (($params['state'] ?? '') !== $signInSession->state) {
      throw new LogtoException('State mismatch.');
    }

    if (!($params['code'] ?? null)) {
      throw new LogtoException('Code not found.');
    }

    $config = $this->config;
    $tokenResponse = $this->oidcCore->fetchTokenByCode(
      clientId: $config->appId,
      clientSecret: $config->appSecret,
      redirectUri: $signInSession->redirectUri,
      code: $params['code'],
      codeVerifier: $signInSession->codeVerifier,
    );

    $this->handleTokenResponse('', $tokenResponse);
    $this->storage->delete(StorageKey::signInSession);
  }

  /**
   * Fetch the user information from the UserInfo endpoint. If the access token
   * is expired, it will be refreshed automatically.
   */
  public function fetchUserInfo(): UserInfoResponse
  {
    $accessToken = $this->getAccessToken();
    if (!$accessToken) {
      throw new LogtoException('Access token not found.');
    }
    return $this->oidcCore->fetchUserInfo($accessToken);
  }

  protected function buildSignInUrl(string $redirectUri, string $codeChallenge, string $state, ?InteractionMode $interactionMode): string
  {
    $pickValue = function (string|\BackedEnum $value): string {
      return $value instanceof \BackedEnum ? $value->value : $value;
    };
    $config = $this->config;
    $scopes = array_unique(
      array_map($pickValue, array_merge($config->scopes ?: [], $this->oidcCore::DEFAULT_SCOPES))
    );
    $resources = array_unique(
      $config->hasOrganizationScope()
      ? array_merge($config->resources ?: [], [ReservedResource::organizations->value])
      : ($config->resources ?: [])
    );

    $query = http_build_query([
      'client_id' => $config->appId,
      'redirect_uri' => $redirectUri,
      'response_type' => 'code',
      'scope' => implode(' ', $scopes),
      'prompt' => $config->prompt->value,
      'code_challenge' => $codeChallenge,
      'code_challenge_method' => 'S256',
      'state' => $state,
      'interaction_mode' => $interactionMode?->value,
    ]);

    return $this->oidcCore->metadata->authorization_endpoint .
      '?' .
      $query .
      (
        count($resources) > 0 ?
        # Resources need to use the same key name as the query string
        '&' . implode('&', array_map(fn($resource) => "resource=" . urlencode($resource), $resources)) :
        ''
      );
  }

  protected function setSignInSession(SignInSession $data): void
  {
    $this->storage->set(StorageKey::signInSession, json_encode($data));
  }

  protected function getSignInSession(): ?SignInSession
  {
    $data = $this->storage->get(StorageKey::signInSession);
    return $data ? new SignInSession(...json_decode($data, true)) : null;
  }

  protected function handleTokenResponse(string $resource, TokenResponse $tokenResponse): void
  {
    if ($tokenResponse->id_token) {
      $this->oidcCore->verifyIdToken($tokenResponse->id_token, $this->config->appId);
      $this->storage->set(StorageKey::idToken, $tokenResponse->id_token);
    }

    if ($tokenResponse->refresh_token) {
      $this->storage->set(StorageKey::refreshToken, $tokenResponse->refresh_token);
    }

    $this->_setAccessToken($resource, $tokenResponse->access_token, $tokenResponse->expires_in);
  }

  /** Return the raw array that stores the access token map in the storage. */
  protected function _getAccessTokenMap(): array
  {
    return json_decode($this->storage->get(StorageKey::accessTokenMap) ?: '{}', true);
  }

  protected function _setAccessToken(string $resource, string $accessToken, int $expiresIn): void
  {
    $accessTokenMap = $this->_getAccessTokenMap();
    $accessTokenMap[$resource] = new AccessToken(
      token: $accessToken,
      expiresAt: time() + $expiresIn - 60, # 60 seconds earlier to avoid clock skew
    );
    $this->storage->set(StorageKey::accessTokenMap, json_encode($accessTokenMap));
  }

  /** Get the valid access token for the given resource from storage, no refresh will be performed. */
  protected function _getAccessToken(string $resource): ?string
  {
    $accessTokenMap = $this->_getAccessTokenMap();

    if (!isset($accessTokenMap[$resource])) {
      return null;
    }

    try {
      $instance = new AccessToken(...$accessTokenMap[$resource]);
      if ($instance->expiresAt < time()) {
        return null;
      }
      return $instance->token;
    } catch (\Throwable) {
      return null;
    }
  }
}
