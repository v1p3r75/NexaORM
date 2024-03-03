<?php

namespace Entities;

use DateTime;
use Nexa\Attributes\Common\AutoIncrement;
use Nexa\Attributes\Common\Nullable;
use Nexa\Attributes\Common\PrimaryKey;
use Nexa\Attributes\Common\Unique;
use Nexa\Attributes\Common\Unsigned;
use Nexa\Attributes\Dates\DateAndTime;
use Nexa\Attributes\Entities\Entity;
use Nexa\Attributes\Numbers\Number;
use Nexa\Attributes\Strings\Strings;

#[Entity('single')]
class SingleEntity
{

    #[Number(11)]
    #[PrimaryKey]
    #[Unsigned]
    #[AutoIncrement]
    public int $id;

    #[Strings(30)]
    public string $img;

    #[Strings(20)]
    #[Nullable(false)]
    #[Unique]
    public string $address;

    #[DateAndTime]
    public DateTime $created_at;
}

// $entity = new EntityReflection(User::class);
