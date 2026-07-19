<?php

declare(strict_types=1);

use Illuminate\Support\Str;

/**
 * Database Configuration for Laravel Application
 *
 * This configuration file contains database connection settings optimized for PHP 8.5
 * and modern Laravel practices. It includes security validations and optimized
 * driver configurations for better performance and compatibility.
 */

// Security validation - prevent insecure database credentials in production
$insecureCredentials = [
    'username' => ['root', 'admin', 'administrator', 'user', 'guest', 'www-data'],
    'password' => ['', 'password', '123456', 'admin', 'root', '123456789', 'qwerty', 'letmein'],
    'database' => ['forge', 'laravel', 'homestead', 'test', 'demo'],
];

$dbUsername = env('DB_USERNAME');
$dbPassword = env('DB_PASSWORD');
$dbDatabase = env('DB_DATABASE');

// Security check for production environments
// NOTE: We deliberately avoid using app()->environment() here because
// config files are loaded very early in the bootstrap process and the
// container binding for "env" may not yet be available. Instead we
// rely directly on the APP_ENV environment variable.
$appEnv = env('APP_ENV', 'production');

if (! in_array($appEnv, ['local', 'development', 'testing'], true)) {
    $hasInsecureCredentials = false;

    if ($dbUsername && in_array(strtolower($dbUsername), $insecureCredentials['username'], true)) {
        $hasInsecureCredentials = true;
    }

    if ($dbPassword && in_array(strtolower($dbPassword), $insecureCredentials['password'], true)) {
        $hasInsecureCredentials = true;
    }

    if ($dbDatabase && in_array(strtolower($dbDatabase), $insecureCredentials['database'], true)) {
        $hasInsecureCredentials = true;
    }

    if ($hasInsecureCredentials) {
        throw new RuntimeException(
            'Insecure database credentials detected in production environment. '.
            'Please update your .env file with secure credentials.'
        );
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => env('DB_BUSY_TIMEOUT', 30000),
            'journal_mode' => env('DB_JOURNAL_MODE', 'WAL'),
            'synchronous' => env('DB_SYNCHRONOUS', 'NORMAL'),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'pars_lian'),
            'username' => env('DB_USERNAME', 'pars_lian_user'),
            'password' => env('DB_PASSWORD'),
            'unix_socket' => env('DB_SOCKET'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'timezone' => env('DB_TIMEZONE', '+00:00'),
            'options' => extension_loaded('pdo_mysql') ? (function () {
                // Use new PDO\MySQL driver-specific attribute constants on PHP 8.5+ to avoid deprecations.
                if (class_exists(\PDO\MySQL::class)) {
                    return array_filter([
                        \PDO\MySQL::ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                        \PDO\MySQL::ATTR_SSL_VERIFY_SERVER_CERT => env('MYSQL_ATTR_SSL_VERIFY_SERVER_CERT', false),
                        \PDO\MySQL::ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone = '".env('DB_TIMEZONE', '+00:00')."'",
                        \PDO\MySQL::ATTR_USE_BUFFERED_QUERY => true,
                        \PDO\MySQL::ATTR_FOUND_ROWS => true,
                    ]);
                }

                // Fallback for older PHP versions where the legacy PDO::MYSQL_* constants are not deprecated.
                return array_filter([
                    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => env('MYSQL_ATTR_SSL_VERIFY_SERVER_CERT', false),
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone = '".env('DB_TIMEZONE', '+00:00')."'",
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::MYSQL_ATTR_FOUND_ROWS => true,
                ]);
            })() : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'pars_lian'),
            'username' => env('DB_USERNAME', 'pars_lian_user'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('DB_SCHEMA', 'public'),
            'sslmode' => env('DB_SSLMODE', 'prefer'),
            'sslcert' => env('DB_SSLCERT'),
            'sslkey' => env('DB_SSLKEY'),
            'sslrootcert' => env('DB_SSLROOTCERT'),
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'pars_lian'),
            'username' => env('DB_USERNAME', 'pars_lian_user'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'encrypt' => env('DB_ENCRYPT', false),
            'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', false),
            'application_name' => env('DB_APPLICATION_NAME', env('APP_NAME', 'Laravel')),
            'connection_timeout' => env('DB_CONNECTION_TIMEOUT', 30),
            'command_timeout' => env('DB_COMMAND_TIMEOUT', 0),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
            'persistent' => env('REDIS_PERSISTENT', false),
            'serializer' => env('REDIS_SERIALIZER', 'php'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
            'read_timeout' => env('REDIS_READ_TIMEOUT', 0.0),
            'retry_interval' => env('REDIS_RETRY_INTERVAL', 0),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
            'read_timeout' => env('REDIS_READ_TIMEOUT', 0.0),
            'retry_interval' => env('REDIS_RETRY_INTERVAL', 0),
        ],

        'session' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_SESSION_DB', 2),
            'read_timeout' => env('REDIS_READ_TIMEOUT', 0.0),
            'retry_interval' => env('REDIS_RETRY_INTERVAL', 0),
        ],

        'queue' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_QUEUE_DB', 3),
            'read_timeout' => env('REDIS_READ_TIMEOUT', 0.0),
            'retry_interval' => env('REDIS_RETRY_INTERVAL', 0),
        ],

    ],

];
