<?php

namespace App\Controller;

use App\Repository\AnecdoteRepository;
use App\Repository\UserRepository;
use App\Service\EntryDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Anecdote;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\AnecdotePasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/anecdotes')]
class AnecdoteController extends AbstractController
{
    // Get anecdotes
    #[Route('/', name: 'app_api_anecdotes_get', methods: ['GET'])]
    public function getAnecdotes(AnecdoteRepository $anecdoteRepository): JsonResponse
    {
        try {

            $anecdotes = $anecdoteRepository->findAll();

            return $this->json($anecdotes, 200, [], ['groups' => ['anecdote-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get one anecdote
    #[Route('/{id}', name: 'app_api_anecdote_get_one', methods: ['GET'])]
    public function getAnecdoteById(AnecdoteRepository $anecdoteRepository, int $id): JsonResponse
    {
        try {

            $anecdotes = $anecdoteRepository->findOneBy(['id' => $id]);

            if (!$anecdotes) {
                return $this->json([
                    'error' => 'Anecdote not found'
                ], 404);
            }

            return $this->json($anecdotes, 200, [], ['groups' => ['anecdote-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }    

    // Create anecdote
    #[Route('/create', name: 'app_api_anecdote_post', methods: ['POST'])]
    public function createAnecdote(Request $request, EntityManagerInterface $em, UserRepository $userRepository, EntryDataService $entryDataService, ValidatorInterface $validator, AnecdoteRepository $anecdoteRepository): JsonResponse
    {
        try {
            
            $content = json_decode($request->getContent(), true);
            $anecdote = new Anecdote();
            $anecdote = $entryDataService->defineKeysInEntity($content, $anecdote, $em);
            if ($anecdote === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity creation'
                ], 400);
            }

            //Symfony validation
            $errors = $validator->validate($anecdote);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $anecdoteRepository->save($anecdote, true);
            return $this->json($anecdote, 201, [], ['groups' => ['anecdote-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage()
            ], 500);

        }
    }

    // Update anecdote
    #[Route('/{id}', name: 'app_api_anecdote_update', methods: ['PATCH'])]
    public function updateAnecdote(int $id, Request $request, AnecdoteRepository $anecdoteRepository, EntryDataService $entryDataService, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $anecdoteToUpdate = $anecdoteRepository->findOneBy(['id' => $id]);
            if (!$anecdoteToUpdate) {
                return $this->json([
                    'error' => 'Anecdote not found'
                ], 404);
            }

            $anecdoteToUpdate = $entryDataService->defineKeysInEntity($content, $anecdoteToUpdate, $em);
            if ($anecdoteToUpdate === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity modification'
                ], 400);
            }

            //Symfony validation
            $errors = $validator->validate($anecdoteToUpdate);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $anecdoteRepository->save($anecdoteToUpdate, true);
            return $this->json($anecdoteToUpdate, 200, [], ['groups' => ['anecdote-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Delete anecdotes
    #[Route('/many', name: 'app_api_anecdotes_delete_many', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function deleteAnecdotes(Request $request, AnecdoteRepository $anecdoteRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        try {

            $data = json_decode($request->getContent(), true);
            $ids = $data['id'] ?? [];

            if (empty($ids)) {
                return $this->json([
                    'error' => 'No IDs provided'
                ], 400);
            }

            $anecdotes = $anecdoteRepository->findBy(['id' => $ids]);

            if (empty($anecdotes) || count($ids) !== count($anecdotes)) {
                return $this->json([
                    'error' => 'One or more anecdote(s) are not found'
                ], 404);
            }

            foreach ($anecdotes as $anecdote) {
                $entityManager->remove($anecdote);
            }

            $entityManager->flush();

            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Clear anecdotes
    #[Route('/clear', name: 'app_api_anecdote_delete_all', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function clearAnecdotes(AnecdoteRepository $anecdoteRepository): JsonResponse
    {
        try {

            $anecdotes = $anecdoteRepository->findAll();

            foreach ($anecdotes as $anecdote) {
                $anecdoteRepository->remove($anecdote, true);
            }

            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Delete anecdote
    #[Route('/{id}', name: 'app_api_anecdote_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function deleteAnecdote(int $id, AnecdoteRepository $anecdoteRepository): JsonResponse
    {
        try {

            $anecdoteToDelete = $anecdoteRepository->findOneBy(['id' => $id]);

            if (!$anecdoteToDelete) {
                return $this->json([
                    'error' => 'Anecdote not found'
                ], 404);
            }

            $anecdoteRepository->remove($anecdoteToDelete, true);
            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

}