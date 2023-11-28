---

# LogtoClient

The main class of the Logto client. You should create an instance of this class
and use it to sign in, sign out, get access token, etc.

- Full name: `\Logto\Sdk\LogtoClient`

## Properties

### oidcCore

```php
protected \Logto\Sdk\Oidc\OidcCore $oidcCore
```

---

### config

```php
public \Logto\Sdk\LogtoConfig $config
```

---

### storage

```php
public \Logto\Sdk\Storage\Storage $storage
```

---

## Methods

### \_\_construct

```php
public __construct(\Logto\Sdk\LogtoConfig $config, \Logto\Sdk\Storage\Storage $storage = new SessionStorage()): mixed
```

**Parameters:**

| Parameter  | Type                           | Description |
| ---------- | ------------------------------ | ----------- |
| `$config`  | **\Logto\Sdk\LogtoConfig**     |             |
| `$storage` | **\Logto\Sdk\Storage\Storage** |             |

---

### isAuthenticated

Check if the user is authenticated by checking if the ID Token exists.

```php
public isAuthenticated(): bool
```

---

### getIdToken

Get the ID Token string. If you need to get the claims in the ID Token, use
`getIdTokenClaims` instead.

```php
public getIdToken(): ?string
```

---

### getIdTokenClaims

Get the claims in the ID Token. If the ID Token does not exist, an exception
will be thrown.

```php
public getIdTokenClaims(): \Logto\Sdk\Models\IdTokenClaims
```

---

### getAccessToken

Get the access token for the given resource. If the access token is expired,
it will be refreshed automatically. If no refresh token is found, null will
be returned.

```php
public getAccessToken(string $resource = &#039;&#039;): ?string
```

**Parameters:**

| Parameter   | Type       | Description |
| ----------- | ---------- | ----------- |
| `$resource` | **string** |             |

---

### getOrganizationToken

Get the access token for the given organization ID. If the access token is
expired, it will be refreshed automatically. If no refresh token is found,
null will be returned.

```php
public getOrganizationToken(string $organizationId): ?string
```

**Parameters:**

| Parameter         | Type       | Description |
| ----------------- | ---------- | ----------- |
| `$organizationId` | **string** |             |

---

### getAccessTokenClaims

Get the claims in the access token for the given resource. If the access token
is expired, it will be refreshed automatically. If it's unable to refresh the
access token, an exception will be thrown.

```php
public getAccessTokenClaims(string $resource = &#039;&#039;): \Logto\Sdk\Models\AccessTokenClaims
```

**Parameters:**

| Parameter   | Type       | Description |
| ----------- | ---------- | ----------- |
| `$resource` | **string** |             |

---

### getOrganizationTokenClaims

Get the claims in the access token for the given organization ID. If the access
token is expired, it will be refreshed automatically. If it's unable to refresh
the access token, an exception will be thrown.

```php
public getOrganizationTokenClaims(string $organizationId): \Logto\Sdk\Models\AccessTokenClaims
```

**Parameters:**

| Parameter         | Type       | Description |
| ----------------- | ---------- | ----------- |
| `$organizationId` | **string** |             |

---

### getRefreshToken

Get the refresh token string.

```php
public getRefreshToken(): ?string
```

---

### signIn

Returns the sign-in URL for the given redirect URI. You should redirect the user
to the returned URL to sign in.

```php
public signIn(string $redirectUri, ?\Logto\Sdk\InteractionMode $interactionMode = null): string
```

By specifying the interaction mode, you can control whether the user will be
prompted for sign-in or sign-up on the first screen. If the interaction mode is
not specified, the default one will be used.

**Parameters:**

| Parameter          | Type                            | Description |
| ------------------ | ------------------------------- | ----------- |
| `$redirectUri`     | **string**                      |             |
| `$interactionMode` | **?\Logto\Sdk\InteractionMode** |             |

---

### signOut

Returns the sign-out URL for the given post-logout redirect URI. You should
redirect the user to the returned URL to sign out.

```php
public signOut(?string $postLogoutRedirectUri = null): string
```

If the post-logout redirect URI is not provided, the Logto default post-logout
redirect URI will be used.

Note:
If the OpenID Connect server does not support the end session endpoint
(i.e. OpenID Connect RP-Initiated Logout), the function will throw an
exception. Logto supports the end session endpoint.

Example:

```php
header('Location: ' . $client->signIn("https://example.com/"));
```

**Parameters:**

| Parameter                | Type        | Description |
| ------------------------ | ----------- | ----------- |
| `$postLogoutRedirectUri` | **?string** |             |

---

### handleSignInCallback

Handle the sign-in callback from the Logto server. This method should be called
in the callback route handler of your application.

```php
public handleSignInCallback(): void
```

---

### fetchUserInfo

Fetch the user information from the UserInfo endpoint. If the access token
is expired, it will be refreshed automatically.

```php
public fetchUserInfo(): \Logto\Sdk\Oidc\UserInfoResponse
```

---

### buildSignInUrl

```php
protected buildSignInUrl(string $redirectUri, string $codeChallenge, string $state, ?\Logto\Sdk\InteractionMode $interactionMode): string
```

**Parameters:**

| Parameter          | Type                            | Description |
| ------------------ | ------------------------------- | ----------- |
| `$redirectUri`     | **string**                      |             |
| `$codeChallenge`   | **string**                      |             |
| `$state`           | **string**                      |             |
| `$interactionMode` | **?\Logto\Sdk\InteractionMode** |             |

---

### setSignInSession

```php
protected setSignInSession(\Logto\Sdk\SignInSession $data): void
```

**Parameters:**

| Parameter | Type                         | Description |
| --------- | ---------------------------- | ----------- |
| `$data`   | **\Logto\Sdk\SignInSession** |             |

---

### getSignInSession

```php
protected getSignInSession(): ?\Logto\Sdk\SignInSession
```

---

### handleTokenResponse

```php
protected handleTokenResponse(string $resource, \Logto\Sdk\Oidc\TokenResponse $tokenResponse): void
```

**Parameters:**

| Parameter        | Type                              | Description |
| ---------------- | --------------------------------- | ----------- |
| `$resource`      | **string**                        |             |
| `$tokenResponse` | **\Logto\Sdk\Oidc\TokenResponse** |             |

---

### \_getAccessTokenMap

Return the raw array that stores the access token map in the storage.

```php
protected _getAccessTokenMap(): array
```

---

### \_setAccessToken

```php
protected _setAccessToken(string $resource, string $accessToken, int $expiresIn): void
```

**Parameters:**

| Parameter      | Type       | Description |
| -------------- | ---------- | ----------- |
| `$resource`    | **string** |             |
| `$accessToken` | **string** |             |
| `$expiresIn`   | **int**    |             |

---

### \_getAccessToken

Get the valid access token for the given resource from storage, no refresh will be performed.

```php
protected _getAccessToken(string $resource): ?string
```

**Parameters:**

| Parameter   | Type       | Description |
| ----------- | ---------- | ----------- |
| `$resource` | **string** |             |

---

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
