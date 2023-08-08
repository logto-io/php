<?php declare(strict_types=1);
namespace Logto\Sdk\Models;

class IdTokenClaims
{
  public $extra;

  public function __construct(
    public string $iss,
    public string $sub,
    public string $aud,
    public int $exp,
    public int $iat,
    public ?string $at_hash = null,
    public ?string $name = null,
    public ?string $username = null,
    public ?string $picture = null,
    public ?string $email = null,
    public ?bool $email_verified = null,
    public ?string $phone_number = null,
    public ?bool $phone_number_verified = null,
    ...$extra
  ) {
    $this->extra = $extra;
  }
}
