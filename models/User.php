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
use Nexa\Attributes\Strings\Strings;
use Nexa\Models\Model;

#[Entity]
class User
{

    #[PrimaryKey]
    #[SmallInt]
    #[AutoIncrement(true)]
    public int $id;

    #[Strings]
    #[DefaultValue('John Doe')]
    public string $username;

    #[Number]
    #[ForeignKey(Profile::class, ['id'], ['onDelete' => 'CASCADE'])]
    #[Comment('user profile')]
    #[Nullable]
    public int $profile_id;

    #[DateAndTime]
    #[DefaultValue('CURRENT_TIMESTAMP')]
    public DateTime $created_at;
}