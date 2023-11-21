<?php

namespace Nexa\Attributes\Common;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Attributes\AttributeCommon;
use Nexa\Attributes\AttributeType;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Unsigned extends AttributeCommon
{

    protected string $key = "unsigned";

    public function __construct(protected $value = true) {}
    
}
