<?php

// Example


require './vendor/autoload.php';

use Doctrine\Inflector\Language;

use Nexa\Nexa;
use Nexa\Test\Models\Profile;

$config = [
    'NEXA_DB_HOST' => 'localhost',
    'NEXA_DB_USER' => 'root',
    'NEXA_DB_PASSWORD' => '',
    'NEXA_DB_NAME'=> 'nexa',
    'NEXA_DB_DRIVER' => 'pdo_mysql',
    'NEXA_LANG' => Language::ENGLISH,
    'NEXA_MIGRATION_PATH' => __DIR__ . "/tests/migrations",
    'NEXA_ENTITY_PATH' => __DIR__ . '/tests/Entities',
    'NEXA_ENTITY_NAMESPACE' => "Nexa\\Test\\Entities",
];

foreach ($config as $key => $value) {
    putenv("$key=$value");
}

$nexa = Nexa::getNexaFromEnv();

dump(Profile::findAll());

// $nexa->makeAllMigrations();
// $nexa->runAllMigrations();