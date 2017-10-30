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

namespace Hyn\Tenancy\Providers;

use Hyn\Tenancy\Commands\InstallCommand;
use Hyn\Tenancy\Contracts;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Providers\Tenants as Providers;
use Hyn\Tenancy\Repositories;
use Illuminate\Support\ServiceProvider;
use Hyn\Tenancy\Contracts\Customer as CustomerContact;
use Hyn\Tenancy\Contracts\Hostname as HostnameContact;
use Hyn\Tenancy\Contracts\Website as WebsiteContract;

class TenancyProvider extends ServiceProvider
{
    public function register()
    {
        $this->migrations();
        $this->registerConfiguration();

        $this->models();

        $this->repositories();

        $this->app->register(Providers\ConfigurationProvider::class);
        $this->app->register(Providers\PasswordProvider::class);
        $this->app->register(Providers\ConnectionProvider::class);
        $this->app->register(Providers\UuidProvider::class);
        $this->app->register(Providers\BusProvider::class);
        $this->app->register(Providers\FilesystemProvider::class);

        // Register last.
        $this->app->register(Providers\EventProvider::class);

        $this->installCommand();
    }

    public function boot()
    {
        // Immediately instantiate the object to work the magic.
        $environment = $this->app->make(Environment::class);
        // Now register it into ioc to make it globally available.
        $this->app->singleton(Environment::class, function () use ($environment) {
            return $environment;
        });
    }

    protected function installCommand()
    {
        $this->commands(InstallCommand::class);
    }

    protected function models()
    {
        $config = $this->app['config']['tenancy.models'];

        $this->app->bind(CustomerContact::class, $config['customer']);
        $this->app->bind(HostnameContact::class, $config['hostname']);
        $this->app->bind(WebsiteContract::class, $config['website']);
    }

    protected function repositories()
    {
        $this->app->singleton(
            Contracts\Repositories\HostnameRepository::class,
            Repositories\HostnameRepository::class
        );
        $this->app->singleton(
            Contracts\Repositories\WebsiteRepository::class,
            Repositories\WebsiteRepository::class
        );
        $this->app->singleton(
            Contracts\Repositories\CustomerRepository::class,
            Repositories\CustomerRepository::class
        );
    }

    protected function migrations()
    {
        $this->publishes([
            __DIR__ . '/../../assets/migrations/' => database_path('migrations')
        ], 'tenancy');
    }

    protected function registerConfiguration()
    {
        $this->publishes([
            __DIR__ . '/../../assets/configs/tenancy.php' => config_path('tenancy.php')
        ], 'tenancy');

        $this->mergeConfigFrom(
            __DIR__ . '/../../assets/configs/tenancy.php',
            'tenancy'
        );
    }
}
