<?php

namespace Nexa\Attributes;
use Nexa\Interfaces\AttributeInterface;


class AttributeType implements AttributeInterface
{

    protected string $value;

    public function get() {

        return $this->value;
    }
}