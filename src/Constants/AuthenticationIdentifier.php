<?php declare(strict_types=1);
namespace Logto\Sdk\Constants;

/** The identifier type for sign-in. */
enum AuthenticationIdentifier: string
{
  case email = 'email';
  case phone = 'phone'; 
  case username = 'username';
} 
