<?php

// Force debug mode to reveal any runtime issues
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';

// Fallback APP_KEY if missing from Vercel environment variables
if (empty($_ENV['APP_KEY']) && empty(getenv('APP_KEY'))) {
    putenv('APP_KEY=base64:4cvTyTyZpzyANCutIJSgLknAo/lOLD07vuWjQNdtwLM=');
    $_ENV['APP_KEY'] = 'base64:4cvTyTyZpzyANCutIJSgLknAo/lOLD07vuWjQNdtwLM=';
    $_SERVER['APP_KEY'] = 'base64:4cvTyTyZpzyANCutIJSgLknAo/lOLD07vuWjQNdtwLM=';
}

// Ensure /tmp storage paths exist for Vercel Serverless environment
$dirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Redirect storage path to /tmp/storage
$_ENV['LARAVEL_STORAGE_PATH'] = '/tmp/storage';
$_SERVER['LARAVEL_STORAGE_PATH'] = '/tmp/storage';
putenv('LARAVEL_STORAGE_PATH=/tmp/storage');

$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Forward execution to Laravel's front controller
require __DIR__ . '/../public/index.php';
