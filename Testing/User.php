<?php

namespace Testing;

require '../vendor/autoload.php';

use DateTime;
use Nexa\Attributes\Common\AutoIncrement;
use Nexa\Attributes\Common\CustomOptions;
use Nexa\Attributes\Common\Length;
use Nexa\Attributes\Common\NotNull;
use Nexa\Attributes\Common\Unsigned;
use Nexa\Attributes\Dates\Date;
use Nexa\Attributes\Dates\DateTime as DatesDateTime;
use Nexa\Attributes\Entities\Entity;
use Nexa\Attributes\Numbers\Fractional;
use Nexa\Attributes\Numbers\Number;
use Nexa\Attributes\Strings\AlphaNumeric;
use Nexa\Attributes\Strings\Text;
use Nexa\Reflection\EntityReflection;

#[Entity('users')]
class User
{

    #[Number]
    #[AutoIncrement(true)]
    public int $id;

    #[AlphaNumeric]
    #[Length(10)]
    public string $firstname;

    #[Fractional]
    #[NotNull(false)]
    #[Length(56)]
    public float $money;

    #[DatesDateTime]
    // #[Unsigned(true)]
    // #[CustomOptions(['test' => 'mouse'])]
    public DateTime $created_at;
}

// $entity = new EntityReflection(User::class);
