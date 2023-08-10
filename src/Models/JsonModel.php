<?php declare(strict_types=1);
namespace Logto\Sdk\Models;

class JsonModel implements \JsonSerializable
{
  public ?array $extra;
  public function jsonSerialize(): array
  {
    $data = [];
    foreach ($this as $key => $value) {
      if ($key === 'extra') {
        continue;
      }
      $data[$key] = $value;
    }
    return array_merge($data, $this->extra ?? []);
  }
}
