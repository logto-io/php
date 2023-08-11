<?php declare(strict_types=1);
namespace Logto\Sdk\Models;


/**
 * A base model class that can be serialized to JSON with extra
 * properties support.
 * 
 * @example
 * ```php
 * class UserInfo extends JsonModel
 * {
 *   public function __construct(
 *   public string $sub,
 *   public string $name,
 *   ...$extra
 *   ) {
 *    $this->extra = $extra;
 *   }
 * }
 * 
 * $user = new UserInfo(['sub' => '123', 'name' => 'John Wick', 'email' => 'john@wick.com']);
 * 
 * $user->sub; // '123'
 * $user->name; // 'John Wick'
 * $user->extra; // ['email' => 'john@wick']
 * ```
 */
class JsonModel implements \JsonSerializable
{
  /** The extra fields of the model. */
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
