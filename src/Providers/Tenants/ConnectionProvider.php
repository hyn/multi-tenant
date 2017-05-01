<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/hyn/multi-tenant
 *
 */

namespace Hyn\Tenancy\Providers\Tenants;

use Hyn\Tenancy\Database\Connection;
use Hyn\Tenancy\Listeners\AffectServicesListener;
use Illuminate\Support\ServiceProvider;

class ConnectionProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Connection::class);
        AffectServicesListener::registerService($this->app->make(Connection::class));
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            Connection::class
        ];
    }
}
