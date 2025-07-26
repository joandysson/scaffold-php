<?php
declare(strict_types=1);

namespace App\Config\Cron;

use App\Config\Cron\CronInterface;

final class ExempleCron implements CronInterface
{
    public function run(): void
    {
        echo "Running ExempleCron...\n";
    }
}
