<?php declare(strict_types=1);
namespace Logto\Sdk\Models;

class AccessTokenClaims extends JsonModel
{
  public function __construct(
    public string $iss,
    public string $sub,
    public string $aud,
    public int $exp,
    public int $iat,
    public string $scope,
    public ?string $client_id = null,
    ...$extra
  ) {
    $this->extra = $extra;
  }
}
