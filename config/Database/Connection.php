<?php
declare(strict_types=1);

namespace Config\Database;

use PDO;
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

        $db = getenv();
        $options = [
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => true,
        ];

        self::$conn = new PDO(
            $db['DB_DRIVER'] . ':host=' . $db['DB_HOST'] . ';dbname=' . $db['DB_NAME']
            . ';charset=utf8mb4',
            $db['DB_USER'],
            $db['DB_PASSWORD'],
            $options
        );

        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$conn->exec('SET collation_connection = utf8mb4_general_ci');
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

        $sql = "INSERT into {$table} ({$fields}) VALUES ({$data});";
        $stmt = $conn->prepare($sql);
        $stmt->execute($arrayData);

        $conn->commit();
    }
}
