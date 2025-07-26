<?php

namespace App\Config\Cron\Quiz;

use App\Config\Cron\CronInterface;

class ExempleCron implements CronInterface
{
    public function run(): void
    {
        echo "Running ExempleCron...\n";
    }
}
