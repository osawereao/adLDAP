<?php

namespace Adldap\Laravel\Facades;

use Adldap\Adldap;

class Adldap
{
    /**
     * Get the registered name of the component.
     * Compatible with Laravel Facades without requiring illuminate/support.
     */
    public static function __callStatic($method, $args)
    {
        $instance = function_exists('app') ? app('adldap') : null;

        if (!$instance) {
            throw new \RuntimeException('Adldap is not bound in the application container.');
        }

        return $instance->$method(...$args);
    }
}
