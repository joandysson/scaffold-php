<?php
declare(strict_types=1);

namespace App\Cron;

use App\Cron\CronInterface;

final class ExampleCron implements CronInterface
{
    public function run(): void
    {
        echo "Running ExampleCron...\n";
    }
}
