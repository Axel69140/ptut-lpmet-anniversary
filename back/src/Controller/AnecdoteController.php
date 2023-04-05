<?php

namespace App\Controller;

use App\Repository\AnecdoteRepository;
use App\Repository\UserRepository;
use App\Service\EntryDataService;
use App\Service\RequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Entity\Anecdote;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\AnecdotePasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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

            if (!$anecdotes) {
                return $this->json([
                    'error' => 'Anecdotes not found'
                ], 404);
            }

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
                    'error' => 'Article not found'
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
    public function createAnecdote(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserRepository $userRepository, EntryDataService $entryDataService, ValidatorInterface $validator, AnecdoteRepository $anecdoteRepository): JsonResponse
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


}