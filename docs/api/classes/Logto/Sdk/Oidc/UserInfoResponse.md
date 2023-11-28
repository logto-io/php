---

# UserInfoResponse

The response model from the user info endpoint.

- Full name: `\Logto\Sdk\Oidc\UserInfoResponse`
- Parent class: [`\Logto\Sdk\Models\JsonModel`](../Models/JsonModel.md)

## Properties

### sub

The subject identifier for whom the token is intended (user ID).

```php
public string $sub
```

---

### name

The full name of the user.

```php
public ?string $name
```

---

### username

The username of the user.

```php
public ?string $username
```

---

### picture

The profile picture URL of the user.

```php
public ?string $picture
```

---

### email

The email address of the user.

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

The phone number of the user.

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

### custom_data

The custom data of the user, can be any JSON object.

```php
public mixed $custom_data
```

---

### identities

The identities of the user, can be a dictionary of key-value pairs, where the key is
the identity type and the value is the `UserIdentity` object.

```php
public ?array $identities
```

---

## Methods

### \_\_construct

```php
public __construct(string $sub, ?string $name = null, ?string $username = null, ?string $picture = null, ?string $email = null, ?bool $email_verified = null, ?string $phone_number = null, ?bool $phone_number_verified = null, ?array $roles = null, ?array $organizations = null, ?array $organization_roles = null, mixed $custom_data = null, ?array $identities = null, mixed $extra): mixed
```

**Parameters:**

| Parameter                | Type        | Description |
| ------------------------ | ----------- | ----------- |
| `$sub`                   | **string**  |             |
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
| `$custom_data`           | **mixed**   |             |
| `$identities`            | **?array**  |             |
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
