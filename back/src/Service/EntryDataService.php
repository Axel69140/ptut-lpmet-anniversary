<?php

namespace App\Service;

use App\Entity\User;
use ReflectionClass;
use App\Repository\SettingsRepository;

class EntryDataService
{
    public function isDifferentType($value, $parameterType): bool
    {
        if(gettype($value) !== $parameterType->getName())
        {
            // Check if type correspond (boolean and bool are same), then continue
            if(is_bool($value) && ($parameterType->getName() === 'bool'))
            {
                return true;
            }

            // Check if allowed to be null and if entry is null, then continue
            if(($parameterType->allowsNull() === true) && (gettype($value) === 'NULL'))
            {
                return true;
            }

            // Check if int given for entity attempt
            if((str_contains($parameterType->getName(), 'App\Entity') && is_int($value)))
            {
                return true;
            }

            return false;
        }
        return true;
    }

    public function getEntityUsingMail($email, $entityRepos)
    {
        if(empty($entityRepos))
        {
            return null;
        }

        $entities = [];
        foreach ($entityRepos as $entityRepo)
        {
            $existingEntity = $entityRepo->findOneBy(['email' => $email]);
            if($existingEntity)
            {
                array_push($entities, $existingEntity);
            }
        }
        return $entities;
    }

    public function defineKeysInEntity($keys, $entity, $em = null)
    {
        foreach ($keys as $key => $value) {

            // Vérification de l'existence de la propriété dans l'objet User
            if (!property_exists($entity::class, $key)) {
                var_dump('property doesn\'t exists ' . $key);
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
            $parameterTypeName = $parameterType->getName();

            // If the type hint is a class or interface, get its name
//            if ($parameterType && !$parameterType->isBuiltin()) {
//                $parameterTypeName = $parameterTypeName;
//            }

            // Check if type are different
            if(!$this->isDifferentType($value, $parameterType))
            {
                return null;
            }

            //Check if function is correct
            if($entity::class === User::class){
                // Check if $em null
                if($em === null)
                {
                    return null;
                }

                $settingsRepository = $em->getRepository('App\Entity\Settings');
                if($key === 'function' && !in_array($value, $settingsRepository->findAll()[0]->getAllowedFunctions()))
                {
                    return null;
                }
            }

            // Appel de la méthode setter pour modifier la propriété
            try {
                if(str_contains($parameterTypeName, 'App\Entity'))
                {

                    // Check if $em null
                    if($em === null)
                    {
                        return null;
                    }

//                    if (!$metadata->hasAssociation($key)) {
//                        throw new \InvalidArgumentException(sprintf('The entity %s does not have an association named %s', $parameterTypeName, $key));
//                    }

//                    $targetEntity = $metadata->getAssociationTargetClass($key);

                    $entityRepository =  $em->getRepository($parameterTypeName);
                    $entityToAffiliate = $entityRepository->findOneBy(['id' => $value]);

                    if(!$entityToAffiliate)
                    {
                        return null;
                    }

                    $entity->$setter($entityToAffiliate);

                } else {

                    $entity->$setter($value);

                }

            } catch (\Exception $e) {

                return null;

            }

        }
        return $entity;
    }
}