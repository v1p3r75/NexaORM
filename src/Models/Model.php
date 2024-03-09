<?php

namespace Nexa\Models;

use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Nexa\Attributes\Common\ForeignKey;
use Nexa\Attributes\Common\PrimaryKey;
use Nexa\Collections\Collection;
use Nexa\Exceptions\NotFoundException;
use Nexa\Nexa;
use Nexa\Reflection\EntityReflection;
use ReflectionException;
use ReflectionProperty;

class Model
{

    private static Connection $connection;

    private static Model $model;

    private static string $table;

    private static QueryBuilder $queryBuilder;

    private static $primaryKeyEntity = null;

    private static $foreignKeys;

    protected $entity;

    protected $hidden;

    protected $fillable = [];

    protected $timestamp = false;

    protected $soft_delete = false;

    protected $primaryKey = "id";

    protected $date_format = "Y-m-d h:i:s";

    protected $created_at = 'created_at';

    protected $updated_at = 'updated_at';

    protected $deleted_at = 'deleted_at';


    /**
     * @throws ReflectionException
     */
    public function __construct()
    {

        self::$connection = Nexa::getNexaFromEnv()->getConnection();
        $reflection = new EntityReflection($this->entity); // TODO: create search_entity method (entity auto detection)
        self::$table = $reflection->getTable(Nexa::$inflector);
        self::$queryBuilder = self::$connection->createQueryBuilder();
        self::$primaryKeyEntity = $this->getPrimaryKey($reflection);
        self::$foreignKeys = $this->getForeignKeys($reflection);
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
            ->where(self::$primaryKeyEntity . "= ?")
            ->setParameters([$id])
            ->fetchAssociative();

        $result = $result ? self::fetchForeignKeysData($result) : false;

        return is_array($result) ? self::collection($result) : $result;
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public static function findOrFail($id): Collection
    {
        new static;

        $result = self::$queryBuilder->select("*")
            ->from(self::$table)
            ->where(self::$primaryKeyEntity . "= ?")
            ->setParameters(self::secure([$id]))
            ->fetchAssociative();

        if (!$result) {

            throw new NotFoundException('Resource not Found', 4004);
        }

        return self::collection(self::fetchForeignKeysData($result));
    }

    public static function findAll(array $columns = ["*"]): Collection
    {
        new static;


        $result = self::$queryBuilder->select(implode(',', $columns))
            ->from(self::$table)
            ->fetchAllAssociative();

        if ($result) $result = self::fetchForeignKeysData($result, false);

        return self::collection($result);
    }

    public static function like(string $column, string $search, $columns = ['*']): Collection
    {
        new static;

        $result = self::$queryBuilder->select(implode(",", $columns))
            ->from(self::$table)
            ->where("$column LIKE '?'")
            ->setParameters(self::secure($search))
            ->fetchAllAssociative();

        return self::collection(self::fetchForeignKeysData($result, false));
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
                [self::$primaryKeyEntity => $id]
            );
        }

        return self::$connection->delete(self::$table, [self::$primaryKeyEntity => $id]);
    }

    /**
     * @throws Exception
     */
    public static function deleteWhere(array $conditions): int|string
    {
        new static;

        return self::$connection->delete(self::$table, self::secure($conditions));
    }

    public static function beginTransaction(): void
    {

        new static;

        self::$connection->beginTransaction();
    }

    public static function commitTranscaction(): void
    {

        new static;

        self::$connection->commit();
    }

    public static function transcational(Closure $function)
    {

        new static;

        return self::$connection->transactional($function);
    }


    public static function random(): array
    {

        return self::findAll()->random();
    }

    private static function collection(array $collection): Collection
    {

        return new Collection($collection);
    }

    private static function secure(array | string | null $data)
    {

        if (is_null($data)) return null;

        if (is_array($data)) {

            return array_map(function ($value) {

                return self::secure($value);
            }, $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    private function getPrimaryKey(EntityReflection $reflection): string|null
    {

        $properties = $reflection->getProperties();

        $result = array_filter($properties, function ($property) {
            return $property->getAttributes(PrimaryKey::class);
        });
        return count($result) > 0 ? $result[0]->getName() : $this->primaryKey;
    }

    private static function fetchForeignKeysData(array $result, bool $single = true): array
    {

        if (!$single) {

            foreach ($result as &$row) {

                foreach (self::$foreignKeys as $foreignKey) {

                    $target = $foreignKey['foreign_table'] . "." . $foreignKey['foreign_column'];
                    $foreignData = self::$queryBuilder->select($foreignKey['foreign_table'] . '.*')
                        ->from($foreignKey['foreign_table'])
                        ->where("$target = :param")
                        ->setParameter('param', $row[$foreignKey['name']])
                        ->fetchAllAssociative();

                    $row[$foreignKey['name']] = array_merge(...$foreignData);
                }
            }
        } else {

            foreach (self::$foreignKeys as $foreignKey) {

                $target = $foreignKey['foreign_table'] . "." . $foreignKey['foreign_column'];
                $foreignData = self::$queryBuilder->select($foreignKey['foreign_table'] . '.*')
                    ->from($foreignKey['foreign_table'])
                    ->where("$target = ?")
                    ->setParameters(array($result[$foreignKey['name']]))
                    ->fetchAllAssociative();


                $result[$foreignKey['name']] = array_merge(...$foreignData);
            }
        }

        return $result;
    }

    private static function getForeignKeys(EntityReflection $reflection)
    {

        $properties = $reflection->getProperties();

        $result = array_filter($properties, function (ReflectionProperty $property) {

            return $property->getAttributes(ForeignKey::class);
        });


        return array_map(function (ReflectionProperty $p) use ($reflection) {

            $attr = $p->getAttributes(ForeignKey::class);
            if ($attr && isset($attr[0])) {
                return [
                    'name' => $p->getName(),
                    'foreign_table' => (new EntityReflection($attr[0]->getArguments()[0]))->getTable(Nexa::$inflector),
                    'foreign_column' => $attr[0]->getArguments()[1],
                ];
            }
        }, $result);
    }
}
