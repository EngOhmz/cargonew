<?php

namespace App\Foundation\Bootstrap;

use Illuminate\Foundation\Bootstrap\HandleExceptions as BaseHandleExceptions;

/**
 * Laravel 8 on PHP 8.4: vendor nullable-parameter deprecations are harmless noise.
 * Parent logs early deprecations only when bootstrapped; otherwise PHP prints them.
 */
class HandleExceptions extends BaseHandleExceptions
{
    public function handleDeprecation($message, $file, $line)
    {
        return true;
    }
}
