<?php

namespace Nexa\Attributes\Dates;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Interfaces\AttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DateTime implements AttributeInterface
{
    public function getType() {

        return Types::DATETIMETZ_MUTABLE;
    }
}
