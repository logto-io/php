<?php declare(strict_types=1);
namespace Logto\Sdk\Storage;

interface Storage
{
  public function get(StorageKey $key): ?string;
  public function set(StorageKey $key, ?string $value): void;
  public function delete(StorageKey $key): void;
}
