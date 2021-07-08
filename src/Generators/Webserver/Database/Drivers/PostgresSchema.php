<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://tenancy.dev
 * @see https://github.com/hyn/multi-tenant
 */

namespace Hyn\Tenancy\Generators\Webserver\Database\Drivers;

use Hyn\Tenancy\Database\Connection;
use Hyn\Tenancy\Events\Websites\Updated;
use Hyn\Tenancy\Exceptions\GeneratorFailedException;
use Illuminate\Database\Connection as IlluminateConnection;
use Illuminate\Support\Arr;

class PostgresSchema extends PostgreSQL
{
    protected function createDatabase(IlluminateConnection $connection, array $config)
    {
        return $connection->statement("CREATE SCHEMA IF NOT EXISTS \"{$config['schema']}\"");
    }

    protected function grantPrivileges(IlluminateConnection $connection, array $config)
    {
        $privileges = config('tenancy.db.tenant-database-user-privileges', null) ?? 'ALL PRIVILEGES';

        return $connection->statement("GRANT $privileges ON SCHEMA \"{$config['schema']}\" TO \"{$config['username']}\"");
    }

    /**
     * @param Updated    $event
     * @param array      $config
     * @param Connection $connection
     * @return bool
     * @throws GeneratorFailedException
     */
    public function updated(Updated $event, array $config, Connection $connection): bool
    {
        $uuid = Arr::get($event->dirty, 'uuid');

        if (!$connection->system($event->website)->statement("ALTER SCHEMA \"$uuid\" RENAME TO \"{$config['schema']}\"")) {
            throw new GeneratorFailedException("Could not rename schema {$config['schema']}, the statement failed.");
        }

        return true;
    }

    protected function dropDatabase(IlluminateConnection $connection, array $config)
    {
        return $connection->statement("DROP SCHEMA IF EXISTS \"{$config['schema']}\" CASCADE");
    }
}
