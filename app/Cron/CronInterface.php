<?php
declare(strict_types=1);

namespace App\Cron;

interface CronInterface
{
    public function run(): void;

}
