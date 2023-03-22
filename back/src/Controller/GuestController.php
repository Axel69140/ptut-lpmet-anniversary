<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GuestRepository;
use App\Service\RequestService;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Guest;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/guests')]
class GuestController extends AbstractController
{
    // Get guests
    #[Route('/', name: 'app_api_guest_get', methods: ['GET'])]
    public function getGuests(GuestRepository $guestRepository): JsonResponse
    {
        try {
            $guests = $guestRepository->findAll();
            return $this->json($guests, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Get one guests
    #[Route('/{id}', name: 'app_api_guest_get_one', methods: ['GET'])]
    public function getGuestById(GuestRepository $guestRepository, int $id): JsonResponse
    {
        try {
            $guest = $guestRepository->find($id);

            if (!$guest) {
                return $this->json([
                    'error' => 'Guest not found'
                ], 404);
            }

            return $this->json($guest, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Create guest
    #[Route('/create', name: 'app_api_guest_post', methods: ['POST'])]
    public function createGuest(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                return $this->json([
                    'error' => 'No data provided'
                ], 400);
            }

            $requiredFields = ['firstName', 'lastName', 'email', 'id_user'];

            // Vérification de la présence de tous les champs requis
            foreach ($requiredFields as $field) {
                if (!isset($content[$field])) {
                    return $this->json([
                        'error' => "Missing field '$field'"
                    ], 400);
                }
            }

            // Vérification des types des champs requis
            if (!is_string($content['firstName']) || !is_string($content['lastName']) || !is_string($content['email']) || !is_numeric($content['id_user'])) {
                return $this->json([
                    'error' => 'One or more filled-in field(s) has/have a wrong type'
                ], 400);
            }

            $email = $content['email'];

            // Vérification de l'existence de l'utilisateur
            $existingGuest = $entityManager->getRepository(Guest::class)->findOneBy(['email' => $email]);
            if ($existingGuest !== null) {
                return $this->json([
                    'error' => 'Email already exists'
                ], 400);
            }

            $guest = $serializer->deserialize($request->getContent(), Guest::class, 'json');

            $entityManager->persist($guest);
            $entityManager->flush();

            return $this->json($guest, 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Update guest
    #[Route('/{id}', name: 'app_api_guest_update', methods: ['PATCH'])]
    public function updateGuest(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        try {
            $guest = $entityManager->getRepository(Guest::class)->find($id);

            // Vérification de l'existence de l'guest
            if ($guest === null) {
                return $this->json([
                    'error' => 'Guest not found'
                ], 404);
            }

            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                return $this->json([
                    'error' => 'No data provided'
                ], 400);
            }

            foreach ($content as $key => $value) {
                // Vérification de l'existence de la propriété dans l'objet Guest
                if (!property_exists(Guest::class, $key)) {
                    return $this->json([
                        'error' => "Unknown property '$key'"
                    ], 400);
                }

                $setter = 'set' . ucfirst($key);

                // Vérification de l'existence de la méthode setter pour la propriété
                if (!method_exists(Guest::class, $setter)) {
                    return $this->json([
                        'error' => "No setter found for property '$key'"
                    ], 500);
                }

                // Appel de la méthode setter pour modifier la propriété
                $guest->$setter($value);
            }

            $entityManager->flush();

            return $this->json($guest, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Delete guests
    #[Route('/many', name: 'app_api_guest_delete_many', methods: ['DELETE'])]
    public function deleteGuests(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $ids = $data['id'] ?? [];
    
            if (empty($ids)) {
                return $this->json([
                    'error' => 'No IDs provided'
                ], 400);
            }
    
            $guests = $entityManager->getRepository(Guest::class)->findBy([
                'id' => $ids
            ]);
    
            if (empty($guests)) {
                return $this->json([
                    'error' => 'Guests not found'
                ], 404);
            }
    
            foreach ($guests as $guest) {
                $entityManager->remove($guest);
            }
    
            $entityManager->flush();
    
            return $this->json([], 204);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }  

    // Clear guests
    #[Route('/clear', name: 'app_api_guest_delete_all', methods: ['DELETE'])]
    public function clearGuests(EntityManagerInterface $entityManager): Response
    {
        try {
            $guestRepository = $entityManager->getRepository(Guest::class);
            $guests = $guestRepository->findAll();

            foreach ($guests as $guest) {
                $entityManager->remove($guest);
            }

            $entityManager->flush();

            return $this->json([], 204);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Delete guest
    #[Route('/{id}', name: 'app_api_guest_delete', methods: ['DELETE'])]
    public function deleteGuest(Guest $guest = null, EntityManagerInterface $entityManager): Response
    {
        try {
            if (!$guest) {
                return $this->json([
                    'error' => 'Guest not found'
                ], 404);
            }

            $entityManager->remove($guest);
            $entityManager->flush();

            return $this->json([], 204);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    } 
}
