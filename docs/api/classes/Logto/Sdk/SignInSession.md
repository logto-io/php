---

# SignInSession

The sign-in session that stores the information for the sign-in callback.

Should be stored before redirecting the user to Logto.

- Full name: `\Logto\Sdk\SignInSession`

## Properties

### redirectUri

The redirect URI for the current sign-in session.

```php
public string $redirectUri
```

---

### codeVerifier

The code verifier of Proof Key for Code Exchange (PKCE).

```php
public string $codeVerifier
```

---

### state

The state for OAuth 2.0 authorization request.

```php
public string $state
```

---

## Methods

### \_\_construct

```php
public __construct(string $redirectUri, string $codeVerifier, string $state): mixed
```

**Parameters:**

| Parameter       | Type       | Description |
| --------------- | ---------- | ----------- |
| `$redirectUri`  | **string** |             |
| `$codeVerifier` | **string** |             |
| `$state`        | **string** |             |

---

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
