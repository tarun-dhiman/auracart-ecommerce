<?php

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Ensure HTTPS is recognized in Vercel Serverless environment
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
}

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

    $_ENV['APP_MAINTENANCE_DRIVER'] = 'file';
    $_SERVER['APP_MAINTENANCE_DRIVER'] = 'file';
    putenv('APP_MAINTENANCE_DRIVER=file');

    $_ENV['SESSION_LIFETIME'] = '120';
    $_SERVER['SESSION_LIFETIME'] = '120';
    putenv('SESSION_LIFETIME=120');

    $_ENV['SESSION_DRIVER'] = 'database';
    $_SERVER['SESSION_DRIVER'] = 'database';
    putenv('SESSION_DRIVER=database');

    $_ENV['SESSION_COOKIE'] = 'auracart_session';
    $_SERVER['SESSION_COOKIE'] = 'auracart_session';
    putenv('SESSION_COOKIE=auracart_session');

    // Direct bootstrap cache files to writable /tmp directory
    $_ENV['APP_PACKAGES_CACHE'] = '/tmp/storage/bootstrap/packages.php';
    $_SERVER['APP_PACKAGES_CACHE'] = '/tmp/storage/bootstrap/packages.php';
    putenv('APP_PACKAGES_CACHE=/tmp/storage/bootstrap/packages.php');

    if (!file_exists('/tmp/storage/bootstrap/packages.php')) {
        $cleanManifest = [
            'laravel/tinker' => [
                'providers' => ['Laravel\\Tinker\\TinkerServiceProvider'],
            ],
            'nesbot/carbon' => [
                'providers' => ['Carbon\\Laravel\\ServiceProvider'],
            ],
        ];
        @file_put_contents('/tmp/storage/bootstrap/packages.php', '<?php return ' . var_export($cleanManifest, true) . ';');
    }

    $_ENV['APP_SERVICES_CACHE'] = '/tmp/storage/bootstrap/services.php';
    $_SERVER['APP_SERVICES_CACHE'] = '/tmp/storage/bootstrap/services.php';
    putenv('APP_SERVICES_CACHE=/tmp/storage/bootstrap/services.php');

    $_ENV['APP_CONFIG_CACHE'] = '/tmp/storage/bootstrap/config.php';
    $_SERVER['APP_CONFIG_CACHE'] = '/tmp/storage/bootstrap/config.php';
    putenv('APP_CONFIG_CACHE=/tmp/storage/bootstrap/config.php');

    $_ENV['APP_ROUTES_CACHE'] = '/tmp/storage/bootstrap/routes.php';
    $_SERVER['APP_ROUTES_CACHE'] = '/tmp/storage/bootstrap/routes.php';
    putenv('APP_ROUTES_CACHE=/tmp/storage/bootstrap/routes.php');

    $_ENV['APP_EVENTS_CACHE'] = '/tmp/storage/bootstrap/events.php';
    $_SERVER['APP_EVENTS_CACHE'] = '/tmp/storage/bootstrap/events.php';
    putenv('APP_EVENTS_CACHE=/tmp/storage/bootstrap/events.php');

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
