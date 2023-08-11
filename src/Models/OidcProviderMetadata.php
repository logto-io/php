<?php declare(strict_types=1);
namespace Logto\Sdk\Models;

/**
 * The OpenID Connect Discovery response object.
 * 
 * @see [OpenID Provider Metadata](https://openid.net/specs/openid-connect-discovery-1_0.html#ProviderMetadata) to learn more.
 */
class OidcProviderMetadata extends JsonModel
{
  public function __construct(
    public string $issuer,
    public string $authorization_endpoint,
    public string $token_endpoint,
    public string $userinfo_endpoint,
    // This is actually "RECOMMENDED" but Logto requires it
    public string $jwks_uri,
    public ?string $registration_endpoint = null,
    public array $scopes_supported = [],
    public array $response_types_supported = [],
    public array $response_modes_supported = [],
    public array $grant_types_supported = [],
    public array $acr_values_supported = [],
    public array $subject_types_supported = [],
    public array $id_token_signing_alg_values_supported = [],
    public array $id_token_encryption_alg_values_supported = [],
    public array $id_token_encryption_enc_values_supported = [],
    public array $userinfo_signing_alg_values_supported = [],
    public array $userinfo_encryption_alg_values_supported = [],
    public array $userinfo_encryption_enc_values_supported = [],
    public array $request_object_signing_alg_values_supported = [],
    public array $request_object_encryption_alg_values_supported = [],
    public array $request_object_encryption_enc_values_supported = [],
    public array $token_endpoint_auth_methods_supported = [],
    public array $token_endpoint_auth_signing_alg_values_supported = [],
    public array $display_values_supported = [],
    public array $claim_types_supported = [],
    public array $claims_supported = [],
    public ?string $service_documentation = null,
    public array $claims_locales_supported = [],
    public array $ui_locales_supported = [],
    public bool $claims_parameter_supported = false,
    public bool $request_parameter_supported = false,
    public bool $request_uri_parameter_supported = true,
    public bool $require_request_uri_registration = false,
    public ?string $op_policy_uri = null,
    public ?string $op_tos_uri = null,
    public ?string $end_session_endpoint = null,
    public array $code_challenge_methods_supported = [],
    ...$extra
  ) {
    $this->extra = $extra;
  }
}
