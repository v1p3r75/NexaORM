<?php

namespace Nexa;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Exception;
use Nexa\Exceptions\DatabaseException;
use Nexa\Reflection\EntityReflection;


class Nexa
{

    private ?Connection $connection;
    private Comparator $comparator;

    private $platform;

    public function __construct(private readonly array $config)
    {

        $this->connection = DriverManager::getConnection($this->config);
        $this->platform = $this->connection->getDatabasePlatform();
        $this->comparator = new Comparator($this->platform);

    }

    public function getSchema(EntityReflection $entity): Schema
    {
        $schema = new Schema;
        $tableName = $entity->getTable();
        $table = $schema->createTable($tableName);
        $columns = $entity->getColumns();

        array_map(function($column) use ($table) {

            $name = $column['name'];
            
            $type = $column['constraints'][0] ?? null;

            $options = $column['constraints'][1] ?? [];

            if (array_key_exists('primary_key', $options)) {

                unset($options['primary_key']);
            }
            if (array_key_exists('foreign_key', $options)) {

                unset($options['foreign_key']);
            }

            $table->addColumn($name, $type, $options);

        }, $columns);

        $primaryKeys = $this->getPrimaryKeys($columns);
        $table->setPrimaryKey($primaryKeys);

        $foreignKeys = $this->getForeignKeys($columns);

        array_map(function ($foreign) use ($table) {
            // $foreign = [column_name, $foreign_table_name, $foreign_table_columns, $options]

            $table->addForeignKeyConstraint($foreign[1], [$foreign[0]], $foreign[2], $foreign[3]);

        },$foreignKeys);

        return $schema;
    }

    private function getPrimaryKeys(array $columns): array {

        $keys = [];
        foreach($columns as $column)
        {
            // find and return the primary key column
            if($column && isset($column['constraints'][1])) {

                if( array_key_exists('primary_key', $column['constraints'][1])) {

                    $keys[] = $column['name'];
                }
            }
        };

        return $keys;
    }

    private function getForeignKeys(array $columns): array {

        $keys = [];
        foreach($columns as $column)
        {
            // find and return the foreign keys
            if(isset($column['constraints'][1])) {

                if( array_key_exists('foreign_key', $column['constraints'][1])) {

                    $keys[] = array_merge([$column['name']], $column['constraints'][1]['foreign_key']);
                }
            }
        };

        return $keys;
    }


    public function executeSchema($schema): Result | DatabaseException
    {

        $sql = $this->getQuery($schema);
        $prepare = $this->connection->prepare($sql);

        try {
            return $prepare->executeQuery();

        }catch (Exception $e) {

            throw new DatabaseException($e->getMessage(), $e->getCode());
        }
    }

    public function getQuery(Schema $schema): string {

        return implode(";", $schema->toSql($this->platform));
    }
    public function compare(Schema $oldSchema, Schema $newSchema): \Doctrine\DBAL\Schema\SchemaDiff
    {

        return $this->comparator->compareSchemas($oldSchema, $newSchema);

    }
}
