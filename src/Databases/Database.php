<?php

namespace Nexa\Databases;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Nexa\Nexa;

class Database
{


    private static \Doctrine\DBAL\Connection $connection;

    public function __construct() {
        
        self::$connection = Nexa::getInstance()->getConnection();

    }

    /**
     * @throws Exception
     */
    public static function raw(string $sql, array $params = [], array $types = []): Result
    {
        new static;

        return self::$connection->executeQuery($sql, $params, $types);
    }

    public static function queryBuilder(): QueryBuilder
    {
        new static;

        return self::$connection->createQueryBuilder();
    }

    public static function hasTable(string $table) {

        new static;
        
        return self::$connection->createSchemaManager()->tableExists($table);
    }

}