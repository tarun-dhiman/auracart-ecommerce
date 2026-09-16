<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');
echo "APP_KEY: " . substr(config('app.key'), 0, 15) . "...\n";
echo "SESSION_DRIVER: " . config('session.driver') . "\n";
echo "SESSION_COOKIE: " . config('session.cookie') . "\n";
echo "SESSION_SECURE: " . var_export(config('session.secure'), true) . "\n";
echo "SESSION_DOMAIN: " . var_export(config('session.domain'), true) . "\n";
echo "SESSION_SAME_SITE: " . var_export(config('session.same_site'), true) . "\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "DB_CONNECTION: " . config('database.default') . "\n";
echo "DB_HOST: " . config('database.connections.mysql.host') . "\n";
try {
    echo "DB Sessions Table Count: " . \Illuminate\Support\Facades\DB::table('sessions')->count() . "\n";
} catch (\Throwable $e) {
    echo "DB Sessions Table ERROR: " . $e->getMessage() . "\n";
}

