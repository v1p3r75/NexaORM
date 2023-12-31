<?php

namespace Models;

require './vendor/autoload.php';

use Doctrine\Inflector\Language;
use Nexa\Databases\Database;
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
    [
        'lang' => Language::ENGLISH,
        'migrations_path' => __DIR__ . "/models/migrations/"
    ]
);
$nexa->makeMigration(new EntityReflection(Profile::class));
/*
dump(Database::raw('SELECT * from profiles'));
dump(Database::queryBuilder()->select('*')->from('profiles')->fetchAllAssociative());
Profile::insert(['img' => 'path', 'address' => 'Cotonou City', 'created_at' => date('Y-m-d')];
Profile::update(['img' => 'Service', 'address' => 'Cotonou New'], ['id' => 15]);
dump(Profile::deleteWhere(['img' => 'path3']));
*/

/*
$profiles = $nexa->getSchema(new EntityReflection(Profile::class));
$users = $nexa->getSchema(new EntityReflection(User::class));
$single = $nexa->getSchema(new EntityReflection(Single::class));
$userNew = $nexa->getSchema(new EntityReflection(UserNew::class));
$nexa->executeSchema($single);
$nexa->executeSchema($profiles);
$nexa->executeSchema($users);
$nexa->compareAndGetSQL($users, $userNew);
*/
