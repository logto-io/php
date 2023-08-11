***

# OidcCore

The core OIDC functions for the Logto client. Provider-agonistic functions
are implemented as static methods, while other functions are implemented as
instance methods.



* Full name: `\Logto\Sdk\Oidc\OidcCore`


## Constants

| Constant | Visibility | Type | Value |
|:---------|:-----------|:-----|:------|
|`DEFAULT_SCOPES`|public| |[&#039;openid&#039;, &#039;offline_access&#039;, &#039;profile&#039;]|

## Properties


### jwkSet



```php
protected \Firebase\JWT\CachedKeySet $jwkSet
```






***

### metadata



```php
public \Logto\Sdk\Models\OidcProviderMetadata $metadata
```






***

### client



```php
protected \GuzzleHttp\Client $client
```






***

## Methods


### create

Create a OidcCore instance for the given Logto endpoint using the discovery URL.

```php
public static create(string $logtoEndpoint): \Logto\Sdk\Oidc\OidcCore
```

Note it may take a few time to fetch the provider metadata since it will send a
network request.

* This method is **static**.




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$logtoEndpoint` | **string** |  |




***

### generateState

Generate a random string (32 bytes) for the state parameter.

```php
public static generateState(): string
```



* This method is **static**.







***

### generateCodeVerifier

Generate a random code verifier string (32 bytes) for PKCE.

```php
public static generateCodeVerifier(): string
```

See [Client Creates a Code Verifier](https://www.rfc-editor.org/rfc/rfc7636.html#section-4.1) to learn more.

* This method is **static**.







***

### generateCodeChallenge

Generate a code challenge string for the given code verifier string.

```php
public static generateCodeChallenge(string $codeVerifier): string
```

See [Client Creates the Code Challenge](https://www.rfc-editor.org/rfc/rfc7636.html#section-4.2) to learn more.

* This method is **static**.




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$codeVerifier` | **string** |  |




***

### __construct

Initialize the OIDC core with the provider metadata. You can use the
static create method to create an instance for the given Logto endpoint.

```php
public __construct(\Logto\Sdk\Models\OidcProviderMetadata $metadata, \GuzzleHttp\Client $client = new Client()): mixed
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$metadata` | **\Logto\Sdk\Models\OidcProviderMetadata** |  |
| `$client` | **\GuzzleHttp\Client** |  |



**See Also:**

* \Logto\Sdk\Oidc\OidcCore::create() - 

***

### verifyIdToken

Verify the ID Token signature and its issuer and client ID, throw an exception
if the verification fails.

```php
public verifyIdToken(string $idToken, string $clientId): mixed
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$idToken` | **string** |  |
| `$clientId` | **string** |  |




***

### fetchTokenByCode

Fetch the token from the token endpoint using the authorization code.

```php
public fetchTokenByCode(string $clientId, ?string $clientSecret, string $redirectUri, string $code, string $codeVerifier): \Logto\Sdk\Oidc\TokenResponse
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$clientId` | **string** |  |
| `$clientSecret` | **?string** |  |
| `$redirectUri` | **string** |  |
| `$code` | **string** |  |
| `$codeVerifier` | **string** |  |




***

### fetchTokenByRefreshToken

Fetch the token for the given resource from the token endpoint using the refresh token.

```php
public fetchTokenByRefreshToken(string $clientId, ?string $clientSecret, string $refreshToken, string $resource = &#039;&#039;): \Logto\Sdk\Oidc\TokenResponse
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$clientId` | **string** |  |
| `$clientSecret` | **?string** |  |
| `$refreshToken` | **string** |  |
| `$resource` | **string** |  |




***

### fetchUserInfo

Fetch the user info from the OpenID Connect UserInfo endpoint.

```php
public fetchUserInfo(string $accessToken): \Logto\Sdk\Oidc\UserInfoResponse
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$accessToken` | **string** |  |



**See Also:**

*  - [UserInfo Endpoint](https://openid.net/specs/openid-connect-core-1_0.html#UserInfo]

***


***
> Automatically generated from source code comments on 2023-08-11 using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
