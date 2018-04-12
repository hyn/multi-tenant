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

namespace Hyn\Tenancy\Tests\Database;

use Hyn\Tenancy\Tests\Test;
use Illuminate\Contracts\Foundation\Application;

class MultiDatabaseTest extends Test
{
    protected function duringSetUp(Application $app)
    {
        // let's configure mysql2
        $config = $app['config']->get('database.connections.mysql', []);
        $config['host'] = 'mysql2';
        $app['config']->set('database.connections.mysql2', $config);

        $this->setUpWebsites();
    }

    public function allow_writing_to_secondary_database()
    {
        $this->website->managed_by_database_connection = 'mysql2';

        $this->website->save();

        $this->assertTrue($this->website->save());
    }
}
