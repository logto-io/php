<?php declare(strict_types=1);
namespace Logto\Sdk\Storage;

/**
 * The storage interface for the Logto client. Logto client will use this
 * interface to store and retrieve the logto session data.
 * 
 * Usually this should be implemented as a persistent storage, such as a
 * session or a database, since the page will be redirected to Logto and
 * then back to the original page.
 */
interface Storage
{
  /** Get the stored string for the given key, return None if not found. */
  public function get(StorageKey $key): ?string;
  /** Set the stored value (string or None) for the given key. */
  public function set(StorageKey $key, ?string $value): void;
  /** Delete the stored value for the given key. */
  public function delete(StorageKey $key): void;
}
