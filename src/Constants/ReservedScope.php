<?php declare(strict_types=1);
/** Caution: Keep the file name as same as the enum name per PHP convention. */

namespace Logto\Sdk\Constants;

/** Scopes that reserved by Logto, which will be added to the auth request automatically. */
enum ReservedScope: string
{
  case openId = 'openid';
  case offlineAccess = 'offline_access';
}
