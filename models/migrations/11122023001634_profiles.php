<?php

use Nexa\Interfaces\MigrationInterface;
use Nexa\Databases\Database;

return new class implements MigrationInterface
{

    public string $table = "profiles";

    public function up() {

        return Database::raw("CREATE TABLE profiles (id INT AUTO_INCREMENT NOT NULL, img VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, created_at DATE NOT NULL, PRIMARY KEY(id))");
        
    }

    public function down() {

        return Database::raw("DROP TABLE profiles");

    }
};