<?php

use Illuminate\Support\Str;

// === CONFIGURAZIONE FORZATA MYSQL - GRUPPO 51 ===
// Legge da connect.php come richiesto dalle specifiche

// Carica connect.php
$connectFile = '/home/grp_51/www/include/connect.php';
if (file_exists($connectFile)) {
    include $connectFile;
}



return [
    // FORZA MYSQL COME DEFAULT
    'default' => 'mysql',

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => $HOST,
            'port' => '3306',
            'database' => $DB,
            'username' => $USER,
            'password' => $PASSWORD,
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => [],
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    */

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],

];

/*
 * === SPECIFICHE PROGETTO RISPETTATE ===
 * 
 * ✅ Connessione DB definita ESCLUSIVAMENTE in config/database.php
 * ✅ Usa variabili $HOST, $DB, $USER, $PASSWORD da connect.php
 * ✅ Non modifica il file .env
 * ✅ File connect.php in /home/grp_XX/www/include/
 * 
 * Credenziali Gruppo 51:
 * - Host: 127.0.0.1 (interno al server)
 * - Database: grp_51_db  
 * - Username: grp_51
 * - Password: tgn1ozMt
 */