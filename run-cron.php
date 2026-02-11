<?php
declare(strict_types=1);

use App\Cron\CronRunner;
use Dotenv\Dotenv;

$basePath = __DIR__;

require_once $basePath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $basePath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'functions.php';

$dotenv = Dotenv::createUnsafeImmutable($basePath);
$dotenv->load();

$runner = new CronRunner();
$tasks = require $basePath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'cron.php';

$task = $argv[1] ?? null;

if (is_null($task)) {
    echo 'Any task was provided....';
    exit();
}

foreach ($tasks as $name => $class) {
    if (class_exists($class)) {
        $runner->register($name, new $class());
    }
}

try {
    echo 'Executing task: ' . $task . ' - ' . date('Y-m-d\TH:i:s') . PHP_EOL;

    $runner->run($task);

    echo 'Task ' . $task . ' executed successfully - ' . date('Y-m-d\TH:i:s') . PHP_EOL;
} catch (InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
} catch (Throwable $e) {
    echo 'An error occurred while executing the task: ' . $e->getMessage() . PHP_EOL;
}
