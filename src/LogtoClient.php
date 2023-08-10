<?php declare(strict_types=1);
namespace Logto\Sdk;

use Logto\Sdk\Models\AccessTokenClaims;
use Logto\Sdk\Models\IdTokenClaims;
use Logto\Sdk\Oidc\OidcCore;
use Logto\Sdk\Oidc\TokenResponse;
use Logto\Sdk\Oidc\UserInfoResponse;
use Logto\Sdk\Storage\SessionStorage;
use Logto\Sdk\Storage\Storage;
use Logto\Sdk\Storage\StorageKey;

class SignInSession
{
  function __construct(
    public string $redirectUri,
    public string $codeVerifier,
    public string $state,
  ) {
  }
}

class AccessToken
{
  function __construct(
    public string $token,
    public int $expiresAt,
  ) {
  }
}

enum InteractionMode: string
{
  case signUp = 'signUp';
  case signIn = 'signIn';
}

class LogtoClient
{
  protected OidcCore $oidcCore;

  function __construct(public ClientConfig $config, public Storage $storage = new SessionStorage())
  {
    $this->oidcCore = OidcCore::create($config->endpoint);
  }

  function isAuthenticated(): bool
  {
    return boolval($this->storage->get(StorageKey::idToken));
  }

  function getIdToken(): ?string
  {
    return $this->storage->get(StorageKey::idToken);
  }

  function getIdTokenClaims(): IdTokenClaims
  {
    return new IdTokenClaims(...json_decode(base64_decode(explode('.', $this->getIdToken())[1]), true));
  }

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

  function getAccessTokenClaims(string $resource = ''): AccessTokenClaims
  {
    return new AccessTokenClaims(...json_decode(base64_decode(explode('.', $this->getAccessToken($resource))[1]), true));
  }

  function getRefreshToken(): ?string
  {
    return $this->storage->get(StorageKey::refreshToken);
  }

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

  public function handleSignInCallback(): void
  {
    $signInSession = $this->getSignInSession();

    if (!$signInSession) {
      throw new LogtoException('Sign-in session not found.');
    }

    // Some loose checks
    if (
      parse_url($signInSession->redirectUri, PHP_URL_HOST) !== ($_SERVER['SERVER_NAME'] ?? null) ||
      parse_url($signInSession->redirectUri, PHP_URL_PATH) !== ($_SERVER['PATH_INFO'] ?? null)
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
    $config = $this->config;
    $query = http_build_query([
      'client_id' => $config->appId,
      'redirect_uri' => $redirectUri,
      'response_type' => 'code',
      'scope' => implode(' ', array_merge($config->scopes ?: [], $this->oidcCore::DEFAULT_SCOPES)),
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
        $config->resources ?
        # Resources need to use the same key name as the query string
        '&' . implode('&', array_map(fn($resource) => "resource=" . urlencode($resource), $config->resources)) :
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
