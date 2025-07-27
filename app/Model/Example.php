<?php
declare(strict_types=1);

namespace App\Model;

use App\Config\Model\BaseModel;

/**
 * @package App\Model
 */
class Example extends BaseModel
{
    /**
     * @var string
     */
    private static string $table = 'example';

    /**
     * @return array
     */
    public function all(): array
    {
        return parent::queryRaw('SELECT * FROM ' . self::$table);
    }
}
