<?php declare(strict_types=1);
namespace Logto\Sdk\Oidc;

use Logto\Sdk\Models\JsonModel;

/** 
 * The response model from the token endpoint. 
 * @see [Token Endpoint](https://openid.net/specs/openid-connect-core-1_0.html#TokenEndpoint) to learn more.
 */
class TokenResponse extends JsonModel
{
  public function __construct(
    /** The access token string. */
    public string $access_token,
    /** The token type string, should be "Bearer". */
    public string $token_type,
    /** The expiration time of the access token (in seconds). */
    public int $expires_in,
    /** The refresh token string. */
    public ?string $refresh_token = null,
    /** The ID token string. */
    public ?string $id_token = null,
    ...$extra
  ) {
    $this->extra = $extra;
  }
}
