<?php

namespace Testing;

require '../vendor/autoload.php';

use Nexa\Attributes\Common\AutoIncrement;
use Nexa\Attributes\Common\CustomOptions;
use Nexa\Attributes\Common\Length;
use Nexa\Attributes\Common\NotNull;
use Nexa\Attributes\Common\Nullable;
use Nexa\Attributes\Common\Unsigned;
use Nexa\Attributes\Dates\Date;
use Nexa\Attributes\Entities\Entity;
use Nexa\Attributes\Numbers\Fractional;
use Nexa\Attributes\Numbers\Number;
use Nexa\Attributes\Strings\AlphaNumeric;
use Nexa\Attributes\Strings\Text;
use Nexa\Reflection\EntityReflection;

#[Entity]
class User
{

    #[AlphaNumeric]
    #[AutoIncrement(true)]
    public $id;

    #[AlphaNumeric]
    #[Length(10)]
    public $firstname;

    #[Fractional]
    #[NotNull(false)]
    #[Length(56)]
    public $money;

    #[Number]
    #[Unsigned(true)]
    #[CustomOptions(['test' => 'mouse'])]
    public $date_creation;
}

$entity = new EntityReflection(User::class);

// $entity->getColumns();

dump($entity->getColumns());