<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://laravel-tenancy.com
 * @see https://github.com/hyn/multi-tenant
 */

namespace Hyn\Tenancy\Facades;

use Illuminate\Support\Facades\Facade;
use Hyn\Tenancy\Environment;

class EnvironmentFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Environment::class;
    }
}
