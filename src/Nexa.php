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
use Nexa\Exceptions\ConfigException;
use Nexa\Exceptions\DatabaseException;
use Nexa\Exceptions\MigrationFailedException;
use Nexa\Logging\Logger;
use Nexa\Reflection\EntityReflection;


class Nexa
{

    const PRIMARY_KEY = 'primary_key';

    const FOREIGN_KEY = 'foreign_key';

    const UNIQUE_KEY = 'unique';

    const ON_DELETE = 'onDelete';

    const ON_UPDATE = 'onUpdate';

    const CASCADE = 'CASCADE';

    const DATETIME_NOW = 'CURRENT_TIMESTAMP';

    private static ?Nexa $instance = null;

    public static ?Connection $connection;

    private Comparator $comparator;

    private AbstractPlatform $platform;

    static Inflector $inflector;

    public array $config = [];

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function __construct(private readonly array $db_config)
    {

        self::$connection = DriverManager::getConnection($this->db_config);
        $this->platform = self::$connection->getDatabasePlatform();
        $this->comparator = new Comparator($this->platform);
    }

    private function setOptions(array $options)
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
        if ($primaryKeys) $table->setPrimaryKey($primaryKeys);

        $foreignKeys = $this->getForeignKeys($columns);

        array_map(function ($foreign) use ($table) {
            // $foreign = [column_name, $foreign_table_name, $foreign_table_columns, $options]

            $table->addForeignKeyConstraint($foreign[1], [$foreign[0]], $foreign[2], $foreign[3]);
        }, $foreignKeys);

        $uniqueKeys = $this->getUniqueKeys($columns);
        if ($uniqueKeys) $table->addUniqueConstraint($uniqueKeys);

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

