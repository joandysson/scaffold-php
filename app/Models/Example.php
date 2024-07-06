<?php

namespace App\Models;

use App\Config\Model\BaseModel;

/**
 * @package App\Models
 */
class ShortUrl extends BaseModel
{
    /**
     * @var string
     */
    private static $table = 'urls';

    /**
     * @return array
     */
    public function all(): array
    {
        return parent::queryRaw('SELECT * FROM ' . self::$table);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public static function byId(int $id): mixed
    {
        $result = parent::queryRaw('SELECT * FROM ' . self::$table . ' WHERE id = ? AND deleted_at IS NULL', [$id]);

        return current($result);
    }

    public function getByShortId(string $shorId): mixed
    {
        $result = parent::queryRaw(
            'SELECT * FROM ' . self::$table . ' WHERE short_id = ? AND deleted_at IS NULL',
            [$shorId]
        );

        return current($result);
    }

    public function getByShortIds(array $shorIds): mixed
    {
        $params = trim(str_repeat('?, ', count($shorIds)), ', ');
        $result = parent::queryRaw('SELECT * FROM ' . self::$table . ' WHERE short_id in ('. $params .')', $shorIds);

        return $result;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function create(array $data): mixed
    {
        $query = parent::prepareQueryCreate($data, self::$table);
        $result = parent::save($query, $data);

        return self::byId($result);
    }

    public static function update(int $id, array $data): mixed
    {
        $query = parent::prepareQueryUpdate($data, $id, self::$table);
        $result = parent::save($query, $data);

        return self::byId($result);
    }

    /**
     * @param int $id
     * @return void
     */
    public static function deleteByShortId(string $shortId): void
    {
        $date = date('Y-m-d H:i:s');

        $sql = 'UPDATE ' . self::$table;
        $sql .= ' SET deleted_at = :deleted_at, updated_at = :updated_at';
        $sql .= ' WHERE short_id = :id';

        self::save($sql, [
            ':deleted_at' => $date,
            ':updated_at' => $date,
            ':id' => $shortId
        ]);
    }
}
