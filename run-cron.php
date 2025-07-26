<?php
declare(strict_types=1);

use App\Cron\CronRunner;
use App\Cron\ExampleCron;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'functions.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable('.');
$dotenv->load();

$runner = new CronRunner();
$runner->register('ExampleCron', new ExampleCron());

if (!isset($argv[1])) {
    echo 'No task provided. Available tasks: ' . implode(', ', $runner->registeredTasks()) . PHP_EOL;
    exit(1);
}

try {
    $runner->run($argv[1]);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
    echo 'Available tasks: ' . implode(', ', $runner->registeredTasks()) . PHP_EOL;
    exit(1);
}
