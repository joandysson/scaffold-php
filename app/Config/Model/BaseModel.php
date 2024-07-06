<?php

namespace App\Config\Model;

use App\Config\Database\Connection;

/**
 * class BaseModel
 * @package App\Config\Model
 */
class BaseModel extends Connection
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

    function __construct()
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
        try {
            $stmt = parent::$conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Undocumented function
     *
     * @param string $query
     * @param array $params
     * @return int|bool
     */
    protected static function save(string $query, array $params): int|bool
    {
        try {
            $stmt = parent::$conn->prepare($query);
            $stmt->execute($params);
            return parent::$conn->lastInsertId();
        } catch (\PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * @param array $data
     * @param string $table
     * @return string
     */
    protected static function prepareQueryCreate(array &$data, string $table): string
    {
        $dataPDO = self::gerateArrayDataPDO($data);
        $dataParams = implode(',', array_keys($data));
        $dataValues = implode(',', array_keys($dataPDO));
        $data = $dataPDO;

        return  "INSERT INTO {$table} ($dataParams) VALUES ($dataValues)";
    }

    /**
     * @param array $data
     * @param integer $id
     * @param string $table
     * @return string
     */
    protected static function prepareQueryUpdate(array &$data, int $id, string $table): string
    {
        $dataPDO = self::gerateArrayDataPDO($data);

        $values = array_combine(array_keys($data), array_keys($dataPDO));
        $queryValues = '';
        foreach ($values as $key => $value) {
            $queryValues .= "$key = $value, ";
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
        $sql = "DELETE FROM {$table} WHERE id = :id";
        self::save($sql, [':id' => $id]);
    }

    /**
     * @param array $data
     * @return array
     */
    private static  function gerateArrayDataPDO(array $data): array
    {
        foreach ($data  as $key => $value) {
            $dataPDO[":$key"] = $value;
        }

        return $dataPDO;
    }
}
