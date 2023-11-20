<?php

namespace Nexa\Attributes\Strings;

use Attribute;
use Doctrine\DBAL\Types\Types;
use Nexa\Attributes\AttributeType;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Blob extends AttributeType
{

    public function __construct(int $length = 255) {

        parent::__construct($length);
    }

    protected string $value = Types::BLOB;

}