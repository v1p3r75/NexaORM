<?php

namespace Nexa\Attributes\Common;

use Attribute;
use Nexa\Attributes\AttributeCommon;
use Nexa\Nexa;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Unique extends AttributeCommon
{

    protected string $key = Nexa::UNIQUE_KEY;

    public function __construct(protected $options = []) {}
    
}