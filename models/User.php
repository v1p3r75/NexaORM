<?php

namespace Models;

use DateTime;
use Nexa\Attributes\Common\AutoIncrement;
use Nexa\Attributes\Common\ForeignKey;
use Nexa\Attributes\Common\Length;
use Nexa\Attributes\Common\PrimaryKey;
use Nexa\Attributes\Dates\DateAndTime;
use Nexa\Attributes\Entities\Entity;
use Nexa\Attributes\Numbers\Number;
use Nexa\Attributes\Numbers\SmallInt;
use Nexa\Attributes\Strings\AlphaNumeric;

#[Entity('users')]
class User
{

    #[SmallInt]
    #[PrimaryKey]
    #[AutoIncrement(true)]
    public int $id;

    #[AlphaNumeric]
    #[Length(10)]
    public string $username;

    #[Number]
    #[ForeignKey(Profile::class, ['id'])]
    public int $post_id;

    #[DateAndTime]
    public DateTime $created_at;
}

// $entity = new EntityReflection(User::class);
