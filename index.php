<?php

namespace Models;

require './vendor/autoload.php';

use Doctrine\Inflector\Language;
use Nexa\Reflection\EntityReflection;

use Nexa\Nexa;

$nexa = new Nexa(
    [
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'dbname' => 'nexa',
        'driver' => 'pdo_mysql'
    ],
    ['lang' => Language::ENGLISH]
);

$profiles = $nexa->getSchema(new EntityReflection(Profile::class));
$users = $nexa->getSchema(new EntityReflection(User::class));
$single = $nexa->getSchema(new EntityReflection(Single::class));
$userNew = $nexa->getSchema(new EntityReflection(UserNew::class));

dump($nexa->compareAndGetSQL($users, $userNew));

/*
$nexa->executeSchema($single);
$nexa->executeSchema($profiles);
$nexa->executeSchema($users);
$nexa->compareAndGetSQL($users, $userNew);
*/
