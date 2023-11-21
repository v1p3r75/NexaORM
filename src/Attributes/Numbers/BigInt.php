<?php

namespace Nexa\Attributes\Numbers;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Attributes\AttributeType;

#[Attribute(Attribute::TARGET_PROPERTY)]
class BigInt extends AttributeType
{
    public function __construct(int $length = 255) {

        parent::__construct($length);
    }
    protected string $value = Types::BIGINT;

}