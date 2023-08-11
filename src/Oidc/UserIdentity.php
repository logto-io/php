<?php declare(strict_types=1);
namespace Logto\Sdk\Oidc;

use Logto\Sdk\Models\JsonModel;

/** The user identity model. */
class UserIdentity extends JsonModel
{
  public function __construct(
    /** The user ID of the target identity. */
    public string $userId,
    /** The details of the target identity, can be any JSON object. */
    public ?array $details = null,
    ...$extra,
  ) {
    $this->extra = $extra;
  }
}
