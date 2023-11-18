<?php

namespace Models;

use DateTime;
use Nexa\Attributes\Common\AutoIncrement;
use Nexa\Attributes\Common\CustomOptions;
use Nexa\Attributes\Common\Length;
use Nexa\Attributes\Common\NotNull;
use Nexa\Attributes\Common\PrimaryKey;
use Nexa\Attributes\Common\Unsigned;
use Nexa\Attributes\Dates\Date;
use Nexa\Attributes\Dates\DateAndTime;
use Nexa\Attributes\Entities\Entity;
use Nexa\Attributes\Numbers\Fractional;
use Nexa\Attributes\Numbers\Number;
use Nexa\Attributes\Numbers\SmallInt;
use Nexa\Attributes\Strings\AlphaNumeric;
use Nexa\Attributes\Strings\Text;
use Nexa\Reflection\EntityReflection;

#[Entity('users2')]
class User2
{

    #[Number]
    #[PrimaryKey]
    #[AutoIncrement(true)]
    public int $id;

    #[AlphaNumeric]
    #[Length(10)]
    public string $firstname;

    #[SmallInt]
    #[NotNull(false)]
    public float $money;

    #[DateAndTime]
    public DateTime $created_at;
}

// $entity = new EntityReflection(User::class);
