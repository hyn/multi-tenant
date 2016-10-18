<?php

return [
    'website' => [
        /**
         * Each website has a short random hash that identifies this entity
         * to the application. By default this id is randomized and fully
         * auto-generated. In case you want to force your own logic fi
         * when you need to have a better overview of the complete
         * tenant folder structure, disable this and implement
         * your own id generation logic.
         *
         * @see https://hyn.readme.io/v3.0/docs/tenancy#section-websitedisable-random-id
         */
        'disable-random-id' => false,

        /**
         * The random Id generator is responsible for creating the hash as mentioned
         * above. You can override what generator to use by modifying this value
         * in the configuration.
         *
         * @see https://hyn.readme.io/v3.0/docs/tenancy#section-websiterandom-id-generator
         * @warn This won't work if disable-random-id is true.
         */
        'random-id-generator' => Hyn\Tenancy\Generators\Uuid\ShaStringGenerator::class,
    ],
    'hostname' => [
        /**
         * If you want the multi tenant application to fall back to a default
         * hostname/website in case the requested hostname was not found
         * in the database, complete in detail the default hostname.
         *
         * @warn this must be a FQDN, these have no protocol or path!
         *
         * @see https://hyn.readme.io/v3.0/docs/tenancy#section-hostnamedefault
         */
        'default' => env('TENANCY_DEFAULT_HOSTNAME'),
        /**
         * The package is able to identify the requested hostname by itself,
         * disable to get full control (and responsibility) over hostname
         * identification. The hostname identification is needed to
         * set a specific website as currently active.
         *
         * @see src/Jobs/HostnameIdentification.php
         * @see https://hyn.readme.io/v3.0/docs/tenancy#section-hostnamauto-identification
         */
        'auto-identification' => env('TENANCY_AUTO_HOSTNAME_IDENTIFICATION', true),
    ],
    'db' => [
        /**
         * The default connection to use; this overrules the Laravel database.default
         * configuration setting. In Laravel this is normally configured to 'mysql'.
         * You can set a environment variable to override the default database
         * connection to - for instance - the tenant connection 'tenant'.
         *
         * @see https://hyn.readme.io/v3.0/docs/tenancy#section-dbdefault
         */
        'default' => env('TENANCY_DEFAULT_CONNECTION'),
        /**
         * Used to give names to the system and tenant database connections. By
         * default we configure 'system' and 'tenant'. The tenant connection
         * is set up automatically by this package.
         *
         * @see src/Database/Connection.php
         * @see https://hyn.readme.io/v3.0/docs/tenancy#section-dbsystem-connection-name
         * @see https://hyn.readme.io/v3.0/docs/tenancy#section-dbtenant-connection-name
         */
        'system-connection-name' => env('TENANCY_SYSTEM_CONNECTION_NAME', 'system'),
        'tenant-connection-name' => env('TENANCY_SYSTEM_CONNECTION_NAME', 'tenant'),

        /**
         * The tenant division mode specifies to what database websites will be
         * connecting. The default setup is to use a new database per tenant.
         * In case you prefer to use the same database with a table prefix,
         * set the mode to 'prefix'.
         *
         * @see src/Database/Connection.php
         * @see https://hyn.readme.io/v3.0/docs/tenancy#section-dbtenant-division-mode
         */
        'tenant-division-mode' => env('TENANCY_DATABASE_DIVISION_MODE', 'database'),

        /**
         * The database password generator takes care of creating a valid hashed
         * string used for tenants to connect to the specific database. Do
         * note that this will only work in 'division modes' that set up
         * a connection to a separate database.
         */
        'password-generator' => Hyn\Tenancy\Generators\Database\DefaultPasswordGenerator::class,
    ]
];
