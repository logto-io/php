# Logto PHP SDK tutorial

This tutorial will show you how to integrate Logto into your PHP web application.

- The example uses Laravel, but the concepts are the same for other frameworks.
- This tutorial assumes your website is hosted on `https://your-app.com/`.

## Table of contents

- [Logto PHP SDK tutorial](#logto-php-sdk-tutorial)
  - [Table of contents](#table-of-contents)
  - [Installation](#installation)
  - [Integration](#integration)
    - [Init LogtoClient](#init-logtoclient)
    - [Implement the sign-in route](#implement-the-sign-in-route)
    - [Implement the callback route](#implement-the-callback-route)
    - [Implement the home page](#implement-the-home-page)
    - [Implement the sign-out route](#implement-the-sign-out-route)
    - [Checkpoint: Test your application](#checkpoint-test-your-application)
  - [Protect your routes](#protect-your-routes)
  - [Scopes and claims](#scopes-and-claims)
    - [Special ID token claims](#special-id-token-claims)
  - [API resources](#api-resources)
    - [Configure Logto client](#configure-logto-client)
    - [Fetch access token for the API resource](#fetch-access-token-for-the-api-resource)
    - [Fetch organization token for user](#fetch-organization-token-for-user)

## Installation

```bash
composer require logto/sdk
```

## Integration

### Init LogtoClient

```php
use logto\sdk\LogtoClient;
use Logto\Sdk\LogtoConfig;

$client = new LogtoClient(
  new LogtoConfig(
    endpoint: "https://you-logto-endpoint.app",
    appId: "replace-with-your-app-id",
    appSecret: "replace-with-your-app-secret",
  ),
);
```

By default, the SDK uses the built-in PHP session to store the Logto data. If you want to use other storage, you can pass a custom storage object as the second parameter:

```php
$client = new LogtoClient(
  new LogtoConfig(
    // ...
  ),
  new YourCustomStorage(),
);
```

See [Storage](./api/classes/Logto/Sdk/Storage/Storage.md) for more details.


### Implement the sign-in route

In your web application, add a route to properly handle the sign-in request from users. Let's use `/sign-in` as an example:

```php
Route::get('/sign-in', function () {
  return redirect($client->signIn('https://your-app.com/callback'));
});
```

Replace `https://your-app.com/callback` with the callback URL you set in your Logto Console for this application.

If you want to show the sign-up page on the first screen, you can set `interactionMode` to `signUp`:

```php
Route::get('/sign-in', function () {
  return redirect($client->signIn('https://your-app.com/callback', InteractionMode::signUp));
});
```

Now, whenever your users visit `https://your-app.com/sign-in`, it will start a new sign-in attempt and redirect the user to the Logto sign-in page.

> **Note**
> Creating a sign-in route isn't the only way to start a sign-in attempt. You can always use the `signIn` method to get the sign-in URL and redirect the user to it.

### Implement the callback route

After the user signs in, Logto will redirect the user to the callback URL you set in the Logto Console. In this example, we use `/callback` as the callback URL:

```php
Route::get('/callback', function () {
  try {
    $client->handleSignInCallback(); // Handle a lot of stuff
  } catch (\Throwable $exception) {
    return $exception; // Change this to your error handling logic
  }
  return redirect('/'); // Redirect the user to the home page after a successful sign-in
});
```

### Implement the home page

Here we implement a simple home page for demonstration:

- If the user is not signed in, show a sign-in button;
- If the user is signed in, show some basic information about the user.

```php
Route::get('/', function () {
  if ($client->isAuthenticated() === false) {
    return "Not authenticated <a href='/sign-in'>Sign in</a>";
  }

  return (
    // Get local ID token claims
    json_decode($client->getIdTokenClaims())
    . "<br>"
    // Fetch user info from Logto userinfo endpoint
    json_decode($client->fetchUserInfo())
    . "<br><a href='/sign-out'>Sign out</a>"
  );
});
```

Our data models are based on [JsonModel](./api/classes/Logto/Sdk/Models/JsonModel.md), which is safe to accept undefined keys while encoding or decoding JSON.

Note that a field (claim) with `null` value doesn't mean the field is set. The reason may be the related scope is not requested, or the user doesn't have the field.

For example, if we didn't request the `email` scope when signing in, and the `email` field will be `null`. However, if we requested the `email` scope, the `email` field will be the user's email address if available.

To learn more about scopes and claims, see [Scopes and claims](#scopes-and-claims).

### Implement the sign-out route

To clean up the PHP session and Logto session, a sign-out route can be implemented as follows:

```php
Route::get('/sign-out', function () {
  return redirect(
    // Redirect the user to the home page after a successful sign-out
    $client->signOut('https://your-app.com/')
  );
});
```

`postLogoutRedirectUri` is optional, and if not provided, the user will be redirected to a Logto default page after a successful sign-out (without redirecting back to your application).

> The name `postLogoutRedirectUri` is from the [OpenID Connect RP-Initiated Logout](https://openid.net/specs/openid-connect-rpinitiated-1_0.html) specification. Although Logto uses "sign-out" instead of "logout", the concept is the same.

### Checkpoint: Test your application

Now, you can test your application:

1. Visit `https://your-app.com/`, and you should see a "Not authenticated" message with a "Sign in" button.
2. Click the "Sign in" button, and you should be redirected to the Logto sign-in page.
3. After you sign in, you should be redirected back to `https://your-app.com/`, and you should see your user info and a "Sign out" button.
4. Click the "Sign out" button, and you should be redirected back to `https://your-app.com/`, and you should see a "Not authenticated" message with a "Sign in" button.

## Protect your routes

Now, you have a working sign-in flow, but your routes are still unprotected. Per the framework you use, you can create a middleware to protect your routes. For example, in Laravel, you can create a middleware as follows:

```php
namespace App\Http\Middleware;

use Closure;

class LogtoAuth
{
  public function handle($request, Closure $next)
  {
    if ($client->isAuthenticated() === false) {
      return redirect('/sign-in');
    }
    return $next($request);
  }
}
```

Then, you can apply this middleware to your routes:

```php
Route::get('/protected', function () {
  return "Protected page";
})->middleware(LogtoAuth::class);
```

## Scopes and claims

Both of "scope" and "claim" are terms from the OAuth 2.0 and OpenID Connect (OIDC) specifications. In OIDC, there are some optional [scopes and claims conventions](https://openid.net/specs/openid-connect-core-1_0.html#Claims) to follow. Logto uses these conventions to define the scopes and claims for the ID token.

In short, when you request a scope, you will get the corresponding claims in the ID token. For example, if you request the `email` scope, you will get the `email` and `email_verified` claims in the ID token.

By default, Logto SDK requests three scopes: `openid`, `profile`, and `offline_access`. You can add more scopes when configuring the Logto client:

```php
$client = new LogtoClient(
  new LogtoConfig(
    // ...other configs
    scopes: ["email", "phone"], // Update per your needs
  ),
);
```

Alternatively, you can use the `UserScope` enum to add scopes:

```php
use Logto\Sdk\Constants\UserScope;

$client = new LogtoClient(
  new LogtoConfig(
    // ...other configs
    scopes: [UserScope::email, UserScope::phone], // Update per your needs
  ),
);
```

> **Note**
> For now, there's no way to remove the default scopes without mutating the `scopes` list.

### Special ID token claims

Considering performance and the data size, Logto doesn't include all the claims in the ID token, such as `custom_data` which could be a large JSON object. To fetch these claims, you can use the `fetchUserInfo` method:

```php
$client->fetchUserInfo()->custom_data; // Get the custom_data claim
```

See [UserInfoScope](./api/classes/Logto/Sdk/Oidc/UserInfoResponse.md) for details.

## API resources

We recommend to read [🔐 Role-Based Access Control (RBAC)](https://docs.logto.io/docs/recipes/rbac/) first to understand the basic concepts of Logto RBAC and how to set up API resources properly.

### Configure Logto client

Once you have set up the API resources, you can add them when configuring the Logto client:

```php
$client = new LogtoClient(
  new LogtoConfig(
    // ...other configs
    resources: ["https://shopping.your-app.com/api", "https://store.your-app.com/api"], // Add API resources
  ),
);
```

Each API resource has its own permissions (scopes). For example, the `https://shopping.your-app.com/api` resource has the `shopping:read` and `shopping:write` permissions, and the `https://store.your-app.com/api` resource has the `store:read` and `store:write` permissions.

To request these permissions, you can add them when configuring the Logto client:

```php
$client = new LogtoClient(
  new LogtoConfig(
    // ...other configs
    scopes: ["shopping:read", "shopping:write", "store:read", "store:write"], // Add scopes
  ),
);
```

You may notice that scopes are defined separately from API resources. This is because [Resource Indicators for OAuth 2.0](https://www.rfc-editor.org/rfc/rfc8707.html) specifies the final scopes for the request will be the cartesian product of all the scopes at all the target services.

Thus, in the above case, scopes can be simplified from the definition in Logto, both of the API resources can have `read` and `write` scopes without the prefix. Then, in the Logto config:

```php
$client = new LogtoClient(
  new LogtoConfig(
    // ...other configs
    scopes: ["read", "write"], // Add scopes
    resources: ["https://shopping.your-app.com/api", "https://store.your-app.com/api"], // Add API resources
  ),
);
```

For every API resource, it will request for both `read` and `write` scopes.

> **Note**
> It is fine to request scopes that are not defined in the API resources. For example, you can request the `email` scope even if the API resources don't have the `email` scope available. Unavailable scopes will be safely ignored.

After the successful sign-in, Logto will issue proper scopes to every API resource according to the user's roles.

### Fetch access token for the API resource

To fetch the access token for a specific API resource, you can use the `getAccessToken` method:

```php
$accessToken = $client->getAccessToken("https://shopping.your-app.com/api");
```

This method will return a JWT access token that can be used to access the API resource, if the user has the proper permissions. If the current cached access token has expired, this method will automatically try to use the refresh token to get a new access token.

If failed by any reason, this method will return `null`.

### Fetch organization token for user

If organization is new to you, please read [🏢 Organizations (Multi-tenancy)](https://docs.logto.io/docs/recipes/organizations/) to get started.

You need to add `UserScope.organizations` scope when configuring the Logto client:

```php
use Logto\Sdk\Constants\UserScope;

$client = new LogtoClient(
  new LogtoConfig(
    // ...other configs
    scopes: [UserScope::organizations], // Add scopes
  ),
);
```

Once the user is signed in, you can fetch the organization token for the user:

```php
# Replace the parameter with a valid organization ID.
# Valid organization IDs for the user can be found in the ID token claim `organizations`.
$organizationToken = $client->getOrganizationToken("organization-id");
# or
$claims = $client->getOrganizationTokenClaims("organization-id");
```
