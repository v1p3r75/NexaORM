<?php

namespace Nexa\Attributes\Dates;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Interfaces\AttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Date implements AttributeInterface
{

    public function getType() {

        return Types::DATE_MUTABLE;
    }
}
