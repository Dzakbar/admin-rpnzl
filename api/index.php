<?php

$_ENV['LARAVEL_STORAGE_PATH'] = $_SERVER['LARAVEL_STORAGE_PATH'] = '/tmp/storage';

foreach ([
    '/tmp/storage/app',
    '/tmp/storage/app/private',
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
] as $path) {
    if (! is_dir($path)) {
        @mkdir($path, 0777, true);
    }
}

require __DIR__.'/../public/index.php';
