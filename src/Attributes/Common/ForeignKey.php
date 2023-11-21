<?php

namespace Nexa\Attributes\Common;

use Attribute;
use Nexa\Attributes\AttributeCommon;
use Nexa\Attributes\Entities\Entity;
use Nexa\Nexa;
use Nexa\Reflection\EntityReflection;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ForeignKey extends AttributeCommon
{

    public function __construct(
        private $foreignEntity,
        private array $foreignColumnsNames,
        private array $options = []){

    }

    public function get()
    {
        $reflexion = new EntityReflection($this->foreignEntity);

        return [
            'options' => [
                Nexa::FOREIGN_KEY => [
                    $reflexion->getTable(), $this->foreignColumnsNames, $this->options
                ]
            ]
        ];
    }
}