<?php

namespace App\Controller;

use App\Repository\SettingsRepository;
use App\Repository\UserRepository;
use App\Service\EntryDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GuestRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Guest;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/guests')]
class GuestController extends AbstractController
{
    // Get guests
    #[Route('/', name: 'app_api_guests_get_all', methods: ['GET'])]
    public function getGuests(GuestRepository $guestRepository): JsonResponse
    {
        try {

            $guests = $guestRepository->findAll();

            return $this->json($guests, 200, [], ['groups' => ['guest-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get one guest
    #[Route('/{id}', name: 'app_api_guests_get', methods: ['GET'])]
    public function getGuestById(int $id, GuestRepository $guestRepository): JsonResponse
    {
        try {

            $guest = $guestRepository->find($id);

            if (!$guest) {
                return $this->json([
                    'error' => 'Guest not found'
                ], 404);
            }

            return $this->json($guest, 200, [], ['groups' => ['guest-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Create guest
    #[Route('/create', name: 'app_api_guests_create', methods: ['POST'])]
    public function createGuest(Request $request, EntryDataService $entryDataService, GuestRepository $guestRepository, ValidatorInterface $validator, EntityManagerInterface $em, UserRepository $userRepository, SettingsRepository $settingsRepository): JsonResponse
    {
        try {

            // Entries verifications
            $content = json_decode($request->getContent(), true);
            $guest = new Guest();
            $guest = $entryDataService->defineKeysInEntity($content, $guest, $em);
            if ($guest === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity creation'
                ], 400);
            }

            // Check if inviter already has reach guest limit
            if((count($guest->getInvitedBy()->getGuests()) > ($settingsRepository->findAll()[0]->getMaxNumberGuests() - 1)) && !in_array("ROLE_ADMIN", $guest->getInvitedBy()->getRoles()))
            {
                return $this->json([
                    'error' => 'The number of guests of this user reach the maximum. He can\'t invite someone else'
                ], 400);
            }

            // Check if mail is available
            $existingEntities = $entryDataService->getEntityUsingMail($guest->getEmail(), [$userRepository, $guestRepository]);
            if(is_null($existingEntities))
            {

                return $this->json([
                    'error' => 'No repository provided'
                ], 403);

            } else if (count($existingEntities) > 0) {

                return $this->json([
                    'error' => 'Email already used'
                ], 403);

            }

            // Check if inviter is participating, can't invite if not
            if($guest->getInvitedBy()->getIsParticipated() === false)
            {
                return $this->json([
                    'error' => 'You can\'t invite someone if you don\'t participate'
                ], 400);
            }

            //Symfony validation
            $errors = $validator->validate($guest);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $guestRepository->save($guest, true);
            return $this->json($guest, 201, [], ['groups' => ['guest-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error' . $e
            ], 500);

        }
    }

    // Update guest
    #[Route('/{id}', name: 'app_api_guests_update', methods: ['PATCH'])]
    public function updateGuest(int $id, Request $request, UserRepository $userRepository, GuestRepository $guestRepository, EntryDataService $entryDataService, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $guestToUpdate = $guestRepository->findOneBy(['id' => $id]);
            if (!$guestToUpdate) {
                return $this->json([
                    'error' => 'Guest not found'
                ], 404);
            }

            $guestToUpdate = $entryDataService->defineKeysInEntity($content, $guestToUpdate, $em);
            if ($guestToUpdate === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity modification'
                ], 400);
            }

            // Check if mail is available
            $existingEntities = $entryDataService->getEntityUsingMail($guestToUpdate->getEmail(), [$userRepository, $guestRepository]);
            if (is_null($existingEntities)) {

                return $this->json([
                    'error' => 'No repository provided'
                ], 403);

            } else if(count($existingEntities) > 1) {

                return $this->json([
                    'error' => 'Email already used'
                ], 403);

            } else if((count($existingEntities) === 1) && (get_class($guestToUpdate) !== get_class($existingEntities[0]))) {

                return $this->json([
                    'error' => 'Email already used'
                ], 403);

            } else if ((count($existingEntities) === 1) && (get_class($guestToUpdate) === get_class($existingEntities[0])) && ($existingEntities[0]->getId() !== $guestToUpdate->getId())) {

                return $this->json([
                    'error' => 'Email already used'
                ], 403);

            }

            //Symfony validation
            $errors = $validator->validate($guestToUpdate);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $guestRepository->save($guestToUpdate, true);
            return $this->json($guestToUpdate, 200, [], ['groups' => ['guest-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error' . $e
            ], 500);

        }
    }

    // Delete guests
    #[Route('/many', name: 'app_api_guests_delete_many', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
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

            $guestsToDelete = $entityManager->getRepository(Guest::class)->findBy([
                'id' => $ids
            ]);

            if (empty($guestsToDelete)) {
                return $this->json([
                    'error' => 'Guests not found'
                ], 404);
            }

            foreach ($guestsToDelete as $guest) {
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
    #[Route('/clear', name: 'app_api_guests_delete_all', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function clearGuests(EntityManagerInterface $entityManager): Response
    {
        try {

            $guestsToDelete = $entityManager->getRepository(Guest::class)->findAll();

            if(!$guestsToDelete)
            {
                return $this->json([
                    'error' => 'Guests not found'
                ], 404);
            }

            foreach ($guestsToDelete as $guest)
            {
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
    #[Route('/{id}', name: 'app_api_guests_delete', methods: ['DELETE'])]
    public function deleteGuest(int $id, GuestRepository $guestRepository): Response
    {
        try {

            $guestToDelete = $guestRepository->findOneBy(['id' => $id]);

            if(!$guestToDelete)
            {
                return $this->json([
                    'error' => 'Guest not found'
                ], 404);
            }

            $guestRepository->remove($guestToDelete, true);
            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }
}
