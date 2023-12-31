<?php

namespace Models;

use DateTime;
use Nexa\Attributes\Common\AutoIncrement;
use Nexa\Attributes\Common\CustomOptions;
use Nexa\Attributes\Common\Nullable;
use Nexa\Attributes\Common\PrimaryKey;
use Nexa\Attributes\Common\Unsigned;
use Nexa\Attributes\Dates\Date;
use Nexa\Attributes\Dates\DateAndTime;
use Nexa\Attributes\Entities\Entity;
use Nexa\Attributes\Numbers\Fractional;
use Nexa\Attributes\Numbers\Number;
use Nexa\Attributes\Numbers\SmallInt;
use Nexa\Attributes\Strings\Strings;
use Nexa\Attributes\Strings\Text;
use Nexa\Reflection\EntityReflection;

#[Entity('single')]
class Single
{

    #[Number(11)]
    #[PrimaryKey]
    #[Unsigned]
    #[AutoIncrement]
    public int $id;

    #[Strings(50)]
    public string $img;

    #[Strings(20)]
    #[Nullable(false)]
    public string $address;

    #[DateAndTime]
    public DateTime $created_at;
}

// $entity = new EntityReflection(User::class);
