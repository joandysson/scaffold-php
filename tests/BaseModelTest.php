<?php
require_once __DIR__ . '/../app/Config/functions.php';

use PHPUnit\Framework\TestCase;

class TestModel extends \App\Config\Model\BaseModel
{
    public static function callPrepareCreate(array &$data, string $table): string
    {
        return self::prepareQueryCreate($data, $table);
    }

    public static function callPrepareUpdate(array &$data, int $id, string $table): string
    {
        return self::prepareQueryUpdate($data, $id, $table);
    }
}

class BaseModelTest extends TestCase
{
    public function testPrepareQueryCreate(): void
    {
        $data = ['name' => 'foo', 'age' => 20];
        $sql = TestModel::callPrepareCreate($data, 'users');
        $this->assertSame('INSERT INTO users (name,age) VALUES (:name,:age)', $sql);
        $this->assertSame([':name' => 'foo', ':age' => 20], $data);
    }

    public function testPrepareQueryUpdate(): void
    {
        $data = ['name' => 'bar'];
        $sql = TestModel::callPrepareUpdate($data, 1, 'users');
        $this->assertSame('UPDATE users SET name = :name WHERE id = 1', $sql);
        $this->assertSame([':name' => 'bar'], $data);
    }
}
