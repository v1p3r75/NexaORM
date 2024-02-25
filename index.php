<?php
namespace Models;

require './vendor/autoload.php';

use Doctrine\DBAL\Schema\Schema;
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
);

$nexa->setOptions(
    [
        'lang' => Language::ENGLISH,
        'migrations_path' => __DIR__ . "/app/migrations",
        'entity_path' => __DIR__ . '/app/entities',
        'entity_namespace' => "Entities\\",
    ]
);

dd($nexa->makeAllMigrations());