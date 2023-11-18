<?php

namespace Testing;
use Doctrine\DBAL\Schema\Schema;
use Nexa\Reflection\EntityReflection;
require dirname(__DIR__) . '/vendor/autoload.php';

use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Nexa\Nexa;

$nexa = new Nexa(
    [
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'dbname' => 'nexa'
    ],
    new Driver,
);

$result = $nexa->getSchema(new EntityReflection(User::class), new Schema());

dump($result);