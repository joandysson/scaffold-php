<?php
declare(strict_types=1);

namespace App\Config\Model;

use App\Config\Database\Connection;
use PDO;

/**
 * class BaseModel
 * @package App\Config\Model
 */
abstract class BaseModel extends Connection
{
    /**
     * @var string $groupBy
     */
    public string $groupBy;

    /**
     * @var int $limit
     */
    public int $limit = 1;

    /**
     * @var string $notIn
     */
    public string $notIn;

    /**
     * @var string $order
     */
    public string $order = 'DESC';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $variable
     * @param mixed $value
     * @return BaseModel
     */
    public function set(string $variable, mixed $value): BaseModel
    {
        $this->$variable = $value;
        return $this;
    }

    /**
     * @param string $query
     * @param array $params
     * @return array
     */
    protected static function queryRaw(string $query, array $params = []): array
    {
        $conn = parent::getConnection();
        $stmt = $conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute an INSERT query and return the inserted ID.
     *
     * @param string $query
     * @param array $params
     * @return string
     */
    protected static function save(string $query, array $params): string
    {
        $conn = parent::getConnection();
        $stmt = $conn->prepare($query);
        $stmt->execute($params);

        return $conn->lastInsertId();
    }

    protected static function execUpdate(string $query, array $params): int
    {
        $conn = parent::getConnection();
        $stmt = $conn->prepare($query);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    /**
     * @param array $data
     * @param string $table
     * @return string
     */
    protected static function prepareQueryCreate(array &$data, string $table): string
    {
        $table = self::sanitizeIdentifier($table);
        $dataPDO = self::generateArrayDataPDO($data);
        $sanitizedParams = [];
        foreach (array_keys($data) as $param) {
            $sanitizedParams[] = self::sanitizeIdentifier($param);
        }
        $dataParams = implode(',', $sanitizedParams);
        $dataValues = implode(',', array_keys($dataPDO));
        $data = $dataPDO;

        return "INSERT INTO {$table} ($dataParams) VALUES ($dataValues)";
    }


    /**
     * @param array $data
     * @param string $table
     * @return string
     */
    protected static function prepareQueryReplace(array &$data, string $table): string
    {
        $table = self::sanitizeIdentifier($table);
        $dataPDO = self::generateArrayDataPDO($data);
        $dataValues = [];
        foreach ($dataPDO as $key => $value) {
            $value = $key;
            $key = str_replace(':', '', $key);
            $key = self::sanitizeIdentifier($key);
            $dataValues[] = "{$key} = {$value}";
        }

        $dataValues = implode(', ', $dataValues);

        $data = $dataPDO;

        return "REPLACE INTO {$table} SET $dataValues";
    }

    /**
     * @param array $data
     * @param integer $id
     * @param string $table
     * @return string
     */
    protected static function prepareQueryUpdate(array &$data, int $id, string $table): string
    {
        $table = self::sanitizeIdentifier($table);
        $dataPDO = self::generateArrayDataPDO($data);

        $values = array_combine(array_keys($data), array_keys($dataPDO));
        $queryValues = '';
        foreach ($values as $key => $value) {
            $sanitizedKey = self::sanitizeIdentifier($key);
            $queryValues .= "$sanitizedKey = $value, ";
        }

        $queryValues = substr($queryValues, 0, -2);
        $data = $dataPDO;

        return  "UPDATE {$table} SET $queryValues WHERE id = $id";
    }

    /**
     * @param string $table
     * @param integer $id
     * @return void
     */
    protected static function deleteById(string $table, int $id): void
    {
        $table = self::sanitizeIdentifier($table);
        $sql = "DELETE FROM {$table} WHERE id = :id";
        self::save($sql, [':id' => $id]);
    }

    /**
     * @param array $data
     * @return array
     */
    private static function generateArrayDataPDO(array $data): array
    {
        foreach ($data as $key => $value) {
            $sanitizedKey = self::sanitizeIdentifier($key);
            $dataPDO[":$sanitizedKey"] = $value;
        }

        return $dataPDO ?? [];
    }

    protected static function prepareQueryCreateBulk(array &$data, string $table): string
    {
        $table = self::sanitizeIdentifier($table);
        $sanitizedParams = [];
        foreach (array_keys($data[0]) as $param) {
            $sanitizedParams[] = self::sanitizeIdentifier($param);
        }
        $dataParams = implode(',', $sanitizedParams);
        $dataValues = [];
        $newData = [];

        foreach ($data as $key => $value) {
            $dataPDO = self::generateArrayDataPDO($value);

            $newDataPDO = [];
            foreach ($dataPDO as $key2 => $value2) {
                $newDataPDO[$key2 . ($key + 1)] = $value2;
            }

            $dataValues[] = '(' . implode(',', array_keys($newDataPDO)) . ')';

            $newData = [...$newData, ...$newDataPDO];
        }

        $data = $newData;
        $dataValues = implode(',', $dataValues);

        return "INSERT INTO {$table} ($dataParams) VALUES {$dataValues}";
    }

    /**
     * Sanitize a table or column identifier to contain only alphanumeric characters and underscores.
     */
    private static function sanitizeIdentifier(string $identifier): string
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
            throw new \InvalidArgumentException('Invalid identifier');
        }

        return $identifier;
    }
}
