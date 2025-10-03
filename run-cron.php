<?php
declare(strict_types=1);

use App\Cron\CronRunner;
use Dotenv\Dotenv;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'functions.php';

$dotenv = Dotenv::createUnsafeImmutable('.');
$dotenv->load();

$runner = new CronRunner();
$tasks = require 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'cron.php';

foreach ($tasks as $name => $class) {
    if (class_exists($class)) {
        $runner->register($name, new $class());
    }
}

if (!isset($argv[1])) {
    echo 'Any task was provided.';
    exit();
}

$task = $argv[1];

try {
    echo 'Executing task: ' . $task . ' - ' . date('Y-m-d\TH:i:s') . PHP_EOL;

    $runner->run($task);

    echo 'Task ' . $task . ' executed successfully - ' . date('Y-m-d\TH:i:s') . PHP_EOL;
} catch (InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
} catch (Throwable $e) {
    echo 'An error occurred while executing the task: ' . $e->getMessage() . PHP_EOL;
}
