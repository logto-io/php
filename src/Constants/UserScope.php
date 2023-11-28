<?php declare(strict_types=1);
/** Caution: Keep the file name as same as the enum name per PHP convention. */

namespace Logto\Sdk\Constants;

enum UserScope: string
{
  /** Scope for basic user information. */
  case profile = 'profile';
  /** Scope for email address (`email` and `email_verified` claims). */
  case email = 'email';
  /** Scope for phone number (`phone_number` and `phone_number_verified` claims). */
  case phone = 'phone';
  /** Scope for custom data (`custom_data` claim). */
  case customData = 'custom_data';
  /** Scope for user's social identities (`identities` claim). */
  case identities = 'identities';
  /** Scope for user's roles (`roles` claim). */
  case roles = 'roles';
  /** 
   * Scope for user's organization IDs and perform organization token grant per [RFC 0001](https://github.com/logto-io/rfcs) (`organizations` claim).
   *
   * To learn more about Logto Organizations, see https://docs.logto.io/docs/recipes/organizations/.
   */
  case organizations = 'urn:logto:scope:organizations';
  /**
   * Scope for user's organization roles per [RFC 0001](https://github.com/logto-io/rfcs) (`organization_roles` claim).
   *
   * To learn more about Logto Organizations, see https://docs.logto.io/docs/recipes/organizations/.
   */
  case organizationRoles = 'urn:logto:scope:organization_roles';
}
