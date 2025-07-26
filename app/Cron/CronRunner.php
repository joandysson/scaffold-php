<?php
declare(strict_types=1);

namespace App\Cron;

use InvalidArgumentException;

final class CronRunner
{
    /**
     * @var array<string, CronInterface>
     */
    private array $tasks = [];

    public function register(string $name, CronInterface $task): void
    {
        $this->tasks[$name] = $task;
    }

    public function run(string $name): void
    {
        if (!isset($this->tasks[$name])) {
            throw new InvalidArgumentException("Task '{$name}' not found.");
        }

        echo 'Executing task: ' . $name . ' - ' . date('Y-m-d\TH:i:s') . PHP_EOL;
        $this->tasks[$name]->run();
        echo 'Task ' . $name . ' executed successfully - ' . date('Y-m-d\TH:i:s') . PHP_EOL;
    }

    /**
     * @return string[]
     */
    public function registeredTasks(): array
    {
        return array_keys($this->tasks);
    }
}
