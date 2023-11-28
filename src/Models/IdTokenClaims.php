<?php declare(strict_types=1);
namespace Logto\Sdk\Models;

/** The ID token claims object. */
class IdTokenClaims extends JsonModel
{
  public function __construct(
    /** The issuer identifier for whom issued the token. */
    public string $iss,
    /** The subject identifier for whom the token is intended (user ID). */
    public string $sub,
    /** The audience that the token is intended for, which is the client ID. */
    public string $aud,
    /** The expiration time of the token (in seconds). */
    public int $exp,
    /** The time at which the token was issued (in seconds). */
    public int $iat,
    public ?string $at_hash = null,
    /** The user's full name. */
    public ?string $name = null,
    /** The user's username. */
    public ?string $username = null,
    /** The user's profile picture URL. */
    public ?string $picture = null,
    /** The user's email address. */
    public ?string $email = null,
    /** Whether the email address is verified. */
    public ?bool $email_verified = null,
    /** The user's phone number. */
    public ?string $phone_number = null,
    /** Whether the phone number is verified. */
    public ?bool $phone_number_verified = null,
    /** The user's roles. */
    public ?array $roles = null,
    /** The user's organization IDs. */
    public ?array $organizations = null,
    /** The user's organization roles. */
    public ?array $organization_roles = null,
    ...$extra
  ) {
    $this->extra = $extra;
  }
}
