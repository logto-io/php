---

# SessionStorage

The storage implementation using PHP session.

- Full name: `\Logto\Sdk\Storage\SessionStorage`
- This class implements:
  [`\Logto\Sdk\Storage\Storage`](./Storage.md)

## Methods

### \_\_construct

```php
public __construct(): mixed
```

---

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
