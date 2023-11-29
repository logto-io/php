---

# LogtoConfig

The configuration object for the Logto client.

- Full name: `\Logto\Sdk\LogtoConfig`

## Properties

### endpoint

The endpoint for the Logto server, you can get it from the integration guide
or the team settings page of the Logto Console.

```php
public string $endpoint
```

---

### appId

The client ID of your application, you can get it from the integration guide
or the application details page of the Logto Console.

```php
public string $appId
```

---

### appSecret

The client secret of your application, you can get it from the application
details page of the Logto Console.

```php
public ?string $appSecret
```

---

### scopes

The scopes (permissions) that your application needs to access.

```php
public ?array $scopes
```

Scopes that will be added by default: `openid`, `offline_access` and `profile`.

If resources are specified, scopes will be applied to every resource.

**See Also:**

- - [Fetch user information](https://docs.logto.io/docs/recipes/integrate-logto/vanilla-js/#fetch-user-information)
    for more information of available scopes for user information.

---

### resources

The API resources that your application needs to access. You can specify
multiple resources by providing an array of strings.

```php
public ?array $resources
```

**See Also:**

- [RBAC](https://docs.logto.io/docs/recipes/rbac/) - to learn more about how to use role-based access control (RBAC) to protect API resources.

---

### prompt

The prompt parameter for the OpenID Connect authorization request.

```php
public \Logto\Sdk\Prompt $prompt
```

- If the value is `consent`, the user will be able to reuse the existing consent without being prompted for sign-in again.
- If the value is `login`, the user will be prompted for sign-in again anyway. Note there will be no Refresh Token returned in this case.

---

## Methods

### \_\_construct

```php
public __construct(string $endpoint, string $appId, ?string $appSecret = null, ?array $scopes = null, ?array $resources = null, \Logto\Sdk\Prompt $prompt = Prompt::consent): mixed
```

**Parameters:**

| Parameter    | Type                  | Description                                                                                 |
| ------------ | --------------------- | ------------------------------------------------------------------------------------------- |
| `$endpoint`  | **string**            | The endpoint for the Logto server. See {@link} for details.                                 |
| `$appId`     | **string**            | The client ID of your application. See {@link} for details.                                 |
| `$appSecret` | **?string**           | The client secret of your application. See {@link} for details.                             |
| `$scopes`    | **?array**            | The scopes (permissions) that your application needs to access. See {@link} for details.    |
| `$resources` | **?array**            | The API resources that your application needs to access. See {@link} for details.           |
| `$prompt`    | **\Logto\Sdk\Prompt** | The prompt parameter for the OpenID Connect authorization request. See {@link} for details. |

---

### hasOrganizationScope

Check if the organization scope is requested by the configuration.

```php
public hasOrganizationScope(): bool
```

**See Also:**

- \Logto\Sdk\Constants\UserScope::organizations -

---

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
