<?php

namespace Nexa\Attributes\Numbers;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Interfaces\AttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Boolean implements AttributeInterface
{
    public function getType() {

        return Types::BOOLEAN;
    }
}