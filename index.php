<?php

// Example


require './vendor/autoload.php';

use Nexa\Nexa;
use Nexa\Test\Models\Profile;

$nexa = Nexa::getInstance(__DIR__ . "/tests/database.php");


// dump(Profile::findAll());

$nexa->makeAllMigrations();
// $nexa->runAllMigrations();