<?php

namespace App\Service;

use ReflectionClass;

class EntryDataService
{

    public function defineKeysInEntity($keys, $entity)
    {
        foreach ($keys as $key => $value) {

            // Vérification de l'existence de la propriété dans l'objet User
            if (!property_exists($entity::class, $key)) {
                var_dump('property exists ' . $key);
                return null;
            }

            $setter = 'set' . ucfirst($key);
            $getter = 'get' . ucfirst($key);

            // Vérification de l'existence de la méthode setter pour la propriété
            if (!method_exists($entity::class, $setter) || !method_exists($entity::class, $getter)) {
                var_dump($key);
                var_dump('not method exists');
                return null;
            }

            // Get the reflection class of your entity
            $reflectionClass = new ReflectionClass($entity::class);

            // Get the setter method you're interested in
            $setterMethod = $reflectionClass->getMethod($setter);

            // Get the type hint of the first parameter of the method
            $parameterType = $setterMethod->getParameters()[0]->getType();

            // If the type hint is a class or interface, get its name
//            if ($parameterType && !$parameterType->isBuiltin()) {
//                $parameterTypeName = $parameterType->getName();
//            }

            // Check if type are different
            if(gettype($value) !== $parameterType->getName())
            {
                // Check if type correspond (boolean and bool are same), then continue
                if((gettype($value) === 'boolean') && ($parameterType->getName() === 'bool')){
                    continue;
                }

                // Check if allowed to be null and if entry is null, then continue
                if(($parameterType->allowsNull() === true) && (gettype($value) === 'NULL')){
                    continue;
                }

                return null;
            }

            //Check if function is correct
            $functions = $entity->getAllowedFunctions();
            if($key === 'function' && in_array($value, $functions))
            {
                var_dump('test');
                return null;
            }

            // Appel de la méthode setter pour modifier la propriété
            try {
                $entity->$setter($value);
            } catch (\Exception $e) {
                var_dump('setter');
                return null;
            }

        }
        return $entity;
    }
}