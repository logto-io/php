<?php declare(strict_types=1);
/** Caution: Keep the file name as same as the enum name per PHP convention. */

namespace Logto\Sdk\Storage;

/** The keys enum for the persistent storage. */
enum StorageKey: string
{
  case idToken = 'logto::id_token';
  case accessTokenMap = 'logto::access_token_map';
  case refreshToken = 'logto::refresh_token';
  case signInSession = 'logto::sign_in_session';
}
