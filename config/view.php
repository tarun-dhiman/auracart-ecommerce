<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. In serverless environments like Vercel,
    | this must point to a writable directory such as /tmp/storage/framework/views.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']))
            ? '/tmp/storage/framework/views'
            : storage_path('framework/views')
    ),

];
