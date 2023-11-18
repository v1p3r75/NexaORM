<?php

namespace Nexa\Attributes\Common;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Attributes\AttributeCommon;
use Nexa\Attributes\AttributeType;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PrimaryKey extends AttributeCommon
{

    protected string $key = "primary_key";

    public function __construct(protected $value = true) {}
    
}
