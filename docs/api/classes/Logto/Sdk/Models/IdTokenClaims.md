---

# IdTokenClaims

The ID token claims object.

- Full name: `\Logto\Sdk\Models\IdTokenClaims`
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

The audience that the token is intended for, which is the client ID.

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

### at_hash

```php
public ?string $at_hash
```

---

### name

The user's full name.

```php
public ?string $name
```

---

### username

The user's username.

```php
public ?string $username
```

---

### picture

The user's profile picture URL.

```php
public ?string $picture
```

---

### email

The user's email address.

```php
public ?string $email
```

---

### email_verified

Whether the email address is verified.

```php
public ?bool $email_verified
```

---

### phone_number

The user's phone number.

```php
public ?string $phone_number
```

---

### phone_number_verified

Whether the phone number is verified.

```php
public ?bool $phone_number_verified
```

---

### roles

The user's roles.

```php
public ?array $roles
```

---

### organizations

The user's organization IDs.

```php
public ?array $organizations
```

---

### organization_roles

The user's organization roles.

```php
public ?array $organization_roles
```

---

## Methods

### \_\_construct

```php
public __construct(string $iss, string $sub, string $aud, int $exp, int $iat, ?string $at_hash = null, ?string $name = null, ?string $username = null, ?string $picture = null, ?string $email = null, ?bool $email_verified = null, ?string $phone_number = null, ?bool $phone_number_verified = null, ?array $roles = null, ?array $organizations = null, ?array $organization_roles = null, mixed $extra): mixed
```

**Parameters:**

| Parameter                | Type        | Description |
| ------------------------ | ----------- | ----------- |
| `$iss`                   | **string**  |             |
| `$sub`                   | **string**  |             |
| `$aud`                   | **string**  |             |
| `$exp`                   | **int**     |             |
| `$iat`                   | **int**     |             |
| `$at_hash`               | **?string** |             |
| `$name`                  | **?string** |             |
| `$username`              | **?string** |             |
| `$picture`               | **?string** |             |
| `$email`                 | **?string** |             |
| `$email_verified`        | **?bool**   |             |
| `$phone_number`          | **?string** |             |
| `$phone_number_verified` | **?bool**   |             |
| `$roles`                 | **?array**  |             |
| `$organizations`         | **?array**  |             |
| `$organization_roles`    | **?array**  |             |
| `$extra`                 | **mixed**   |             |

---

## Inherited methods

### jsonSerialize

```php
public jsonSerialize(): array
```

---

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
