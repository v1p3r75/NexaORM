<?php

namespace Nexa\Attributes\Strings;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Interfaces\AttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Text implements AttributeInterface
{
    public function __construct(public int $len = 255) {}

    public function getType() {

        return Types::TEXT;
    }
}