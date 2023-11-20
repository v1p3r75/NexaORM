<?php

namespace Nexa\Attributes\Dates;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Attributes\AttributeType;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DateAndTime extends AttributeType
{

    public function __construct() {

        parent::__construct(null);
    }
    protected string $value = Types::DATETIMETZ_MUTABLE;
    
}
