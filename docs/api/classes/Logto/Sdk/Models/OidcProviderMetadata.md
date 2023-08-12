---

# OidcProviderMetadata

The OpenID Connect Discovery response object.

- Full name: `\Logto\Sdk\Models\OidcProviderMetadata`
- Parent class: [`\Logto\Sdk\Models\JsonModel`](./JsonModel.md)

**See Also:**

- - [OpenID Provider Metadata](https://openid.net/specs/openid-connect-discovery-1_0.html#ProviderMetadata) to learn more.

## Properties

### issuer

```php
public string $issuer
```

---

### authorization_endpoint

```php
public string $authorization_endpoint
```

---

### token_endpoint

```php
public string $token_endpoint
```

---

### userinfo_endpoint

```php
public string $userinfo_endpoint
```

---

### jwks_uri

```php
public string $jwks_uri
```

---

### registration_endpoint

```php
public ?string $registration_endpoint
```

---

### scopes_supported

```php
public array $scopes_supported
```

---

### response_types_supported

```php
public array $response_types_supported
```

---

### response_modes_supported

```php
public array $response_modes_supported
```

---

### grant_types_supported

```php
public array $grant_types_supported
```

---

### acr_values_supported

```php
public array $acr_values_supported
```

---

### subject_types_supported

```php
public array $subject_types_supported
```

---

### id_token_signing_alg_values_supported

```php
public array $id_token_signing_alg_values_supported
```

---

### id_token_encryption_alg_values_supported

```php
public array $id_token_encryption_alg_values_supported
```

---

### id_token_encryption_enc_values_supported

```php
public array $id_token_encryption_enc_values_supported
```

---

### userinfo_signing_alg_values_supported

```php
public array $userinfo_signing_alg_values_supported
```

---

### userinfo_encryption_alg_values_supported

```php
public array $userinfo_encryption_alg_values_supported
```

---

### userinfo_encryption_enc_values_supported

```php
public array $userinfo_encryption_enc_values_supported
```

---

### request_object_signing_alg_values_supported

```php
public array $request_object_signing_alg_values_supported
```

---

### request_object_encryption_alg_values_supported

```php
public array $request_object_encryption_alg_values_supported
```

---

### request_object_encryption_enc_values_supported

```php
public array $request_object_encryption_enc_values_supported
```

---

### token_endpoint_auth_methods_supported

```php
public array $token_endpoint_auth_methods_supported
```

---

### token_endpoint_auth_signing_alg_values_supported

```php
public array $token_endpoint_auth_signing_alg_values_supported
```

---

### display_values_supported

```php
public array $display_values_supported
```

---

### claim_types_supported

```php
public array $claim_types_supported
```

---

### claims_supported

```php
public array $claims_supported
```

---

### service_documentation

```php
public ?string $service_documentation
```

---

### claims_locales_supported

```php
public array $claims_locales_supported
```

---

### ui_locales_supported

```php
public array $ui_locales_supported
```

---

### claims_parameter_supported

```php
public bool $claims_parameter_supported
```

---

### request_parameter_supported

```php
public bool $request_parameter_supported
```

---

### request_uri_parameter_supported

```php
public bool $request_uri_parameter_supported
```

---

### require_request_uri_registration

```php
public bool $require_request_uri_registration
```

---

### op_policy_uri

```php
public ?string $op_policy_uri
```

---

### op_tos_uri

```php
public ?string $op_tos_uri
```

---

### end_session_endpoint

```php
public ?string $end_session_endpoint
```

---

### code_challenge_methods_supported

```php
public array $code_challenge_methods_supported
```

---

## Methods

### \_\_construct

```php
public __construct(string $issuer, string $authorization_endpoint, string $token_endpoint, string $userinfo_endpoint, string $jwks_uri, ?string $registration_endpoint = null, array $scopes_supported = [], array $response_types_supported = [], array $response_modes_supported = [], array $grant_types_supported = [], array $acr_values_supported = [], array $subject_types_supported = [], array $id_token_signing_alg_values_supported = [], array $id_token_encryption_alg_values_supported = [], array $id_token_encryption_enc_values_supported = [], array $userinfo_signing_alg_values_supported = [], array $userinfo_encryption_alg_values_supported = [], array $userinfo_encryption_enc_values_supported = [], array $request_object_signing_alg_values_supported = [], array $request_object_encryption_alg_values_supported = [], array $request_object_encryption_enc_values_supported = [], array $token_endpoint_auth_methods_supported = [], array $token_endpoint_auth_signing_alg_values_supported = [], array $display_values_supported = [], array $claim_types_supported = [], array $claims_supported = [], ?string $service_documentation = null, array $claims_locales_supported = [], array $ui_locales_supported = [], bool $claims_parameter_supported = false, bool $request_parameter_supported = false, bool $request_uri_parameter_supported = true, bool $require_request_uri_registration = false, ?string $op_policy_uri = null, ?string $op_tos_uri = null, ?string $end_session_endpoint = null, array $code_challenge_methods_supported = [], mixed $extra): mixed
```

**Parameters:**

| Parameter                                           | Type        | Description |
| --------------------------------------------------- | ----------- | ----------- |
| `$issuer`                                           | **string**  |             |
| `$authorization_endpoint`                           | **string**  |             |
| `$token_endpoint`                                   | **string**  |             |
| `$userinfo_endpoint`                                | **string**  |             |
| `$jwks_uri`                                         | **string**  |             |
| `$registration_endpoint`                            | **?string** |             |
| `$scopes_supported`                                 | **array**   |             |
| `$response_types_supported`                         | **array**   |             |
| `$response_modes_supported`                         | **array**   |             |
| `$grant_types_supported`                            | **array**   |             |
| `$acr_values_supported`                             | **array**   |             |
| `$subject_types_supported`                          | **array**   |             |
| `$id_token_signing_alg_values_supported`            | **array**   |             |
| `$id_token_encryption_alg_values_supported`         | **array**   |             |
| `$id_token_encryption_enc_values_supported`         | **array**   |             |
| `$userinfo_signing_alg_values_supported`            | **array**   |             |
| `$userinfo_encryption_alg_values_supported`         | **array**   |             |
| `$userinfo_encryption_enc_values_supported`         | **array**   |             |
| `$request_object_signing_alg_values_supported`      | **array**   |             |
| `$request_object_encryption_alg_values_supported`   | **array**   |             |
| `$request_object_encryption_enc_values_supported`   | **array**   |             |
| `$token_endpoint_auth_methods_supported`            | **array**   |             |
| `$token_endpoint_auth_signing_alg_values_supported` | **array**   |             |
| `$display_values_supported`                         | **array**   |             |
| `$claim_types_supported`                            | **array**   |             |
| `$claims_supported`                                 | **array**   |             |
| `$service_documentation`                            | **?string** |             |
| `$claims_locales_supported`                         | **array**   |             |
| `$ui_locales_supported`                             | **array**   |             |
| `$claims_parameter_supported`                       | **bool**    |             |
| `$request_parameter_supported`                      | **bool**    |             |
| `$request_uri_parameter_supported`                  | **bool**    |             |
| `$require_request_uri_registration`                 | **bool**    |             |
| `$op_policy_uri`                                    | **?string** |             |
| `$op_tos_uri`                                       | **?string** |             |
| `$end_session_endpoint`                             | **?string** |             |
| `$code_challenge_methods_supported`                 | **array**   |             |
| `$extra`                                            | **mixed**   |             |

---

## Inherited methods

### jsonSerialize

```php
public jsonSerialize(): array
```

---

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
