<?php

namespace Nexa\Attributes\Common;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Attributes\AttributeCommon;
use Nexa\Attributes\AttributeType;

#[Attribute(Attribute::TARGET_PROPERTY)]
class NotNull extends AttributeCommon
{

    protected string $key = "notnull";

    public function __construct(protected $value = true) {}
    
}
