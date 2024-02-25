<?php

namespace Nexa\Reflection;

use Doctrine\Inflector\Inflector;
use Nexa\Attributes\Entities\Entity;
use ReflectionClass;

class EntityReflection extends ReflectionClass
{

    public function __construct(private readonly object | string $entity)
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

            if(! $constraints) continue; // if the property have not an attribute

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

        $optionsList = array_filter(
            $constraints,
            fn($constraint) => array_key_exists('options', $constraint)
        );

        $typesList = array_filter(
            $constraints,
            fn($constraint) => array_key_exists('type', $constraint)
        );

        $first = array_key_first($typesList); // select the first type if there are more types.

        $type = $typesList[$first];

        $options = array_map(function($options) {

            return $options['options'];

        }, $optionsList);

        $options = array_merge($type['params'], ...$options); // Merge options with constructor params

        return [$type['type'], $options];

    }

    public function getTable(Inflector $inflector)
    {

        $entityAttr = $this->getAttributes(Entity::class);

        if (isset($entityAttr[0], $entityAttr[0]->getArguments()[0])) {

            return $entityAttr[0]->getArguments()[0]; // return table name if present
        }

        // Returns the entity class name in plural form.

        $entityName = explode('\\', $this->entity);
        $name = end($entityName);

        // delete 'entity' suffix if exists
        if (str_ends_with($name, 'Entity')) {
            $name = preg_replace('#Entity$#', '', $name);
        }

        return strtolower($inflector->pluralize($name));
    }

    public function getEntity() {

        return $this->entity;
    }
}
