<?php

namespace Nexa\Reflection;

use Nexa\Attributes\Entities\Entity;
use ReflectionClass;

class EntityReflection extends ReflectionClass
{

    public function __construct(object | string $entity)
    {

        parent::__construct($entity);
    }

    public function getColumns(): array
    {

        $properties = $this->getProperties();

        $attributes = array_map(function ($property) {

            return ['name' => $property->getName(), 'attributes' => $property->getAttributes()];
        }, $properties);

        $columns = [];

        foreach ($attributes as $attribute) {

            $attribute_name = $attribute['name'];

            $constraints = array_map(function ($definition) {

                $attributeInstance = $definition->newInstance();

                return $attributeInstance->get(); // get attr type name (string, date, integer,...)
            }, $attribute["attributes"]);

            $columns[] = [
                'name' => $attribute_name,
                "constraints" => $this->formatConstraints($constraints)
            ];
        }

        return $columns;
    }

    /**
     * Merge all constraints without a first constraints (type of columns)
     * */

    private function formatConstraints(array $constraints): array
    {

        if (count($constraints) > 1) {

            $options = array_slice($constraints, 1);
            $mergedOptions = array_merge(...$options);

            return [$constraints[0], $mergedOptions];
        }

        return $constraints;
    }

    public function getTable()
    {

        $entityAttr = $this->getAttributes(Entity::class);

        if (isset($entityAttr[0]) && isset($entityAttr[0]->getArguments()[0])) {

            return $entityAttr[0]->getArguments()[0];
        }

        return "none";
    }
}
