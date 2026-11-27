<?php

// Fix SCRIPT_NAME/PHP_SELF so Symfony does not strip "/api" from /api/* routes
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Ensure writable /tmp storage structure exists on Vercel's read-only filesystem
$storagePath = '/tmp/storage';
foreach ([
    $storagePath.'/app/public',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

$setEnvDefault = static function (string $key, string $value): void {
    $current = getenv($key);
    if (
        ($current === false || $current === '')
        && (! isset($_ENV[$key]) || $_ENV[$key] === '')
        && (! isset($_SERVER[$key]) || $_SERVER[$key] === '')
    ) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
};

// Ensure Vercel read-only filesystem defaults point to /tmp when not explicitly set
$vercelDefaults = [
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'APP_CONFIG_CACHE' => '/tmp/config.php',
    'APP_EVENTS_CACHE' => '/tmp/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_ROUTES_CACHE' => '/tmp/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'VIEW_COMPILED_PATH' => $storagePath.'/framework/views',
    'CACHE_DRIVER' => 'array',
    'CACHE_STORE' => 'array',
    'LOG_CHANNEL' => 'stderr',
    'SESSION_DRIVER' => 'cookie',
    // Demo fallback key when APP_KEY is not configured in Vercel Dashboard
    'APP_KEY' => 'base64:FjfOfMiUDdv4iiebYmRV+0ahEq0t3/pCAk2XUGKn+O4=',
];

foreach ($vercelDefaults as $key => $value) {
    $setEnvDefault($key, $value);
}

// Derive APP_URL from current Vercel host if not explicitly set
$host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? getenv('VERCEL_URL') ?: '';
if ($host !== '') {
    $setEnvDefault('APP_URL', 'https://'.preg_replace('#^https?://#', '', $host));
}

// Fall back to pre-seeded SQLite demo database in /tmp when no external DB is configured
$hasExternalDb = (getenv('DB_HOST') !== false && getenv('DB_HOST') !== '' && getenv('DB_HOST') !== '127.0.0.1')
    || (getenv('DB_URL') !== false && getenv('DB_URL') !== '');

if (! $hasExternalDb) {
    $demoSource = __DIR__.'/../database/vercel-demo.sqlite';
    $tmpDatabase = '/tmp/database.sqlite';

    if (is_file($demoSource) && ! is_file($tmpDatabase)) {
        copy($demoSource, $tmpDatabase);
    }

    if (is_file($tmpDatabase)) {
        putenv('DB_CONNECTION=sqlite');
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_CONNECTION'] = 'sqlite';

        putenv("DB_DATABASE={$tmpDatabase}");
        $_ENV['DB_DATABASE'] = $tmpDatabase;
        $_SERVER['DB_DATABASE'] = $tmpDatabase;
    }
}

// Forward Vercel requests to Laravel's public/index.php
require __DIR__.'/../public/index.php';
