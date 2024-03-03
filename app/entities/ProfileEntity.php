<?php

namespace Entities;

use DateTime;
use Nexa\Attributes\Common\AutoIncrement;
use Nexa\Attributes\Common\PrimaryKey;
use Nexa\Attributes\Common\Unique;
use Nexa\Attributes\Dates\Date;
use Nexa\Attributes\Entities\Entity;
use Nexa\Attributes\Numbers\Number;
use Nexa\Attributes\Strings\Strings;

#[Entity]
class ProfileEntity
{

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