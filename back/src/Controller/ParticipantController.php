<?php

namespace App\Controller;

use App\Entity\User;
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

#[Route('/participants')]
class ParticipantController extends AbstractController
{
    // Get participants
    #[Route('/', name: 'app_api_participant_get', methods: ['GET'])]
    public function getParticipants(GuestRepository $guestRepository, UserRepository $userRepository): JsonResponse
    {
        try {

            $participants = array_merge($userRepository->findBy(['isParticipated' => true]), $guestRepository->findAll());
            return $this->json($participants, 200, [], ['groups' => ['participant-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage()
            ], 500);

        }
    }

    // Get one participant
    #[Route('/{email}', name: 'app_api_participant_get_one', methods: ['GET'])]
    public function getParticipantByEmail(string $email, GuestRepository $guestRepository, UserRepository $userRepository): JsonResponse
    {
        try {

            $guest = $guestRepository->findBy(['email' => $email]);

            if($guest)
            {
                return $this->json($guest, 200, [], ['groups' => ['participant-return']]);
            }

            $user = $userRepository->findBy(['email' => $email, 'isParticipated' => true]);

            if($user)
            {
                return $this->json($user, 200, [], ['groups' => ['participant-return']]);
            } else {

                return $this->json([
                    'error' => 'Participant not found'
                ], 404);

            }

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Delete participants
    #[Route('/many', name: 'app_api_participant_delete_many', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function deleteParticipants(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {

            $data = json_decode($request->getContent(), true);
            $emails = $data['email'] ?? [];
    
            if (empty($emails)) {
                return $this->json([
                    'error' => 'No emails provided'
                ], 400);
            }

            $usersToDelete = $entityManager->getRepository(User::class)->findBy([
                'email' => $emails
            ]);

            if(!empty($usersToDelete))
            {
                foreach ($usersToDelete as $user) {
                    $user->setIsParticipated(false);
                    $entityManager->persist($user);
                }
            }

            $guestsToDelete = $entityManager->getRepository(Guest::class)->findBy([
                'email' => $emails
            ]);

            if(!empty($guestsToDelete))
            {
                foreach ($guestsToDelete as $guest) {
                    $entityManager->remove($guest);
                }
            }

            if(empty($guestsToDelete) && empty($usersToDelete)) {
                return $this->json([
                    'error' => 'Participants not found'
                ], 404);
            }

            $entityManager->flush();
            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }  

    // Clear participants
    #[Route('/clear', name: 'app_api_participant_delete_all', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function clearParticipants(EntityManagerInterface $entityManager): Response
    {
        try {

            $guestsToDelete = $entityManager->getRepository(Guest::class)->findAll();
            $usersToDelete = $entityManager->getRepository(User::class)->findBy(['isParticipated' => true]);

            if($guestsToDelete)
            {
                foreach ($guestsToDelete as $guest)
                {
                    $entityManager->remove($guest);
                }

            }

            if($usersToDelete)
            {
                foreach ($usersToDelete as $user)
                {
                    $entityManager->remove($user);
                }

            }

            if(!$guestsToDelete && !$usersToDelete)
            {
                return $this->json([
                    'error' => 'Participants not found'
                ], 404);
            }

            $entityManager->flush();
            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

}
