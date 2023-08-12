---

# AccessTokenClaims

The access token claims object.

- Full name: `\Logto\Sdk\Models\AccessTokenClaims`
- Parent class: [`\Logto\Sdk\Models\JsonModel`](./JsonModel.md)

## Properties

### iss

The issuer identifier for whom issued the token.

```php
public string $iss
```

---

### sub

The subject identifier for whom the token is intended (user ID).

```php
public string $sub
```

---

### aud

The audience that the token is intended for, which is the client ID or the resource
indicator.

```php
public string $aud
```

---

### exp

The expiration time of the token (in seconds).

```php
public int $exp
```

---

### iat

The time at which the token was issued (in seconds).

```php
public int $iat
```

---

### scope

The scopes that the token is granted for.

```php
public string $scope
```

---

### client_id

The client ID that the token is granted for. Useful when the client ID is not
included in the `aud` claim.

```php
public ?string $client_id
```

---

## Methods

### \_\_construct

```php
public __construct(string $iss, string $sub, string $aud, int $exp, int $iat, string $scope, ?string $client_id = null, mixed $extra): mixed
```

**Parameters:**

| Parameter    | Type        | Description |
| ------------ | ----------- | ----------- |
| `$iss`       | **string**  |             |
| `$sub`       | **string**  |             |
| `$aud`       | **string**  |             |
| `$exp`       | **int**     |             |
| `$iat`       | **int**     |             |
| `$scope`     | **string**  |             |
| `$client_id` | **?string** |             |
| `$extra`     | **mixed**   |             |

---

## Inherited methods

### jsonSerialize

```php
public jsonSerialize(): array
```

---

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
