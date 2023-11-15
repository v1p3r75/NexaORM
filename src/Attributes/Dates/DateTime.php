<?php

namespace Nexa\Attributes\Dates;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Attributes\AttributeType;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DateTime extends AttributeType
{

    protected string $value = Types::DATETIMETZ_MUTABLE;
    
}
