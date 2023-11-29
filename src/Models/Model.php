<?php

namespace Nexa\Models;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Nexa\Attributes\Common\PrimaryKey;
use Nexa\Attributes\Entities\Entity;
use Nexa\Exceptions\NotFoundException;
use Nexa\Nexa;
use Nexa\Reflection\EntityReflection;
use ReflectionException;

class Model
{

    private static Connection $connection;

    private static string $table;

    private static QueryBuilder $queryBuilder;

    private static ?string $primaryKey = null;


    /**
     * @throws ReflectionException
     */
    public function __construct()
    {

        $reflection = new EntityReflection($this::class);
        self::$connection = Nexa::getConnection();
        self::$table = $reflection->getTable(Nexa::$inflector);
        self::$queryBuilder = self::$connection->createQueryBuilder();
        self::$primaryKey = self::getPrimaryKey($reflection);
    }

    /**
     * @throws Exception
     */
    public static function find($id, $columns = ["*"]): array|false
    {
        new static;

        return self::$queryBuilder->select(implode(",", $columns))
            ->from(self::$table)
            ->where(self::$primaryKey . "= ?")
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
            ->where(self::$primaryKey . "= ?")
            ->setParameters([$id])
            ->fetchAssociative();

        if (!$result) {

            throw new NotFoundException('Resource not Found', 4004);
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public static function findAll(array $columns = ["*"]): array
    {
        new static;

        return self::$queryBuilder->select(implode(",", $columns))
            ->from(self::$table)
            ->fetchAllAssociative();
    }

    public static function like(string $column, string $search, $columns = ['*']): array|false
    {
        new static;

        return self::$queryBuilder->select(implode(",", $columns))
            ->from(self::$table)
            ->where("$column LIKE '%$search%'")
            ->fetchAllAssociative();
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

        return self::$connection->delete(self::$table, [self::$primaryKey => $id]);

    }

    /**
     * @throws Exception
     */
    public static function deleteWhere(array $conditions): int|string
    {
        new static;

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

    private static function getPrimaryKey(EntityReflection $reflection): string|null
    {

        $properties = $reflection->getProperties();

        $result = array_filter($properties, function($property) {
            return $property->getAttributes(PrimaryKey::class);
        });
        return count($result) > 0 ? $result[0]->getName() : null;
    }

}