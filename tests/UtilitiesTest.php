<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Firebase\JWT\JWT;

final class UtilitiesTest extends TestCase
{
  function test_urlSafeEncode_shouldEncode()
  {
    $this->assertSame(
      JWT::urlsafeB64Encode('123'),
      'MTIz',
    );
  }

  function test_urlSafeEncode_shouldRemovePadding()
  {
    $this->assertSame(
      JWT::urlsafeB64Encode('1234'),
      'MTIzNA'
    );
  }

  function test_urlSafeEncode_shouldReplaceUnsafeCharacters()
  {
    $this->assertSame(
      JWT::urlsafeB64Encode("\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f"),
      'AAECAwQFBgcICQoLDA0ODw'
    );
  }

  function test_urlSafeEncode_shouldReplaceUnsafeEncodedCharacters()
  {
    $this->assertSame(
      JWT::urlsafeB64Encode("\xff\xff"),
      '__8'
    );
  }
}
