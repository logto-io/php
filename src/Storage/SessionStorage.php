<?php declare(strict_types=1);
namespace Logto\Sdk\Storage;

/**
 * The storage implementation using PHP session.
 */
class SessionStorage implements Storage
{
  public function __construct()
  {
    // Start a new session or resume the existing session
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  public function get(StorageKey $key): ?string
  {
    return $_SESSION[$key->value] ?? null;
  }

  public function set(StorageKey $key, ?string $value): void
  {
    if ($value === null) {
      $this->delete($key);
    } else {
      $_SESSION[$key->value] = $value;
    }
  }

  public function delete(StorageKey $key): void
  {
    unset($_SESSION[$key->value]);
  }
}
