<?php

namespace Hyn\Tenancy\Commands\Seeds;

use Hyn\Tenancy\Traits\TenantDatabaseCommandTrait;
use Illuminate\Database\ConnectionResolverInterface as Resolver;

class SeedCommand extends \Illuminate\Database\Console\Seeds\SeedCommand
{
    use TenantDatabaseCommandTrait;
    /**
     * @var \Hyn\Tenancy\Contracts\WebsiteRepositoryContract
     */
    protected $website;

    /**
     * SeedCommand constructor.
     *
     * @param Resolver $resolver
     */
    public function __construct(Resolver $resolver)
    {
        parent::__construct($resolver);
        $this->website = app('Hyn\Tenancy\Contracts\WebsiteRepositoryContract');
    }

    /**
     * Fires the command.
     */
    public function fire()
    {
        // if no tenant option is set, simply run the native laravel seeder
        if (! $this->option('tenant')) {
            return parent::fire();
        }

        if (! $this->option('force') && ! $this->confirmToProceed()) {
            $this->error('Stopped no confirmation and not forced.');

            return;
        }

        $websites = $this->getWebsitesFromOption();

        // forces database to tenant
        if (! $this->option('database')) {
            $this->input->setOption('database', 'tenant');
        }

        foreach ($websites as $website) {
            $this->info("Seeding for {$website->id}: {$website->present()->name}");

            $website->database->setCurrent();

            $this->resolver->setDefaultConnection($website->database->name);

            $this->getSeeder()->run();
        }
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(
            parent::getOptions(),
            $this->getTenantOption()
        );
    }
}
