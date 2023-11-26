<?php

namespace Nexa\Models;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Nexa\Exceptions\NotFoundException;
use Nexa\Nexa;
use Nexa\Reflection\EntityReflection;
use ReflectionException;

class Model
{

    private static Connection $connection;

    private static string $table;

    private static QueryBuilder $queryBuilder;


    /**
     * @throws ReflectionException
     */
    public function __construct()
    {

        $reflection = new EntityReflection($this::class);
        self::$connection = Nexa::getConnection();
        self::$table = $reflection->getTable(Nexa::$inflector);
        self::$queryBuilder = self::$connection->createQueryBuilder();
    }

    public static function find($id, $columns = ["*"]): array|false
    {
        new static;

        return self::$queryBuilder->select(implode(",", $columns))
            ->from(self::$table)
            ->where("id = ?")
            ->setParameters([$id])
            ->fetchAssociative();
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public static function findOrFail($id, $columns = ["*"]): array
    {
        new static;

        $result = self::$queryBuilder->select(implode(",", $columns))
            ->from(self::$table)
            ->where("id = ?")
            ->setParameters([$id])
            ->fetchAssociative();

        if (!$result) {

            throw new NotFoundException('Resource not Found', 4004);
        }

        return $result;
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

    private static function prefixTable(array $columns): string
    {
        $columns = array_map(fn($column) => self::$table .".". $column, $columns);

        $select = implode(',', $columns);

        return $select;

    }

}