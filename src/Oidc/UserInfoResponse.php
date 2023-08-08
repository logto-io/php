<?php declare(strict_types=1);
namespace Logto\Sdk\Oidc;

class UserInfoResponse
{
  public $extra;
  public function __construct(
    public string $sub,
    public ?string $name = null,
    public ?string $username = null,
    public ?string $picture = null,
    public ?string $email = null,
    public ?bool $email_verified = null,
    public ?string $phone_number = null,
    public ?bool $phone_number_verified = null,
    public mixed $custom_data = null,
    public ?array $identities = null,
    ...$extra,
  ) {
    $this->extra = $extra;
  }
}
