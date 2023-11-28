---

# Storage

The storage interface for the Logto client. Logto client will use this
interface to store and retrieve the logto session data.

Usually this should be implemented as a persistent storage, such as a
session or a database, since the page will be redirected to Logto and
then back to the original page.

- Full name: `\Logto\Sdk\Storage\Storage`

## Methods

### get

Get the stored string for the given key, return None if not found.

```php
public get(\Logto\Sdk\Storage\StorageKey $key): ?string
```

**Parameters:**

| Parameter | Type                              | Description |
| --------- | --------------------------------- | ----------- |
| `$key`    | **\Logto\Sdk\Storage\StorageKey** |             |

---

### set

Set the stored value (string or None) for the given key.

```php
public set(\Logto\Sdk\Storage\StorageKey $key, ?string $value): void
```

**Parameters:**

| Parameter | Type                              | Description |
| --------- | --------------------------------- | ----------- |
| `$key`    | **\Logto\Sdk\Storage\StorageKey** |             |
| `$value`  | **?string**                       |             |

---

### delete

Delete the stored value for the given key.

```php
public delete(\Logto\Sdk\Storage\StorageKey $key): void
```

**Parameters:**

| Parameter | Type                              | Description |
| --------- | --------------------------------- | ----------- |
| `$key`    | **\Logto\Sdk\Storage\StorageKey** |             |

---

---

> Automatically generated from source code comments using [phpDocumentor](http://www.phpdoc.org/) and [saggre/phpdocumentor-markdown](https://github.com/Saggre/phpDocumentor-markdown)
