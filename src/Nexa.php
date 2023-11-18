<?php

namespace Nexa;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Nexa\Reflection\EntityReflection;


class Nexa
{

    private ?Connection $connection = null;

    public function __construct(
        private readonly array $config,
        private                $driver,
        private                $optional = null
    ) {
    }

    public function getConnection(): Connection
    {

        if(!$this->connection) {

            $this->connection = new Connection($this->config, $this->driver, $this->optional);
        }

        return $this->connection;

    }

    public function getSchema(EntityReflection $entity, Schema $schema) {

        $tableName = $entity->getTable();

        $table = $schema->createTable($tableName);

        array_map(function($column) use ($table) {

            $name = $column['name'];
            $type = isset($column['constraints'][0]) ? $column['constraints'][0] : null;
            $options = isset($column['constraints'][1]) ? $column['constraints'][1] : [];

            $table->addColumn($name, $type, $options);

        }, $entity->getColumns());


        dump($schema->toSql(new MySQLPlatform));
    }
}
