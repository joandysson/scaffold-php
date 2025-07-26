<?php
declare(strict_types=1);

namespace App\Config\Cron;

interface CronInterface
{
    public function run(): void;

}
