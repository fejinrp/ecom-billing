<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. By default, bcrypt is used; however,
    | you remain free to modify this option if you wish.
    |
    */

    'driver' => 'md5_bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options for the bcrypt driver.
    | This will control the "work factor" or "rounds" for the driver.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => true,
    ],

];
