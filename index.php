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
        'driver' => 'pdo_mysql'
    ],
);

$profiles = $nexa->getSchema(new EntityReflection(Profile::class));
$users = $nexa->getSchema(new EntityReflection(User::class));

dump($profiles);
//$nexa->executeSchema($users);
//$nexa->executeSchema($profiles)
