<?php

/**
 * Laravel 8 on PHP 8.4: vendor code triggers E_DEPRECATED noise in CLI/browser.
 * Real errors still show; only deprecation notices are hidden.
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

set_error_handler(function ($severity, $message, $file, $line) {
    if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
        return true;
    }

    return false;
});