<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class EntryDataService
{

    public function defineKeysInEntity($keys, $entity)
    {
        foreach ($keys as $key => $value) {

            // Vérification de l'existence de la propriété dans l'objet User
            if (!property_exists($entity::class, $key)) {
                return null;
            }

            $setter = 'set' . ucfirst($key);

            // Vérification de l'existence de la méthode setter pour la propriété
            if (!method_exists($entity::class, $setter)) {
                return null;
            }

            // Appel de la méthode setter pour modifier la propriété
            try {
                $entity->$setter($value);
            } catch (\Exception $e) {
                return null;
            }

        }
        return $entity;
    }
}