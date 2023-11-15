<?php

namespace Nexa\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Entity
{
    
    public function __construct(?string $tableName) {}
}