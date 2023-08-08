<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Logto\Sdk\Utilities;

final class UtilitiesTest extends TestCase
{
  function test_urlSafeEncode_shouldEncode()
  {
    $this->assertSame(
      Utilities::urlSafeEncode('123'),
      'MTIz',
    );
  }

  function test_urlSafeEncode_shouldRemovePadding()
  {
    $this->assertSame(
      Utilities::urlSafeEncode('1234'),
      'MTIzNA'
    );
  }

  function test_urlSafeEncode_shouldReplaceUnsafeCharacters()
  {
    $this->assertSame(
      Utilities::urlSafeEncode("\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f"),
      'AAECAwQFBgcICQoLDA0ODw'
    );
  }

  function test_urlSafeEncode_shouldReplaceUnsafeEncodedCharacters()
  {
    $this->assertSame(
      Utilities::urlSafeEncode("\xff\xff"),
      '__8'
    );
  }
}
