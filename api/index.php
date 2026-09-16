<?php

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

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
        '/tmp/storage/bootstrap',
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

    // Bootstrap cache redirection
    if (file_exists(__DIR__ . '/../bootstrap/cache/packages.php') && !file_exists('/tmp/storage/bootstrap/packages.php')) {
        @copy(__DIR__ . '/../bootstrap/cache/packages.php', '/tmp/storage/bootstrap/packages.php');
    }
    if (file_exists(__DIR__ . '/../bootstrap/cache/services.php') && !file_exists('/tmp/storage/bootstrap/services.php')) {
        @copy(__DIR__ . '/../bootstrap/cache/services.php', '/tmp/storage/bootstrap/services.php');
    }

    $_ENV['APP_PACKAGES_CACHE'] = '/tmp/storage/bootstrap/packages.php';
    $_SERVER['APP_PACKAGES_CACHE'] = '/tmp/storage/bootstrap/packages.php';
    putenv('APP_PACKAGES_CACHE=/tmp/storage/bootstrap/packages.php');

    $_ENV['APP_SERVICES_CACHE'] = '/tmp/storage/bootstrap/services.php';
    $_SERVER['APP_SERVICES_CACHE'] = '/tmp/storage/bootstrap/services.php';
    putenv('APP_SERVICES_CACHE=/tmp/storage/bootstrap/services.php');

    // Forward execution to Laravel's front controller
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    if (!headers_sent()) {
        header('Content-Type: text/plain; charset=utf-8');
        http_response_code(500);
    }
    echo "ERROR CAUGHT IN API/INDEX.PHP:\n";
    echo $e->getMessage() . "\n";
    echo "In " . $e->getFile() . " on line " . $e->getLine() . "\n\n";
    echo $e->getTraceAsString();
}
