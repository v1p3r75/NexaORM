<?php

namespace Nexa\Models;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Nexa\Attributes\Common\PrimaryKey;
use Nexa\Collections\Collection;
use Nexa\Exceptions\NotFoundException;
use Nexa\Nexa;
use Nexa\Reflection\EntityReflection;
use ReflectionException;

class Model
{

    private static Connection $connection;

    private static Model $model;

    private static string $table;

    private static QueryBuilder $queryBuilder;

    private static ?string $primaryKey = null;

    protected $entity;

    protected $hidden;

    protected $fillable;

    protected $timestamp = false;

    protected $soft_delete = false;

    protected $date_format = "Y-m-d h:i:s";

    protected $created_at = 'created_at';

    protected $updated_at = 'updated_at';

    protected $deleted_at = 'deleted_at';


    /**
     * @throws ReflectionException
     */
    public function __construct()
    {

        self::$connection = Nexa::getConnection();
        $reflection = new EntityReflection($this->entity); // TODO: create search_entity method
        self::$table = $reflection->getTable(Nexa::$inflector);
        self::$queryBuilder = self::$connection->createQueryBuilder();
        self::$primaryKey = self::getPrimaryKey($reflection);
        self::$model = $this;
    }

    /**
     * @throws Exception
     */
    public static function find($id): Collection | false
    {
        new static;

        $result =  self::$queryBuilder->select('*')
            ->from(self::$table)
            ->where(self::$primaryKey . "= ?")
            ->setParameters([$id])
            ->fetchAssociative();

        return is_array($result) ? self::collection($result) : $result;
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public static function findOrFail($id): Collection
    {
        new static;

        $result = self::$queryBuilder->select()
            ->from(self::$table)
            ->where(self::$primaryKey . "= ?")
            ->setParameters([$id])
            ->fetchAssociative();

        if (!$result) {

            throw new NotFoundException('Resource not Found', 4004);
        }

        return self::collection($result);
    }

    /**
     * @throws Exception
     */
    public static function findAll(array $columns = ["*"]): Collection
    {
        new static;

        $result = self::$queryBuilder->select(implode(',', $columns))
            ->from(self::$table)
            ->fetchAllAssociative();

        return self::collection($result);
    }

    public static function like(string $column, string $search, $columns = ['*']): Collection
    {
        new static;

        $result = self::$queryBuilder->select(implode(",", $columns))
            ->from(self::$table)
            ->where("$column LIKE '$search'")
            ->fetchAllAssociative();

        return self::collection($result);
    }

    /**
     * @throws Exception
     */
    public static function insert(array $data): int
    {
        new static;

        $data = self::secure($data);

        if (implode('', self::$model->fillable) != "*") {

            foreach ($data as $key => $value) {

                if (!in_array($key, self::$model->fillable)) {
                    unset($data[$key]);
                }
            }
        }
        
        if (self::$model->timestamp) {
            $data[self::$model->created_at] = date(self::$model->date_format);
        }

        return self::$connection->insert(self::$table, $data);
    }

    /**
     * @throws Exception
     */
    public static function update(array $data, array $conditions = []): int
    {
        new static;

        $data = self::secure($data);

        if (self::$model->timestamp) {
            $data[self::$model->updated_at] = date(self::$model->date_format);
        }

        return self::$connection->update(self::$table, $data, $conditions);
    }

    /**
     * @throws Exception
     */
    public static function delete($id): int|string
    {
        new static;

        $id = self::secure($id);

        if (self::$model->soft_delete) {

            return self::$connection->update(
                self::$table,
                [
                    self::$model->deleted_at => date(self::$model->date_format)
                ],
                [self::$primaryKey => $id]
            );
        }

        return self::$connection->delete(self::$table, [self::$primaryKey => $id]);
    }

    /**
     * @throws Exception
     */
    public static function deleteWhere(array $conditions): int|string
    {
        new static;

        return self::$connection->delete(self::$table, self::secure($conditions));
    }

    public static function random(): array {

        return self::findAll()->random();
    }

    private static function collection(array $collection): Collection {

        return new Collection($collection);
    }

    private static function secure(array | string $data)
    {

        if (is_array($data)) {

            return array_map(function ($value) {

                return self::secure($value);
            }, $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    private static function getPrimaryKey(EntityReflection $reflection): string|null
    {

        $properties = $reflection->getProperties();

        $result = array_filter($properties, function ($property) {
            return $property->getAttributes(PrimaryKey::class);
        });
        return count($result) > 0 ? $result[0]->getName() : 'id';
    }
}
