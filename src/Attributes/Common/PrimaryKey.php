<?php

namespace Nexa\Attributes\Common;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Attributes\AttributeCommon;
use Nexa\Attributes\AttributeType;
use Nexa\Nexa;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PrimaryKey extends AttributeCommon
{

    protected string $key = Nexa::PRIMARY_KEY;

    public function __construct(protected $value = true) {}
    
}
