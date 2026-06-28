<?php

$storagePath = '/tmp/storage';
$cachePath = '/tmp/bootstrap/cache';

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
