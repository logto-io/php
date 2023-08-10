<?php declare(strict_types=1);
namespace Logto\Sdk\Oidc;

use Logto\Sdk\Models\JsonModel;

class TokenResponse extends JsonModel
{
  public function __construct(
    public string $access_token,
    public string $token_type,
    public int $expires_in,
    public ?string $refresh_token = null,
    public ?string $id_token = null,
    ...$extra
  ) {
    $this->extra = $extra;
  }
}
