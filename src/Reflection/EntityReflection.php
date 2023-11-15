<?php

namespace Nexa\Reflection;

use Doctrine\DBAL\Schema\Schema;
use ReflectionClass;

class EntityReflection extends ReflectionClass
{

    public function __construct(private string $entity) {

        parent::__construct($entity);
    }

    public function getColumns()
    {

        $properties = $this->getProperties();

        $attributes = array_map(function ($propertie){ 
            
            return ['name' => $propertie->getName(), 'attributes' => $propertie->getAttributes()];

        }, $properties);

        $columns = [];

        foreach ($attributes as $attribute) {

            $attribute_name = $attribute['name'];

            $constraints = array_map(function ($definition) {

                $attributeInstance = $definition->newInstance();

                return $attributeInstance->get();

            }, $attribute["attributes"]);

            $columns[$attribute_name]["constraints"] = $this->formatConstraints($constraints);
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
}
