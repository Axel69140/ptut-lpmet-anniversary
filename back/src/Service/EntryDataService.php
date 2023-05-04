<?php

namespace App\Service;

use App\Entity\User;
use ReflectionClass;

class EntryDataService
{

    public function isDifferentType($value, $parameterType): bool
    {
        if (gettype($value) !== $parameterType->getName()) {

            // Check if type correspond (integer and int are same), then continue
            if (is_int($value) && ($parameterType->getName() === 'int')) {
                return true;
            }

            // Check if type correspond (boolean and bool are same), then continue
            if (is_bool($value) && ($parameterType->getName() === 'bool')) {
                return true;
            }

            if (is_string($value) && ($parameterType->getName() === 'DateTimeInterface'))
            {
                return true;
            }

            // Check if allowed to be null and if entry is null, then continue
            if (($parameterType->allowsNull() === true) && (gettype($value) === 'NULL')) {
                return true;
            }

            // Check if int given for entity attempt
            if ((str_contains($parameterType->getName(), 'App\Entity') && is_int($value))) {
                return true;
            }

            return false;
        }
        return true;

    }

    public function getEntityUsingMail($email, $entityRepos)
    {
        if (empty($entityRepos)) {
            return null;
        }

        $entities = [];
        foreach ($entityRepos as $entityRepo) {
            $existingEntity = $entityRepo->findOneBy(['email' => $email]);
            if ($existingEntity) {
                array_push($entities, $existingEntity);
            }
        }
        return $entities;
    }

    public function defineKeysInEntity($keys, $entity, $em)
    {

        if ($em === null) {
            return null;
        }

        foreach ($keys as $key => $value) {

            // Vérification de l'existence de la propriété dans l'objet
            if (!property_exists($entity::class, $key)) {
                return null;
            }

            $setter = 'set' . ucfirst($key);
            $getter = 'get' . ucfirst($key);

            // Vérification de l'existence de la méthode setter pour la propriété
            if (!method_exists($entity::class, $setter) || !method_exists($entity::class, $getter)) {
                return null;
            }

            $reflectionClass = new ReflectionClass($entity::class);

            // Récupération de la méthode set
            $setterMethod = $reflectionClass->getMethod($setter);

            // Récupération du type voulu par le setter
            $parameterType = $setterMethod->getParameters()[0]->getType();
            $parameterTypeName = $parameterType->getName();

            if (!$this->isDifferentType($value, $parameterType)) {
                return null;
            }

            // Check si la fonction pour les users est correcte
            if ($entity::class === User::class) {

                $settingsRepository = $em->getRepository('App\Entity\Settings');
                if ($key === 'function' && !in_array($value, $settingsRepository->findAll()[0]->getAllowedFunctions())) {
                    return null;
                }

            }

            // Appel de la méthode setter pour modifier la propriété
            try {
                if (str_contains($parameterTypeName, 'App\Entity')) {

                    $entityRepository = $em->getRepository($parameterTypeName);
                    $entityToAffiliate = $entityRepository->findOneBy(['id' => $value]);

                    if (!$entityToAffiliate) {
                        return null;
                    }

                    $entity->$setter($entityToAffiliate);

                } else {

                    if($parameterTypeName === 'DateTimeInterface')
                    {
                        $value = new \DateTime($value);
                    }

                    $entity->$setter($value);

                }

            } catch (\Exception $e) {

                return null;

            }

        }               
        
        return $entity;
    }
}