---

# AccessToken

The access token class for a resource.

- Full name: `\Logto\Sdk\AccessToken`

## Properties

### token

The access token string.

```php
public string $token
```

---

### expiresAt

The timestamp (in seconds) when the access token will expire.

```php
public int $expiresAt
```

Note this is not the expiration time of the access token itself, but the
expiration time of the access token cache.

---

## Methods

### \_\_construct

```php
public __construct(string $token, int $expiresAt): mixed
```

**Parameters:**

| Parameter    | Type       | Description |
| ------------ | ---------- | ----------- |
| `$token`     | **string** |             |
| `$expiresAt` | **int**    |             |

---

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
