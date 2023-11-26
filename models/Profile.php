<?php

namespace Models;

use DateTime;
use Nexa\Attributes\Common\AutoIncrement;
use Nexa\Attributes\Common\DefaultValue;
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
use Nexa\Models\Model;
use Nexa\Reflection\EntityReflection;

#[Entity]
class Profile
{
    use Model;

    #[Number]
    #[PrimaryKey]
    #[AutoIncrement]
    public int $id;

    #[Strings]
    public string $img;

    #[Strings]
    public string $address;

    #[Date]
    public DateTime $created_at;
}

// $entity = new EntityReflection(User::class);
