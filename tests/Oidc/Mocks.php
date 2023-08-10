<?php declare(strict_types=1);
use Logto\Sdk\Models\OidcProviderMetadata;
use GuzzleHttp\Psr7\Response;
use Logto\Sdk\Oidc\OidcCore;
use Firebase\JWT\CachedKeySet;
use Firebase\JWT\Key;
use Firebase\JWT\JWK;

class MockOidcCore extends OidcCore
{
  static function generateState(): string
  {
    return 'state';
  }

  static function generateCodeVerifier(): string
  {
    return 'codeVerifier';
  }

  static function generateCodeChallenge(string $codeVerifier): string
  {
    return 'codeChallenge';
  }

  // Promote visibility for testing
  public CachedKeySet $jwkSet;
}

class MockKeySet extends CachedKeySet
{
  static $signedIdToken = "eyJhbGciOiJFUzM4NCIsImtpZCI6IjEiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2xvZ3RvLmFwcCIsInN1YiI6InVzZXIxIiwiYXVkIjoiZm9vIiwiZXhwIjo5NjE2NDQ2NDAwLCJpYXQiOjE2MTY0NDYzMDAsImF0X2hhc2giOm51bGwsIm5hbWUiOiJKb2huIFdpY2siLCJ1c2VybmFtZSI6ImpvaG4iLCJwaWN0dXJlIjpudWxsLCJlbWFpbCI6ImpvaG5Ad2ljay5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwicGhvbmVfbnVtYmVyIjpudWxsLCJwaG9uZV9udW1iZXJfdmVyaWZpZWQiOm51bGx9.rBJDg6L2eenDRUVVPW7LwrqYPEU0a8DZA0EE12BR0OAYUCLJNkG_GK_o2JltReJ4zbPOE9fHXRqU_eni_IeMGB3QTYTQ954QlPAw3fJ6f_XQCC9sLfCiQE5KYMN-M6HX";
  function __construct()
  {
  }

  function offsetGet($keyId): Key
  {
    return JWK::parseKey([
      "kty" => "EC",
      "use" => "sig",
      "crv" => "P-384",
      "kid" => "1",
      "x" => "GWEhvHiHu2nfZNn741QeWPyn3Laphn11wcD9c5LWqPQTaqw-SlJIWXavrvl4Yv7f",
      "y" => "0KiYwX8U2pb74HCRby6ljlNgQGD-v_j5QN-MzXObRYa7XRQzKCrqj0_4BZN6UcS6",
      "alg" => "ES384",
    ]);
  }
}


class Mocks
{
  static function getOidcProviderMetadata(): OidcProviderMetadata
  {
    return new OidcProviderMetadata(
      issuer: "https://logto.app",
      authorization_endpoint: "https://logto.app/oidc/auth",
      token_endpoint: "https://logto.app/oidc/auth/token",
      userinfo_endpoint: "https://logto.app/oidc/userinfo",
      jwks_uri: "https://logto.app/oidc/jwks",
      end_session_endpoint: "https://logto.app/oidc/logout",
      response_types_supported: [],
      subject_types_supported: [],
      id_token_signing_alg_values_supported: [],
    );
  }

  static function mockResponse(object|array $body, int $status = 200, array $headers = ['is-testing' => 'true']): Response
  {
    return new Response(
      $status,
      $headers,
      json_encode($body)
    );
  }
}
