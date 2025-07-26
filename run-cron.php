<?php
declare(strict_types=1);

use App\Cron\CronInterface as CronCronInterface;
use App\Cron\CronRunner;
use App\Cron\ExampleCron;
use Dotenv\Dotenv;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'functions.php';

$dotenv = Dotenv::createUnsafeImmutable('.');
$dotenv->load();

$runner = new CronRunner();
$runner->register('ExampleCron', new ExampleCron());

if (!isset($argv[1])) {
    echo 'Any task was provided.';
    exit();
}

$task = $argv[1];

$tasks = require 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'cron.php';

$cron = isset($tasks[$task]) ? new $tasks[$task]() : "Task '{$task}' not found.";

if ($cron instanceof CronCronInterface) {

    echo 'Executing task: ' . $task . ' - ' . date('Y-m-d\TH:i:s') . PHP_EOL;

    $cron->run();

    echo 'Task ' . $task . ' executed successfully' . ' - ' . date('Y-m-d\TH:i:s') . PHP_EOL;

    exit();
}
