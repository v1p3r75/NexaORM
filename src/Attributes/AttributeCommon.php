<?php

namespace Nexa\Attributes;
use Nexa\Interfaces\AttributeInterface;


class AttributeCommon implements AttributeInterface
{

    protected string $key;

    protected $value;

    public function get() {

        $data = [$this->key => $this->value];

        return [...$data];
    }
}