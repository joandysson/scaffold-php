<?php
declare(strict_types=1);

namespace App\Config\Database;

use PDO;
use PDOException;
use App\Config\Log\Log;
/**
 * class Connection
 */
abstract class Connection
{
    /**
     * @var PDO $conn
     */
    protected static ?PDO $conn = null;

    public function __construct()
    {
    }

    /**
     * @return void
     */
    private static function connect(): void
    {
        if (self::$conn !== null) {
            return;
        }

        try {
            $db = getenv();
            $options = [
                PDO::ATTR_TIMEOUT => 2,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_general_ci'
            ];

            self::$conn = new PDO(
                $db['DB_DRIVER'] . ':host=' . $db['DB_HOST'] . ';dbname=' . $db['DB_NAME'] . ';charset=utf8',
                $db['DB_USER'],
                $db['DB_PASSWORD'],
                $options
            );

            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            Log::error($e->getMessage(), $e);
            if (getenv('APP_DEBUG') === 'true') {
                echo $e->getMessage();
            }
        }
    }

    protected static function getConnection(): PDO
    {
        if (self::$conn === null) {
            self::connect();
        }
        return self::$conn;
    }

    public static function insert(string $table, string $fields, string $data, array $arrayData): void
    {
        $conn = self::getConnection();
        $conn->beginTransaction();
        try {
            $sql = "INSERT into {$table} ({$fields}) VALUES ({$data});";
            $stmt = $conn->prepare($sql);
            $stmt->execute($arrayData);

            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            Log::error($e->getMessage(), $e);
            if (getenv('APP_DEBUG') === 'true') {
                echo $e->getMessage();
            }
        }
    }
}
