<?php declare(strict_types=1);
namespace Logto\Sdk\Oidc;

use Logto\Sdk\Models\JsonModel;

class UserIdentity extends JsonModel
{
  public function __construct(
    public string $userId,
    public ?array $details = null,
    ...$extra,
  ) {
    $this->extra = $extra;
  }
}
