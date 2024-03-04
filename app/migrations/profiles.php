<?php

use Nexa\Interfaces\MigrationInterface;
use Nexa\Databases\Database;

return new class implements MigrationInterface
{

    public string $table = "profiles";

    public string $entity = "Entities\ProfileEntity";

    public string $schema = <<<CODE
    O:27:"Doctrine\DBAL\Schema\Schema":7:{s:8:" * _name";s:0:"";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:39:" Doctrine\DBAL\Schema\Schema namespaces";a:0:{}s:10:" * _tables";a:1:{s:9:".profiles";O:26:"Doctrine\DBAL\Schema\Table":11:{s:8:" * _name";s:8:"profiles";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:11:" * _columns";a:4:{s:2:"id";O:27:"Doctrine\DBAL\Schema\Column":15:{s:8:" * _name";s:2:"id";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:8:" * _type";O:31:"Doctrine\DBAL\Types\IntegerType":0:{}s:10:" * _length";i:255;s:13:" * _precision";N;s:9:" * _scale";i:0;s:12:" * _unsigned";b:0;s:9:" * _fixed";b:0;s:11:" * _notnull";b:1;s:11:" * _default";N;s:17:" * _autoincrement";b:1;s:19:" * _platformOptions";a:0:{}s:20:" * _columnDefinition";N;s:11:" * _comment";s:0:"";}s:3:"img";O:27:"Doctrine\DBAL\Schema\Column":15:{s:8:" * _name";s:3:"img";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:8:" * _type";O:30:"Doctrine\DBAL\Types\StringType":0:{}s:10:" * _length";i:255;s:13:" * _precision";N;s:9:" * _scale";i:0;s:12:" * _unsigned";b:0;s:9:" * _fixed";b:0;s:11:" * _notnull";b:1;s:11:" * _default";N;s:17:" * _autoincrement";b:0;s:19:" * _platformOptions";a:0:{}s:20:" * _columnDefinition";N;s:11:" * _comment";s:0:"";}s:7:"address";O:27:"Doctrine\DBAL\Schema\Column":15:{s:8:" * _name";s:7:"address";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:8:" * _type";r:32;s:10:" * _length";i:255;s:13:" * _precision";N;s:9:" * _scale";i:0;s:12:" * _unsigned";b:0;s:9:" * _fixed";b:0;s:11:" * _notnull";b:1;s:11:" * _default";N;s:17:" * _autoincrement";b:0;s:19:" * _platformOptions";a:0:{}s:20:" * _columnDefinition";N;s:11:" * _comment";s:0:"";}s:10:"created_at";O:27:"Doctrine\DBAL\Schema\Column":15:{s:8:" * _name";s:10:"created_at";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:8:" * _type";O:28:"Doctrine\DBAL\Types\DateType":0:{}s:10:" * _length";N;s:13:" * _precision";N;s:9:" * _scale";i:0;s:12:" * _unsigned";b:0;s:9:" * _fixed";b:0;s:11:" * _notnull";b:1;s:11:" * _default";N;s:17:" * _autoincrement";b:0;s:19:" * _platformOptions";a:0:{}s:20:" * _columnDefinition";N;s:11:" * _comment";s:0:"";}}s:43:" Doctrine\DBAL\Schema\Table implicitIndexes";a:0:{}s:11:" * _indexes";a:1:{s:7:"primary";O:26:"Doctrine\DBAL\Schema\Index":8:{s:8:" * _name";s:7:"primary";s:13:" * _namespace";N;s:10:" * _quoted";b:0;s:11:" * _columns";a:1:{s:2:"id";O:31:"Doctrine\DBAL\Schema\Identifier":3:{s:8:" * _name";s:2:"id";s:13:" * _namespace";N;s:10:" * _quoted";b:0;}}s:12:" * _isUnique";b:1;s:13:" * _isPrimary";b:1;s:9:" * _flags";a:0:{}s:35:" Doctrine\DBAL\Schema\Index options";a:0:{}}}s:18:" * _primaryKeyName";s:7:"primary";s:20:" * uniqueConstraints";a:0:{}s:17:" * _fkConstraints";a:0:{}s:11:" * _options";a:1:{s:14:"create_options";a:0:{}}s:16:" * _schemaConfig";O:33:"Doctrine\DBAL\Schema\SchemaConfig":3:{s:22:" * maxIdentifierLength";i:63;s:7:" * name";N;s:22:" * defaultTableOptions";a:0:{}}}}s:13:" * _sequences";a:0:{}s:16:" * _schemaConfig";r:96;}
    CODE;

    public function up() {

        return Database::raw("CREATE TABLE profiles (id INT AUTO_INCREMENT NOT NULL, img VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, created_at DATE NOT NULL, PRIMARY KEY(id))");
        
    }

    public function down() {

        return Database::raw("DROP TABLE profiles");

    }
};