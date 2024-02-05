<?php

use Nexa\Interfaces\MigrationInterface;
use Nexa\Databases\Database;

return new class implements MigrationInterface
{

    public string $table = "users";

    public function up() {

        return Database::raw("CREATE TABLE users (id SMALLINT AUTO_INCREMENT NOT NULL, username VARCHAR(255) DEFAULT 'John Doe' NOT NULL, profile_id INT DEFAULT NULL COMMENT 'user profile', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_1483A5E9CCFA12B8 (profile_id), PRIMARY KEY(id));ALTER TABLE users ADD CONSTRAINT FK_1483A5E9CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profiles (id) ON DELETE SET NULL");
        
    }

    public function down() {

        return Database::raw("ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9CCFA12B8;DROP TABLE users");

    }
};