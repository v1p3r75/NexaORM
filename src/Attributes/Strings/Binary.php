<?php

namespace Nexa\Attributes\Strings;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Attributes\AttributeType;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Binary extends AttributeType
{

    protected string $value = Types::BINARY;

}