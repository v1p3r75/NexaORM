<?php

use Nexa\Interfaces\MigrationInterface;
use Nexa\Databases\Database;

return new class implements MigrationInterface
{

    public string $table = "single";

    public function up() {

        return Database::raw("CREATE TABLE single (id INT UNSIGNED AUTO_INCREMENT NOT NULL, img VARCHAR(50) NOT NULL, address VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id))");
        
    }

    public function down() {

        return Database::raw("DROP TABLE single");

    }
};