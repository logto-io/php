<?php declare(strict_types=1);
namespace Logto\Sdk\Models;

/** The access token claims object. */
class AccessTokenClaims extends JsonModel
{
  public function __construct(
    /** The issuer identifier for whom issued the token. */
    public string $iss,
    /** The subject identifier for whom the token is intended (user ID). */
    public string $sub,
    /**
     * The audience that the token is intended for, which is the client ID or the resource
     * indicator.
     */
    public string $aud,
    /** The expiration time of the token (in seconds). */
    public int $exp,
    /** The time at which the token was issued (in seconds). */
    public int $iat,
    /** The scopes that the token is granted for. */
    public string $scope,
    /**
     * The client ID that the token is granted for. Useful when the client ID is not
     * included in the `aud` claim.
     */
    public ?string $client_id = null,
    ...$extra
  ) {
    $this->extra = $extra;
  }
}
