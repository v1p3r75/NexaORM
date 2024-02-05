<?php

namespace Nexa;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Language;
use Error;
use Exception;
use Nexa\Databases\Database;
use Nexa\Exceptions\ConfigException;
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

    public array $config = [];

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __construct(private readonly array $db_config)
    {

        self::$connection = DriverManager::getConnection($this->db_config);
        $this->platform = self::$connection->getDatabasePlatform();
        $this->comparator = new Comparator($this->platform);
    }

    public function setOptions(array $options)
    {

        $this->config = $options;

        self::$inflector = InflectorFactory::createForLanguage(
            $options['lang'] ?? Language::ENGLISH
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

            foreach ($keys as $key)
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

        $founds = array_filter($options, function ($option) use ($specials) {

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
    public function getQuery(Schema $schema, bool $forDrop = false): string
    {
        $result =  !$forDrop ? $schema->toSql($this->platform) : $schema->toDropSql($this->platform);

        return implode(";", $result);
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

        $file = $this->getMigrationsPath() . date('dmYHis') . "_$tableName.php";

        $schema = $this->getSchema($entity);

        return (bool)$this->writeMigration($schema, $tableName, $file);
    }

    private function writeMigration(Schema $schema, string $table, string $file)
    {

        $upSql = $this->getQuery($schema);
        $downSql = $this->getQuery($schema, true);

        $stub = file_get_contents(__DIR__ . '/Stubs/migration.stub');
        $stub = str_replace('{up_sql}', $upSql, $stub);
        $stub = str_replace('{down_sql}', $downSql, $stub);
        $stub = str_replace('{table}', $table, $stub);

        return file_put_contents($file, $stub);
    }

    public function saveAllMigrations(): bool
    {

        $migration_files = $this->getDirectoryFiles($this->getMigrationsPath());

        // if (!empty($migration_files)) {

        //     foreach ($migration_files as $file) {

        //         unlink($this->getMigrationsPath() . $file);
        //     }

        //     print("\n - Deleted all migrations files : OK");
        // }

        array_map(
            fn ($entity) =>
            $this->makeMigration(new EntityReflection($entity)),

            $this->getEntities()
        );

        print("\n - Make new migrations : OK\n");

        return true;
    }

    public function runAllMigrations()
    {

        $migrations = $this->getDirectoryFiles($this->getMigrationsPath());
        $manager = self::$connection->createSchemaManager();
        $failed =  [];

        foreach ($migrations as $migration) {

            $_migration = require_once $this->getMigrationsPath() . $migration;

            if (Database::hasTable($_migration->table)) {

                try {

                    $manager->dropTable($_migration->table);
                } catch (DriverException $e) {

                    $failed[] = $_migration;
                }
            }

            $_migration->up();
            $this->makeMigrationAsCompleted($migration);
        }
        // array_map(
        //     function ($migration) use ($manager) {
        //         $manager->dropTable($migration->table);
        //         $migration->up();
        //         $this->makeMigrationAsCompleted($migration);
        //     },
        //     $failed
        // );
    }

    private function makeMigrationAsCompleted(string $migration_name)
    {

        $builder = Database::queryBuilder();

        return $builder->insert('nexa_migrations')->values([
            'name' => '?',
            'batch' => '?',
        ])->setParameters([$migration_name, 1])->executeQuery();
    }
    private function getEntities(): array
    {

        $path = $this->getEntitiesPath();
        $namespace = $this->getEntitiesNamespace();

        $files = $this->getDirectoryFiles($path);

        return array_map(function ($file) use ($path, $namespace) {

            $entityPath = $path . $file;
            $class = $namespace . pathinfo($entityPath, PATHINFO_FILENAME);

            if (class_exists($class))
                return $class;

            return false;
        }, $files);
    }

    public function saveMigrationsTable(Schema $schema)
    {
        $table = $schema->createTable('nexa_migrations');

        $table->addColumn('id', 'smallint', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 50]);
        $table->addColumn('batch', 'smallint',);
        $table->setPrimaryKey(['id']);

        return $this->executeSchema($schema);
    }

    private function getDirectoryFiles(string $directory): array
    {

        $content = scandir($directory);

        return array_filter($content, function ($file) use ($directory) {

            $path = $directory . '/' . $file;
            return is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php';
        });
    }

    public function getMigrationsPath(): string
    {

        if (isset($this->config['migrations_path'])) {

            return trim($this->config['migrations_path'], '/') . '/';
        }

        throw new ConfigException("You must set the migrations_path");
    }

    public function getEntitiesPath(): string
    {

        if (isset($this->config['entity_path'])) {

            return trim($this->config['entity_path'], '/') . '/';
        }

        throw new ConfigException("You must set the entity_path");
    }

    public function getEntitiesNamespace(): string
    {

        if (isset($this->config['entity_namespace'])) {

            return trim($this->config['entity_namespace'], '\\') . '\\';
        }

        throw new ConfigException("You must set the entity_namespace");
    }

    public static function getConnection(): Connection
    {
        return self::$connection;
    }
}
