<?php

use App\Config\Router\Router;
use function Sentry\captureException;
use function Sentry\init;
use App\Config\Log\Log;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'functions.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname(__DIR__));
$dotenv->load();

if (getenv('APP_STAGE') === 'PROD') {
    init([
        'dsn' => getenv('SENTRY_DNS'),
        // Specify a fixed sample rate
        'traces_sample_rate' => 1.0,
        // Set a sampling rate for profiling - this is relative to traces_sample_rate
        'profiles_sample_rate' => 1.0,
    ]);
}

errorReporting(getenv('APP_DEBUG') === 'true');

try {
    new Router;
} catch (\Throwable $exception) {
    if (getenv('APP_STAGE') === 'PROD') {
        captureException($exception);
    }

    Log::error($exception->getMessage());
    if (getenv('APP_DEBUG') === 'true') {
        echo $exception->getMessage();
    }
}
