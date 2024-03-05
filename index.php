<?php

// Example


require './vendor/autoload.php';

use Doctrine\Inflector\Language;

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
        'migrations_path' => __DIR__ . "/tests/migrations",
        'entity_path' => __DIR__ . '/tests/Entities',
        'entity_namespace' => "Nexa\\Test\\Entities",
    ]
);


$nexa->makeAllMigrations();
$nexa->runAllMigrations();