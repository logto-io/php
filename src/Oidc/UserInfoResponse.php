<?php declare(strict_types=1);
namespace Logto\Sdk\Oidc;

use Logto\Sdk\Models\JsonModel;

/** The response model from the user info endpoint. */
class UserInfoResponse extends JsonModel
{
  public function __construct(
    /** The subject identifier for whom the token is intended (user ID). */
    public string $sub,
    /** The full name of the user. */
    public ?string $name = null,
    /** The username of the user. */
    public ?string $username = null,
    /** The profile picture URL of the user. */
    public ?string $picture = null,
    /** The email address of the user. */
    public ?string $email = null,
    /** Whether the email address is verified. */
    public ?bool $email_verified = null,
    /** The phone number of the user. */
    public ?string $phone_number = null,
    /** Whether the phone number is verified. */
    public ?bool $phone_number_verified = null,
    /** The user's roles. */
    public ?array $roles = null,
    /** The user's organization IDs. */
    public ?array $organizations = null,
    /** The user's organization roles. */
    public ?array $organization_roles = null,
    /** The custom data of the user, can be any JSON object. */
    public mixed $custom_data = null,
    /**
     * The identities of the user, can be a dictionary of key-value pairs, where the key is
     * the identity type and the value is the `UserIdentity` object.
     */
    public ?array $identities = null,
    ...$extra,
  ) {
    $this->extra = $extra;
  }
}
