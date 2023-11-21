<?php

namespace Models;

use DateTime;
use Nexa\Attributes\Common\AutoIncrement;
use Nexa\Attributes\Common\Comment;
use Nexa\Attributes\Common\DefaultValue;
use Nexa\Attributes\Common\ForeignKey;
use Nexa\Attributes\Common\Nullable;
use Nexa\Attributes\Common\PrimaryKey;
use Nexa\Attributes\Dates\DateAndTime;
use Nexa\Attributes\Entities\Entity;
use Nexa\Attributes\Numbers\Number;
use Nexa\Attributes\Numbers\SmallInt;
use Nexa\Attributes\Strings\AlphaNumeric;

#[Entity('users')]
class User
{

    #[PrimaryKey]
    #[SmallInt]
    #[AutoIncrement(true)]
    public int $id;

    #[AlphaNumeric]
    #[DefaultValue('John Doe')]
    public string $username;

    #[Number]
    #[ForeignKey(Profile::class, ['id'], ['onDeleted' => 'CASCADE'])]
    #[Comment('user profile')]
    #[Nullable]
    public int $profile_id;

    #[DateAndTime]
    public DateTime $created_at;
}