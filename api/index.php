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

// Ensure Vercel read-only filesystem defaults point to /tmp when not explicitly set
$vercelDefaults = [
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
];

foreach ($vercelDefaults as $key => $value) {
    $current = getenv($key);
    if (($current === false || $current === '') && (! isset($_ENV[$key]) || $_ENV[$key] === '') && (! isset($_SERVER[$key]) || $_SERVER[$key] === '')) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Forward Vercel requests to Laravel's public/index.php
require __DIR__.'/../public/index.php';
