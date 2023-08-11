<?php declare(strict_types=1);
namespace Logto\Sdk;

class Utilities
{
  function __construct()
  {
    throw new \Exception('Not implemented');
  }

  /** Encode the given bytes to a URL-safe string. */
  static function urlSafeEncode(string $data)
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }
}
