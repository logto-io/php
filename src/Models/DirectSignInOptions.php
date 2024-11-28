<?php declare(strict_types=1);
namespace Logto\Sdk\Models;

use Logto\Sdk\Constants\DirectSignInMethod;

/** Options for direct sign-in. */
class DirectSignInOptions extends JsonModel
{
  public function __construct(
    /** The method to be used for the direct sign-in. */
    public DirectSignInMethod $method,
    /** 
     * The target to be used for the direct sign-in.
     * For `method: 'social'`, it should be the social connector target.
     */
    public string $target,
  ) {
  }
} 
