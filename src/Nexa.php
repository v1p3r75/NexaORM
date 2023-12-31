<?php

namespace Nexa;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Language;
use Exception;
use Nexa\Exceptions\DatabaseException;
use Nexa\Reflection\EntityReflection;


class Nexa
{

    const PRIMARY_KEY = 'primary_key';
    const FOREIGN_KEY = 'foreign_key';

    public static ?Connection $connection;

    private Comparator $comparator;

    private AbstractPlatform $platform;

    static Inflector $inflector;

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __construct(private readonly array $db_config, public $other_config = [])
    {

        self::$connection = DriverManager::getConnection($this->db_config);
        $this->platform = self::$connection->getDatabasePlatform();
        $this->comparator = new Comparator($this->platform);
        self::$inflector = InflectorFactory::createForLanguage(
            $this->other_config['lang'] ?? Language::ENGLISH
        )->build();
    }

    public function getSchema(EntityReflection $entity): Schema
    {
        $schema = new Schema;
        $tableName = $entity->getTable(self::$inflector);
        $table = $schema->createTable($tableName);
        $columns = $entity->getColumns();

        array_map(function ($column) use ($table) {

            $name = $column['name'];
            $type = $column['constraints'][0];

            $options = $column['constraints'][1] ?? [];
            $keys = $this->getSpecialOptions($options);

            foreach($keys as $key)
                unset($options[$key]);

            $table->addColumn($name, $type, $options);
        }, $columns);

        $primaryKeys = $this->getPrimaryKeys($columns);
        $table->setPrimaryKey($primaryKeys);

        $foreignKeys = $this->getForeignKeys($columns);

        array_map(function ($foreign) use ($table) {
            // $foreign = [column_name, $foreign_table_name, $foreign_table_columns, $options]

            $table->addForeignKeyConstraint($foreign[1], [$foreign[0]], $foreign[2], $foreign[3]);
        }, $foreignKeys);

        // TODO: Add uniqueIndex columns

        return $schema;
    }

    private function getPrimaryKeys(array $columns): array
    {

        $keys = [];
        foreach ($columns as $column) {
            // find and return the primary key column
            if ($column && isset($column['constraints'][1])) {

                if (array_key_exists(Nexa::PRIMARY_KEY, $column['constraints'][1])) {

                    $keys[] = $column['name'];
                }
            }
        }

        return $keys;
    }

    private function getForeignKeys(array $columns): array
    {

        $keys = [];
        foreach ($columns as $column) {
            // find and return the foreign keys
            if (isset($column['constraints'][1])) {

                if (array_key_exists(Nexa::FOREIGN_KEY, $column['constraints'][1])) {

                    $keys[] = array_merge([$column['name']], $column['constraints'][1][Nexa::FOREIGN_KEY]);
                }
            }
        }

        return $keys;
    }


    private function getSpecialOptions(array $options): array
    {

        $specials = [Nexa::PRIMARY_KEY, Nexa::FOREIGN_KEY];

        $founds = array_filter($options, function($option) use ($specials) {

            return in_array($option, $specials);

        }, ARRAY_FILTER_USE_KEY);

        return array_keys($founds);
    }

    /**
     * @throws DatabaseException
     * @throws \Doctrine\DBAL\Exception
     */
    public function executeSchema($schema): Result | DatabaseException
    {

        $sql = $this->getQuery($schema);
        $prepare = self::$connection->prepare($sql);

        try {
            return $prepare->executeQuery();
        } catch (Exception $e) {

            throw new DatabaseException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getQuery(Schema $schema): string
    {

        return implode(";", $schema->toSql($this->platform));
    }

    public function compareAndGetSQL(Schema $oldSchema, Schema $newSchema): string
    {

        $schemaDiff = $this->comparator->compareSchemas($oldSchema, $newSchema);

        $sql = $this->platform->getAlterSchemaSQL($schemaDiff);

        return implode(";", $sql);
    }

    public function makeMigration(EntityReflection $entity): bool
    {

        $tableName = $entity->getTable(self::$inflector);

        $file = $this->getMigrationsPath() . '/' . date('dmYHis') . "-$tableName.php";

        $schema = $this->getSchema($entity);

        return (bool)$this->writeMigration($schema, $file);
    }

    private function writeMigration(Schema $schema, string $file)
    {

        $upSql = $this->formatSql($schema->toSql($this->platform));
        $downSql = $this->formatSql($schema->toDropSql($this->platform));

        $stub = file_get_contents(__DIR__ . '/Stubs/migration.stub');
        $stub = str_replace('{up_sql}', $upSql, $stub);
        $stub = str_replace('{down_sql}', $downSql, $stub);

        return file_put_contents($file, $stub);
    }

    private function formatSql(array $sql)
    {
        return implode(";", $sql);
    }

    public function getMigrationsPath(): string
    {

        return $this->other_config['migrations_path'] ?? __DIR__ . '/migrations';
    }
    public static function getConnection(): Connection
    {
        return self::$connection;
    }
}
