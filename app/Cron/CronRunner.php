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
        if (!array_key_exists($name, $this->tasks)) {
            throw new InvalidArgumentException("Task '{$name}' not found.");
        }

        $task = $this->tasks[$name];

        if (!$task instanceof CronInterface) {
            throw new InvalidArgumentException("Task '{$name}' is not a valid cron task.");
        }

        $task->run();
    }

    /**
     * @return string[]
     */
    public function registeredTasks(): array
    {
        return array_keys($this->tasks);
    }
}
