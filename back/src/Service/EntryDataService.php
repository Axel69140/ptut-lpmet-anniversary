<?php

namespace App\Service;

use App\Entity\User;

class EntryDataService
{

    public function defineKeysInEntity($keys, $entity, $em)
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

//            $entityManager = $doctrine->getManagerForClass($entity::class);
//            $classMetadata = $entityManager->getClassMetadata($entity::class);
//            $fieldType = $classMetadata->getTypeOfField($key);

            $metadata = $em->getClassMetadata($entity::class);
            $type = $metadata->getTypeOfField($key);

            if(gettype($value) !== $type)
            {
                var_dump($key);
                var_dump('fieldType ' . $type);
                var_dump('gettype ' . gettype($value));
                var_dump('not same type');
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