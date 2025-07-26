<?php

use App\Config\Cron\CronInterface;
use App\Config\Cron\Quiz\ExempleCron;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'functions.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable('.');
$dotenv->load();

if (!isset($argv[1])) {
    echo 'Any task was provided.';
}

$task = $argv[1];

$cron = match ($task) {
    'ExempleCron' => new ExempleCron(),
    default => "Task '{$task}' not found."
};

if ($cron instanceof CronInterface) {

    echo 'Executing task: ' . $task . ' - ' . date('Y-m-d\TH:i:s') . PHP_EOL;

    $cron->run();

    echo 'Task ' . $task . ' executed successfully' . ' - ' . date('Y-m-d\TH:i:s') . PHP_EOL;

    exit();
}

echo $cron;
