<?php

$storagePath = '/tmp/storage';
$cachePath = '/tmp/bootstrap/cache';

function normalizeVercelApiRequestUri(): void
{
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';

    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';

    if ($requestUri === '' || str_starts_with($requestUri, '/api/')) {
        return;
    }

    $path = parse_url($requestUri, PHP_URL_PATH) ?: '';
    $query = parse_url($requestUri, PHP_URL_QUERY);
    $firstSegment = explode('/', ltrim($path, '/'))[0] ?? '';

    if (! in_array($firstSegment, ['company-profile', 'bookings', 'testimonials', 'schedules', 'user'], true)) {
        return;
    }

    $_SERVER['REQUEST_URI'] = '/api'.$path.($query ? '?'.$query : '');
}

normalizeVercelApiRequestUri();

$_ENV['LARAVEL_STORAGE_PATH'] = $_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;
$_ENV['APP_SERVICES_CACHE'] = $_SERVER['APP_SERVICES_CACHE'] = $cachePath.'/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $_SERVER['APP_PACKAGES_CACHE'] = $cachePath.'/packages.php';
$_ENV['APP_CONFIG_CACHE'] = $_SERVER['APP_CONFIG_CACHE'] = $cachePath.'/config.php';
$_ENV['APP_ROUTES_CACHE'] = $_SERVER['APP_ROUTES_CACHE'] = $cachePath.'/routes-v7.php';
$_ENV['APP_EVENTS_CACHE'] = $_SERVER['APP_EVENTS_CACHE'] = $cachePath.'/events.php';

foreach ([
    $cachePath,
    $storagePath.'/app',
    $storagePath.'/app/private',
    $storagePath.'/app/public',
    $storagePath.'/framework/cache',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
] as $path) {
    if (! is_dir($path)) {
        @mkdir($path, 0777, true);
    }
}

require __DIR__.'/../public/index.php';
