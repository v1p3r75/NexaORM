<?php

namespace Models;

require './vendor/autoload.php';

use Doctrine\DBAL\Schema\Schema;
use Nexa\Reflection\EntityReflection;

use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Nexa\Nexa;
use Models\User;

$nexa = new Nexa(
    [
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'dbname' => 'nexa',
        'driver' => 'pdo_mysql',
    ],
);

$result = $nexa->saveEntity(new EntityReflection(User::class));
$result2 = $nexa->saveEntity(new EntityReflection(User2::class));

dump($nexa->compare($result, $result2));
