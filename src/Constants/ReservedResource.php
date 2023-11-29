<?php declare(strict_types=1);
/** Caution: Keep the file name as same as the enum name per PHP convention. */

namespace Logto\Sdk\Constants;

/** Resources that reserved by Logto, which cannot be defined by users. */
enum ReservedResource: string
{
  /** The resource for organization template per [RFC 0001](https://github.com/logto-io/rfcs). */
  case organizations = 'urn:logto:resource:organizations';
}
