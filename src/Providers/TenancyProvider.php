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
        $this->mergeConfigFrom(
            __DIR__ . '/../../assets/configs/tenancy.php',
            'tenancy'
        );

        $this->registerModels();

        $this->registerRepositories();

        $this->registerProviders();
    }

    public function boot()
    {
        $this->bootPublishes();

        $this->bootInstallCommand();

        $this->bootEnvironment();
    }

    public function provides()
    {
        return [Environment::class];
    }

    protected function registerModels()
    {
        $config = $this->app['config']['tenancy.models'];

        $this->app->bind(CustomerContact::class, $config['customer']);
        $this->app->bind(HostnameContact::class, $config['hostname']);
        $this->app->bind(WebsiteContract::class, $config['website']);
    }

    protected function registerRepositories()
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

    protected function registerProviders()
    {
        $this->app->register(Providers\ConfigurationProvider::class);
        $this->app->register(Providers\PasswordProvider::class);
        $this->app->register(Providers\ConnectionProvider::class);
        $this->app->register(Providers\UuidProvider::class);
        $this->app->register(Providers\BusProvider::class);
        $this->app->register(Providers\FilesystemProvider::class);

        // Register last.
        $this->app->register(Providers\EventProvider::class);
    }

    protected function bootPublishes()
    {
        $this->publishes([
            __DIR__ . '/../../assets/configs/tenancy.php' => config_path('tenancy.php')
        ], 'tenancy');

        $this->publishes([
            __DIR__.'/../../assets/migrations/create_customers_table.php' => database_path('migrations/2017_01_01_000000_create_customers_table.php'),
        ], 'tenancy');

        $this->publishes([
            __DIR__.'/../../assets/migrations/create_websites_table.php' => database_path('migrations/2017_01_01_000002_create_websites_table.php'),
        ], 'tenancy');

        $this->publishes([
            __DIR__.'/../../assets/migrations/create_hostnames_table.php' => database_path('migrations/2017_01_01_000004_create_hostnames_table.php'),
        ], 'tenancy');
    }

    protected function bootInstallCommand()
    {
        $this->commands(InstallCommand::class);
    }

    protected function bootEnvironment()
    {
        // Immediately instantiate the object to work the magic.
        $environment = $this->app->make(Environment::class);
        // Now register it into ioc to make it globally available.
        $this->app->singleton(Environment::class, function () use ($environment) {
            return $environment;
        });
    }
}
