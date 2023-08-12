---

# UserIdentity

The user identity model.

- Full name: `\Logto\Sdk\Oidc\UserIdentity`
- Parent class: [`\Logto\Sdk\Models\JsonModel`](../Models/JsonModel.md)

## Properties

### userId

The user ID of the target identity.

```php
public string $userId
```

---

### details

The details of the target identity, can be any JSON object.

```php
public ?array $details
```

---

## Methods

### \_\_construct

```php
public __construct(string $userId, ?array $details = null, mixed $extra): mixed
```

**Parameters:**

| Parameter  | Type       | Description |
| ---------- | ---------- | ----------- |
| `$userId`  | **string** |             |
| `$details` | **?array** |             |
| `$extra`   | **mixed**  |             |

---

## Inherited methods

### jsonSerialize

```php
public jsonSerialize(): array
```

---

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
