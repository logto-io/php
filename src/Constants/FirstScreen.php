<?php declare(strict_types=1);
namespace Logto\Sdk\Constants;

/** The first screen to show in the sign-in experience. */
enum FirstScreen: string 
{
  case resetPassword = 'reset_password';
  case signIn = 'identifier:sign_in';
  case register = 'identifier:register';
  case singleSignOn = 'single_sign_on';
} 
