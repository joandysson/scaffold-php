<?php
use PHPUnit\Framework\TestCase;
use App\Cron\CronRunner;
use App\Cron\CronInterface;

class DummyCron implements CronInterface
{
    public bool $ran = false;
    public function run(): void
    {
        $this->ran = true;
    }
}

class CronRunnerTest extends TestCase
{
    public function testRunExecutesTask(): void
    {
        $cron = new DummyCron();
        $runner = new CronRunner();
        $runner->register('dummy', $cron);
        $runner->run('dummy');
        $this->assertTrue($cron->ran);
    }

    public function testRunThrowsForMissingTask(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $runner = new CronRunner();
        $runner->run('missing');
    }
}
