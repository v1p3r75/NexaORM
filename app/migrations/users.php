<?php

use Nexa\Interfaces\MigrationInterface;
use Nexa\Databases\Database;

return new class implements MigrationInterface
{

    public string $table = "users";

    public string $entity = "Entities\UserEntity";

    public string $schema = <<<CODE
    O:27:"Doctrine\DBAL\Schema\Schema":7:{s:8:" * _name";s:0:"";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:39:" Doctrine\DBAL\Schema\Schema namespaces";a:0:{}s:10:" * _tables";a:1:{s:6:".users";O:26:"Doctrine\DBAL\Schema\Table":11:{s:8:" * _name";s:5:"users";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:11:" * _columns";a:4:{s:2:"id";O:27:"Doctrine\DBAL\Schema\Column":15:{s:8:" * _name";s:2:"id";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:8:" * _type";O:32:"Doctrine\DBAL\Types\SmallIntType":0:{}s:10:" * _length";i:255;s:13:" * _precision";N;s:9:" * _scale";i:0;s:12:" * _unsigned";b:0;s:9:" * _fixed";b:0;s:11:" * _notnull";b:1;s:11:" * _default";N;s:17:" * _autoincrement";b:1;s:19:" * _platformOptions";a:0:{}s:20:" * _columnDefinition";N;s:11:" * _comment";s:0:"";}s:8:"username";O:27:"Doctrine\DBAL\Schema\Column":15:{s:8:" * _name";s:8:"username";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:8:" * _type";O:30:"Doctrine\DBAL\Types\StringType":0:{}s:10:" * _length";i:255;s:13:" * _precision";N;s:9:" * _scale";i:0;s:12:" * _unsigned";b:0;s:9:" * _fixed";b:0;s:11:" * _notnull";b:1;s:11:" * _default";s:4:"John";s:17:" * _autoincrement";b:0;s:19:" * _platformOptions";a:0:{}s:20:" * _columnDefinition";N;s:11:" * _comment";s:0:"";}s:7:"profile";O:27:"Doctrine\DBAL\Schema\Column":15:{s:8:" * _name";s:7:"profile";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:8:" * _type";O:31:"Doctrine\DBAL\Types\IntegerType":0:{}s:10:" * _length";i:255;s:13:" * _precision";N;s:9:" * _scale";i:0;s:12:" * _unsigned";b:0;s:9:" * _fixed";b:0;s:11:" * _notnull";b:0;s:11:" * _default";N;s:17:" * _autoincrement";b:0;s:19:" * _platformOptions";a:0:{}s:20:" * _columnDefinition";N;s:11:" * _comment";s:12:"user profile";}s:10:"created_at";O:27:"Doctrine\DBAL\Schema\Column":15:{s:8:" * _name";s:10:"created_at";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:8:" * _type";O:34:"Doctrine\DBAL\Types\DateTimeTzType":0:{}s:10:" * _length";N;s:13:" * _precision";N;s:9:" * _scale";i:0;s:12:" * _unsigned";b:0;s:9:" * _fixed";b:0;s:11:" * _notnull";b:1;s:11:" * _default";s:17:"CURRENT_TIMESTAMP";s:17:" * _autoincrement";b:0;s:19:" * _platformOptions";a:0:{}s:20:" * _columnDefinition";N;s:11:" * _comment";s:0:"";}}s:43:" Doctrine\DBAL\Schema\Table implicitIndexes";a:1:{s:20:"idx_1483a5e98157aa0f";O:26:"Doctrine\DBAL\Schema\Index":8:{s:8:" * _name";s:20:"IDX_1483A5E98157AA0F";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:11:" * _columns";a:1:{s:7:"profile";O:31:"Doctrine\DBAL\Schema\Identifier":3:{s:8:" * _name";s:7:"profile";s:13:" * _namespace";N;s:10:" * _quoted";b:0;}}s:12:" * _isUnique";b:0;s:13:" * _isPrimary";b:0;s:9:" * _flags";a:0:{}s:35:" Doctrine\DBAL\Schema\Index options";a:0:{}}}s:11:" * _indexes";a:2:{s:7:"primary";O:26:"Doctrine\DBAL\Schema\Index":8:{s:8:" * _name";s:7:"primary";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:11:" * _columns";a:1:{s:2:"id";O:31:"Doctrine\DBAL\Schema\Identifier":3:{s:8:" * _name";s:2:"id";s:13:" * _namespace";N;s:10:" * _quoted";b:0;}}s:12:" * _isUnique";b:1;s:13:" * _isPrimary";b:1;s:9:" * _flags";a:0:{}s:35:" Doctrine\DBAL\Schema\Index options";a:0:{}}s:20:"idx_1483a5e98157aa0f";r:77;}s:18:" * _primaryKeyName";s:7:"primary";s:20:" * uniqueConstraints";a:0:{}s:17:" * _fkConstraints";a:1:{s:19:"fk_1483a5e98157aa0f";O:41:"Doctrine\DBAL\Schema\ForeignKeyConstraint":7:{s:8:" * _name";s:19:"FK_1483A5E98157AA0F";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:20:" * _localColumnNames";a:1:{s:7:"profile";O:31:"Doctrine\DBAL\Schema\Identifier":3:{s:8:" * _name";s:7:"profile";s:13:" * _namespace";N;s:10:" * _quoted";b:0;}}s:20:" * _foreignTableName";O:31:"Doctrine\DBAL\Schema\Identifier":3:{s:8:" * _name";s:8:"profiles";s:13:" * _namespace";N;s:10:" * _quoted";b:0;}s:22:" * _foreignColumnNames";a:1:{s:2:"id";O:31:"Doctrine\DBAL\Schema\Identifier":3:{s:8:" * _name";s:2:"id";s:13:" * _namespace";N;s:10:" * _quoted";b:0;}}s:10:" * options";a:1:{s:8:"onDelete";s:7:"CASCADE";}}}s:11:" * _options";a:1:{s:14:"create_options";a:0:{}}s:16:" * _schemaConfig";O:33:"Doctrine\DBAL\Schema\SchemaConfig":3:{s:22:" * maxIdentifierLength";i:63;s:7:" * name";N;s:22:" * defaultTableOptions";a:0:{}}}}s:13:" * _sequences";a:0:{}s:16:" * _schemaConfig";r:130;}
    CODE;

    public function up() {

        return Database::raw("ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9CCFA12B8;DROP INDEX IDX_1483A5E9CCFA12B8 ON users;ALTER TABLE users CHANGE profile_id profile INT DEFAULT NULL COMMENT 'user profile';ALTER TABLE users ADD CONSTRAINT FK_1483A5E98157AA0F FOREIGN KEY (profile) REFERENCES profiles (id) ON DELETE CASCADE;CREATE INDEX IDX_1483A5E98157AA0F ON users (profile)");
        
    }

    public function down() {

        return Database::raw("ALTER TABLE users DROP FOREIGN KEY FK_1483A5E98157AA0F;DROP TABLE users");

    }
};