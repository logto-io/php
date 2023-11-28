---

# Logto PHP SDK

This is an automatically generated documentation for **Logto PHP SDK**.

## Namespaces

### \Logto\Sdk

#### Classes

| Class                                                     | Description                                                                                                                                  |
| --------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------- |
| [`AccessToken`](./classes/Logto/Sdk/AccessToken.md)       | The access token class for a resource.                                                                                                       |
| [`LogtoClient`](./classes/Logto/Sdk/LogtoClient.md)       | The main class of the Logto client. You should create an instance of this class<br />and use it to sign in, sign out, get access token, etc. |
| [`LogtoConfig`](./classes/Logto/Sdk/LogtoConfig.md)       | The configuration object for the Logto client.                                                                                               |
| [`LogtoException`](./classes/Logto/Sdk/LogtoException.md) | The exception class to identify the exceptions from the Logto client.                                                                        |
| [`SignInSession`](./classes/Logto/Sdk/SignInSession.md)   | The sign-in session that stores the information for the sign-in callback.                                                                    |

### \Logto\Sdk\Models

#### Classes

| Class                                                                        | Description                                                                           |
| ---------------------------------------------------------------------------- | ------------------------------------------------------------------------------------- |
| [`AccessTokenClaims`](./classes/Logto/Sdk/Models/AccessTokenClaims.md)       | The access token claims object.                                                       |
| [`IdTokenClaims`](./classes/Logto/Sdk/Models/IdTokenClaims.md)               | The ID token claims object.                                                           |
| [`JsonModel`](./classes/Logto/Sdk/Models/JsonModel.md)                       | A base model class that can be serialized to JSON with extra<br />properties support. |
| [`OidcProviderMetadata`](./classes/Logto/Sdk/Models/OidcProviderMetadata.md) | The OpenID Connect Discovery response object.                                         |

### \Logto\Sdk\Oidc

#### Classes

| Class                                                              | Description                                                                                                                                                                        |
| ------------------------------------------------------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| [`OidcCore`](./classes/Logto/Sdk/Oidc/OidcCore.md)                 | The core OIDC functions for the Logto client. Provider-agonistic functions<br />are implemented as static methods, while other functions are implemented as<br />instance methods. |
| [`TokenResponse`](./classes/Logto/Sdk/Oidc/TokenResponse.md)       | The response model from the token endpoint.                                                                                                                                        |
| [`UserIdentity`](./classes/Logto/Sdk/Oidc/UserIdentity.md)         | The user identity model.                                                                                                                                                           |
| [`UserInfoResponse`](./classes/Logto/Sdk/Oidc/UserInfoResponse.md) | The response model from the user info endpoint.                                                                                                                                    |

### \Logto\Sdk\Storage

#### Classes

| Class                                                             | Description                                   |
| ----------------------------------------------------------------- | --------------------------------------------- |
| [`SessionStorage`](./classes/Logto/Sdk/Storage/SessionStorage.md) | The storage implementation using PHP session. |

#### Interfaces

| Interface                                           | Description                                                                                                                         |
| --------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------- |
| [`Storage`](./classes/Logto/Sdk/Storage/Storage.md) | The storage interface for the Logto client. Logto client will use this<br />interface to store and retrieve the logto session data. |

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
