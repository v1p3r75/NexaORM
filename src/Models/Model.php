<?php

namespace Nexa\Models;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Nexa\Nexa;
use Nexa\Reflection\EntityReflection;
use ReflectionException;

trait Model
{

    private static ?Connection $connection = null;

    private static ?string $table = null;

    /**
     * @throws ReflectionException
     */
    public function __construct() {

        $reflection = new EntityReflection($this::class);
        self::$table = $reflection->getTable(Nexa::$inflector);
        self::$connection = Nexa::getConnection();
    }

    public static function find($id) {

    }

    public function findAll(string $select = "*") {

    }

    public function like() {

    }

    /**
     * @throws Exception
     */
    public static function insert(array $data): int
    {
        new static;

        $data = self::secure($data);

        return self::$connection->insert(self::$table, $data);

    }

    /**
     * @throws Exception
     */
    public static function update(array $data, array $conditions = []): int
    {
        new static;

        $data = self::secure($data);

        return self::$connection->update(self::$table, $data, $conditions);

    }

    /**
     * @throws Exception
     */
    public static function delete($id): int|string
    {
        new static;

        $id = self::secure($id);

        // TODO : get entity primary column

        return self::$connection->delete(self::$table, ['id' => $id]);

    }

    /**
     * @throws Exception
     */
    public static function deleteWhere(array $conditions): int|string
    {
        new static;

        $id = self::secure($conditions);

        return self::$connection->delete(self::$table, $conditions);

    }

    private static function secure(array | string $data) {

        if (is_array($data)) {

            return array_map(function($value) {

               return self::secure($value);
            }, $data);

        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

}