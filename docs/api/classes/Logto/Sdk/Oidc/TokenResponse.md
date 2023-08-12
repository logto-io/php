---

# TokenResponse

The response model from the token endpoint.

- Full name: `\Logto\Sdk\Oidc\TokenResponse`
- Parent class: [`\Logto\Sdk\Models\JsonModel`](../Models/JsonModel.md)

**See Also:**

- - [Token Endpoint](https://openid.net/specs/openid-connect-core-1_0.html#TokenEndpoint) to learn more.

## Properties

### access_token

The access token string.

```php
public string $access_token
```

---

### token_type

The token type string, should be "Bearer".

```php
public string $token_type
```

---

### expires_in

The expiration time of the access token (in seconds).

```php
public int $expires_in
```

---

### refresh_token

The refresh token string.

```php
public ?string $refresh_token
```

---

### id_token

The ID token string.

```php
public ?string $id_token
```

---

## Methods

### \_\_construct

```php
public __construct(string $access_token, string $token_type, int $expires_in, ?string $refresh_token = null, ?string $id_token = null, mixed $extra): mixed
```

**Parameters:**

| Parameter        | Type        | Description |
| ---------------- | ----------- | ----------- |
| `$access_token`  | **string**  |             |
| `$token_type`    | **string**  |             |
| `$expires_in`    | **int**     |             |
| `$refresh_token` | **?string** |             |
| `$id_token`      | **?string** |             |
| `$extra`         | **mixed**   |             |

---

## Inherited methods

### jsonSerialize

```php
public jsonSerialize(): array
```

---

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
