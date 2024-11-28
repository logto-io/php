<?php declare(strict_types=1);
namespace Logto\Sdk\Constants;

/** The method to be used for direct sign-in. */
enum DirectSignInMethod: string
{
  case social = 'social';
  case sso = 'sso';
} 
