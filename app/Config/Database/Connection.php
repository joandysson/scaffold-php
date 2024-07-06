<?php

namespace App\Config\Database;

use PDO;
use PDOException;


/**
 * class Connection
 */
abstract class Connection extends PDO
{
    /**
     * @var PDO $conn
     */
    protected static PDO $conn;

    function __construct()
    {
        self::connect();
    }

    /**
     * @return PDO
     */
    private static function connect(): PDO
    {
        try {
            $db = getenv();
            self::$conn = new PDO($db['DB_DRIVER'] . ":host=" . $db['DB_HOST'] . ";dbname=" . $db['DB_NAME'] . ";charset=utf8", $db['DB_USER'], $db['DB_PASSWORD']);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return self::$conn;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    /**
     * Undocumented function
     *
     * @param string $table
     * @param string $fields
     * @param string $data
     * @param array $arrayData
     * @return boolean
     */
    public static function insert(string $table, string $fields, string $data, array $arrayData = null): void
    {
        self::$conn->beginTransaction();
        try {
            $sql = "INSERT into {$table} ({$fields}) VALUES ({$data});";
            $stmt = self::$conn->prepare($sql);
            $stmt->execute($arrayData);

            self::$conn->commit();
        } catch (PDOException $e) {
            self::$conn->rollBack();
            echo $e->getMessage();
        }
    }
}
