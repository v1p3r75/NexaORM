<?php

namespace Nexa\Attributes;
use Nexa\Interfaces\AttributeInterface;


class AttributeType implements AttributeInterface
{



    public function __construct(protected ?int $length = 255) {

    }
    protected string $value;

    public function get() {

        return [
            'type' => $this->value,
            'params' => ['length' => $this->length]
        ];
    }
}