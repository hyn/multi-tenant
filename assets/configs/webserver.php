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

return [

    'apache' => [
        'paths' => [
            /**
             * Location where to save tenant vhost configuration files to.
             *
             * @info In case you leave this unset, will fallback to default.
             * @see https://hyn.readme.io/v3.0/docs/webserverphp#section-apachepathstenant-files
             */
            'tenant-files' => null,
            /**
             * Location where vhost configuration files can be found.
             *
             * @see https://hyn.readme.io/v3.0/docs/webserverphp#section-apachepathsvhost-files
             */
            'vhost-files' => [
                '/etc/apache2/sites-enabled/'
            ],

            'actions' => [
                'exists' => '/etc/init.d/apache2',
                'test-config' => 'apache2ctl -t',
                'reload' => 'apache2ctl graceful'
            ]
        ]
    ]
];
