<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = '';

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'batom_studio',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];



    //    /**
    //     * Sample database connection for Postgre.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => '',
    //        'hostname'   => 'localhost',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'database'   => 'ci4',
    //        'schema'     => 'public',
    //        'DBDriver'   => 'Postgre',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'utf8',
    //        'swapPre'    => '',
    //        'failover'   => [],
    //        'port'       => 5432,
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for SQLSRV.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => '',
    //        'hostname'   => 'localhost',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'database'   => 'ci4',
    //        'schema'     => 'dbo',
    //        'DBDriver'   => 'SQLSRV',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'utf8',
    //        'swapPre'    => '',
    //        'encrypt'    => false,
    //        'failover'   => [],
    //        'port'       => 1433,
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for OCI8.
    //     *
    //     * You may need the following environment variables:
    //     *   NLS_LANG                = 'AMERICAN_AMERICA.UTF8'
    //     *   NLS_DATE_FORMAT         = 'YYYY-MM-DD HH24:MI:SS'
    //     *   NLS_TIMESTAMP_FORMAT    = 'YYYY-MM-DD HH24:MI:SS'
    //     *   NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS'
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => 'localhost:1521/FREEPDB1',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'DBDriver'   => 'OCI8',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'AL32UTF8',
    //        'swapPre'    => '',
    //        'failover'   => [],
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    /**
     * This database connection is used when running PHPUnit database tests.
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => true,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'synchronous' => null,
        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        // Set filesPath at runtime to avoid invalid constant expressions
        // during PHP compile-time in some hosting environments.
        $this->filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

        // Respect environment selected for CI and allow overriding
        // the default database config from environment variables.
        // Support multiple naming conventions for Railway/containers compatibility:
        // - Dot-notation: database.default.hostname, database.default.port
        // - Underscore: DATABASE_HOST, DATABASE_PORT, DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD
        // - MySQL style: MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD
        // - Generic: DB_HOST, DB_PORT, DB_DATABASE, DB_USER, DB_PASSWORD
        
        $env = getenv('CI_ENVIRONMENT') ?: getenv('CI_ENV') ?: 'production';
        
        // DSN configuration
        $this->default['DSN'] = getenv('database.default.DSN') ?: 
                               getenv('DATABASE_DSN') ?: 
                               getenv('DB_DSN') ?: '';
        
        // Hostname - most critical for connection
        $this->default['hostname'] = getenv('database.default.hostname') ?: 
                                    getenv('DATABASE_HOST') ?: 
                                    getenv('MYSQLHOST') ?: 
                                    getenv('DB_HOST') ?: 
                                    getenv('RAILWAY_MYSQL_HOST') ?: 
                                    'localhost';
        
        // Username
        $this->default['username'] = getenv('database.default.username') ?: 
                                    getenv('DATABASE_USER') ?: 
                                    getenv('DATABASE_USERNAME') ?: 
                                    getenv('MYSQLUSER') ?: 
                                    getenv('DB_USER') ?: 
                                    getenv('DB_USERNAME') ?: 
                                    'root';
        
        // Password
        $this->default['password'] = getenv('database.default.password') ?: 
                                    getenv('DATABASE_PASSWORD') ?: 
                                    getenv('MYSQLPASSWORD') ?: 
                                    getenv('DB_PASSWORD') ?: 
                                    getenv('DB_PASS') ?: '';
        
        // Database name
        $this->default['database'] = getenv('database.default.database') ?: 
                                    getenv('DATABASE_NAME') ?: 
                                    getenv('DATABASE') ?: 
                                    getenv('MYSQLDATABASE') ?: 
                                    getenv('DB_DATABASE') ?: 
                                    getenv('RAILWAY_MYSQL_DATABASE') ?: 
                                    $this->default['database'];
        
        // DB Driver
        $this->default['DBDriver'] = getenv('database.default.DBDriver') ?: 
                                    getenv('DATABASE_DRIVER') ?: 
                                    getenv('DB_DRIVER') ?: 'MySQLi';
        
        // Port
        $portRaw = getenv('database.default.port') ?: 
                  getenv('DATABASE_PORT') ?: 
                  getenv('MYSQLPORT') ?: 
                  getenv('DB_PORT') ?: 
                  getenv('RAILWAY_MYSQL_PORT') ?: 
                  $this->default['port'];
        $this->default['port'] = (int) $portRaw;
        
        // Debug mode - disable in production
        $this->default['DBDebug'] = ($env !== 'production');

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}
