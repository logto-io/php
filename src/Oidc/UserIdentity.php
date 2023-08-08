<?php declare(strict_types=1);
namespace Logto\Sdk\Oidc;

class UserIdentity
{
  public $extra;
  public function __construct(
    public string $userId,
    public ?array $details = null,
    ...$extra,
  ) {
    $this->extra = $extra;
  }
}
