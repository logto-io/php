<?php declare(strict_types=1);

use Logto\Sdk\Oidc\OidcCore;
use PHPUnit\Framework\TestCase;

final class OidcCoreTest extends TestCase
{
  function test_generateState()
  {
    $this->assertSame(
      strlen(OidcCore::generateState()),
      43,
    );
  }

  function test_generateCodeVerifier()
  {
    $this->assertSame(
      strlen(OidcCore::generateCodeVerifier()),
      43,
    );
  }

  function test_generateCodeChallenge()
  {
    $this->assertSame(
      OidcCore::generateCodeChallenge('12345678901234567890123456789012345678901234567890'),
      '9Y__uhKapn7GO_ElcaQpd8C3hdOyqTzAU4VXyR2iEV0',
    );
  }
}
