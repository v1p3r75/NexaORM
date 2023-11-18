<?php

namespace Nexa;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Nexa\Attributes\Common\PrimaryKey;
use Nexa\Reflection\EntityReflection;


class Nexa
{

    private ?Connection $connection = null;
    private Schema $schema;
    private Comparator $comparator;

    public function __construct(
        private array $config,
    ) {

        $this->connection = DriverManager::getConnection($this->config);
        $this->schema = new Schema;
        $this->comparator = new Comparator(new MySQLPlatform);

    }

    public function saveEntity(EntityReflection $entity) {

        $tableName = $entity->getTable();

        $table = $this->schema->createTable($tableName);
        $columns = $entity->getColumns();

        array_map(function($column) use ($table) {

            $name = $column['name'];
            
            $type = isset($column['constraints'][0]) ? $column['constraints'][0] : null;

            $options = [];

            if (isset($column['constraints'][1])) {
                
                if (array_key_exists('primary_key', $column['constraints'][1])) {

                    unset($column['constraints'][1]['primary_key']);
                }

                $options = $column['constraints'][1];
            }

            $table->addColumn($name, $type, $options);

        }, $columns);

        $primaryKey = $this->getPrimaryKey($columns);

        $key = isset($primaryKey) ? $primaryKey['name'] : null;

        $table->setPrimaryKey([$key]);

        $sql = implode("", $this->schema->toSql(new MySQLPlatform));

        return $this->schema;
    }

    private function getPrimaryKey(array $columns): array {

        foreach($columns as $column){
            
            // find and return the primary key column
            if($column && isset($column['constraints'][1])) {

                if (array_key_exists('primary_key', $column['constraints'][1]))
                return $column;
            }
        }

        return [];
    }

    public function executeQuery(string $sql) {

        $prepare = $this->connection->prepare($sql);

        if($result = $prepare->executeQuery()) {

            return $result;
        }

        return false;
    }

    public function compare(Schema $schema1, Schema $schema2) {

        $schemaManager = $this->connection->createSchemaManager();
        $comparator = $schemaManager->createComparator();

        return $comparator->compareSchemas($schema1, $schema2);
    }
}
