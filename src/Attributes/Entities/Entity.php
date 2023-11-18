<?php

namespace Nexa\Attributes\Entities;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Entity
{
    
    public function __construct(protected string $tableName) {}
}