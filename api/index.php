<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "DIAGNOSTIC: vendor/autoload.php DOES NOT EXIST on Vercel!\n";
        echo "Files in root: " . implode(', ', scandir(__DIR__ . '/..')) . "\n";
        exit;
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
} catch (\Throwable $e) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "ERROR CAUGHT IN API/INDEX.PHP:\n";
    echo $e->getMessage() . "\n";
    echo "In " . $e->getFile() . " on line " . $e->getLine() . "\n\n";
    echo $e->getTraceAsString();
}
