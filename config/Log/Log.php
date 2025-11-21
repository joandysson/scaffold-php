<?php
declare(strict_types=1);

namespace App\Config\Log;

class Log
{
    private static string $driver;
    private static string $filePath;
    private static bool $initialized = false;

    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$driver = getenv('LOG_DRIVER') ?: 'file';
        self::$filePath = 'storage/logs/app.log';
        if (self::$driver === 'file') {
            $dir = dirname(self::$filePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            } elseif (!is_writable($dir)) {
                chmod($dir, 0755);
            }
        }
        self::$initialized = true;
    }

    public static function info(string $message): void
    {
        self::write('INFO', $message);
    }

    public static function error(string $message, ?\Throwable $exception = null): void
    {
        if ($exception !== null) {
            $message .= ' | ' . $exception->getMessage()
                . ' in ' . $exception->getFile()
                . ':' . $exception->getLine();
        }
        self::write('ERROR', $message);
    }

    private static function write(string $level, string $message): void
    {
        self::init();
        $log = '[' . date('Y-m-d H:i:s') . "] {$level}: {$message}" . PHP_EOL;
        if (self::$driver === 'error_log') {
            \error_log($log);
            return;
        }
        file_put_contents(self::$filePath, $log, FILE_APPEND);
    }
}