    private function getUniqueKeys(array $columns): array
    {

        $keys = [];
        foreach ($columns as $column) {
            // find and return the primary key column
            if ($column && isset($column['constraints'][1])) {

                if (array_key_exists(Nexa::UNIQUE_KEY, $column['constraints'][1])) {

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

        $specials = [Nexa::PRIMARY_KEY, Nexa::FOREIGN_KEY, Nexa::UNIQUE_KEY];

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



    private function makeMigration(EntityReflection $entity): bool
    {

        $tableName = $entity->getTable(self::$inflector);
        $schema = $this->getSchema($entity);
        $fileName = "$tableName.php";
        $file = $this->getMigrationsPath() . $fileName;

        $migration_data = $this->getMigrationsDataFileContent();

        if (file_exists($file)) {

            if (! $migration_data->runs->$fileName) { // if the migration is not run yet

                return (bool)$this->writeMigration($schema, $tableName, $file, $entity);
            }

            $old_migration = require $file;

            $old_schema = unserialize($old_migration->schema);
            $change = $this->comparator->compareSchemas($old_schema, $schema);

            if (!$change->isEmpty()) {
                $sql = $this->compareAndGetSQL($old_schema, $schema);
                $migration_data->runs->$fileName = false;
                $this->setMigrationsData($migration_data);

                Logger::info('Edit an old migration for : ' . $tableName);
                return (bool)$this->writeMigration($schema, $tableName, $file, $entity,  true, $sql);
            }
        }
        if (!isset($migration_data->runs->$fileName)) {

            Logger::info('Make new migration for : ' . $tableName);
            $migration_data->runs->$fileName = false;
            $this->setMigrationsData($migration_data);
        }

        return (bool)$this->writeMigration($schema, $tableName, $file, $entity);
    }

    private function writeMigration(
        Schema $schema,
        string $table,
        string $file,
        EntityReflection $entity,
        bool $updated = false,
        string $updated_sql = ''
    ) {

        $stub = file_get_contents(__DIR__ . '/Stubs/migration.stub');

        $data = [
            'up_sql' => !$updated ? $this->getQuery($schema) : $updated_sql,
            'down_sql' => $this->getQuery($schema, true),
            'table' => $table,
            'schema' => serialize($schema),
            'entity' => $entity->getEntity()
        ];

        return $this->fillStub($stub, $file, $data);
    }

    public function makeAllMigrations(): bool
    {
        $this->prepareMigrationsFolder();


        $migration_files = $this->getDirectoryFiles($this->getMigrationsPath());
        $migration_data = $this->getMigrationsDataFileContent();


        foreach ($migration_files as $file) {

            $migration = require $this->getMigrationsPath() . $file;

            if (!class_exists($migration->entity)) {

                if (!in_array($file, $migration_data->removes)) {

                    $migration_data->removes[] = $file;

                    $this->setMigrationsData($migration_data);
                }
            }
        }

        array_map(
            fn ($entity) =>
            $this->makeMigration(new EntityReflection($entity)),

            array_filter($this->getEntities(), fn ($entity) => $entity != null)
        );

        return true;
    }

    public function runAllMigrations()
    {

        $migration_data = $this->getMigrationsDataFileContent();

        // remove entities

        foreach ($migration_data->removes as $migration_to_remove) {

            $migration_class = require $this->getMigrationsPath() . $migration_to_remove;
            try {
                if ($migration_class->down()) {
                    unset($migration_data->runs->$migration_to_remove);
                    $migration_data->removes = array_filter($migration_data->removes, fn ($m) => $m != $migration_to_remove);
                    $this->setMigrationsData($migration_data);
                    unlink($this->getMigrationsPath() . $migration_to_remove); // Delete unused migration file
                }
            } catch (Exception $e) {

                throw new MigrationFailedException($e->getMessage(), $e->getCode(), $e->getPrevious());
            }
        }

        // runs changed migrations

        foreach ($migration_data->runs as $migration => $state) {

            if (!$state) {

                $migration_class = require $this->getMigrationsPath() . $migration;
                try {

                    if ($migration_class->up()) {
                        $migration_data->runs->$migration = true;
                        $this->setMigrationsData($migration_data);
                    }
                } catch (Exception $e) {

                    throw new MigrationFailedException($e->getMessage(), $e->getCode(), $e->getPrevious());
                }
            }
        }

        return true;
    }

    public function rebuildAllMigrations()
    {

        if ($this->deleteFolder($this->getMigrationsPath())) {

            return $this->makeAllMigrations();
        }

        return false;
    }

    private function deleteFolder(string $path)
    {

        $content = scandir($path);

        array_map(function ($file) use ($path) {

            if (!in_array($file, ['.', '..'])) {

                $filepath = $path . "/" . $file;

                is_dir($filepath) ?
                    $this->deleteFolder($filepath) && rmdir($filepath)
                    : unlink($filepath);
            }
        }, $content);

        return true;
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
        }, $files);
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

    public function getMigrationsDataPath(): string
    {
        return $this->getMigrationsPath() . "/data/nexa_migrations.json";
    }

    private function getMigrationsDataFileContent(): mixed
    {

        $migrations = file_get_contents($this->getMigrationsDataPath());

        return json_decode($migrations);
    }

    private function setMigrationsData(mixed $data)
    {

        return file_put_contents($this->getMigrationsDataPath(), json_encode($data));
    }

    private function fillStub(string $stub_content, string $file, array $vars, string $start_delimiter = "{{", string $end_delimiter = "}}"): bool
    {

        foreach ($vars as $key => $value) {

            $query = $start_delimiter . $key . $end_delimiter;
            $stub_content = str_replace($query, $value, $stub_content);
        }

        return @file_put_contents($file, $stub_content);
    }

    private function prepareMigrationsFolder(): void
    {

        $file = $this->getMigrationsPath() . "/data/nexa_migrations.json";

        if (!file_exists($file)) {

            mkdir(dirname($file), 0777, true);
            file_put_contents($file, file_get_contents(__DIR__ . "/Stubs/migrations_settings.stub"));
        }
    }

    public static function getNexa(array $db_config, array $options): Nexa
    {

        if (is_null(self::$instance)) {

            self::$instance = new self($db_config);
            self::$instance->setOptions($options);
        }

        return self::$instance;
    }

    public static function getConnection(): Connection
    {

        return self::$instance::$connection;
    }

    public static function getNexaFromEnv()
    {

        $config = [
                'host' => getenv('NEXA_DB_HOST'),
                'user' => getenv('NEXA_DB_USER'),
                'password' => getenv('NEXA_DB_PASSWORD'),
                'dbname' => getenv('NEXA_DB_NAME'),
                'driver' => getenv('NEXA_DB_DRIVER')
        ];

        $options = [
            'lang' => getenv('NEXA_LANG'),
            'migrations_path' => getenv('NEXA_MIGRATION_PATH'),
            'entity_path' => getenv('NEXA_ENTITY_PATH'),
            'entity_namespace' => getenv('NEXA_ENTITY_NAMESPACE'),
        ];


        return self::getNexa($config, $options);
    }
}
