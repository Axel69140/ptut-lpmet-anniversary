<?php

namespace App\Controller;

use App\Repository\AnecdoteRepository;
use App\Repository\UserRepository;
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

#[Route('/anecdotes')]
class AnecdoteController extends AbstractController
{
    // Get anecdotes
    #[Route('/', name: 'app_api_anecdote_get', methods: ['GET'])]
    public function getAnecdotes(AnecdoteRepository $anecdoteRepository): JsonResponse
    {
        try {
            $anecdotes = $anecdoteRepository->findAll();
            return $this->json($anecdotes, 200);
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
            $anecdote = $anecdoteRepository->find($id);

            if (!$anecdote) {
                return $this->json([
                    'error' => 'Anecdote not found'
                ], 404);
            }

            return $this->json($anecdote, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }    

    // Create anecdote
    #[Route('/create', name: 'app_api_anecdote_post', methods: ['POST'])]
    public function createAnecdote(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true);
        
            if (empty($content)) {
                return $this->json([
                    'error' => 'No data provided'
                ], 400);
            }
        
            $requiredFields = ['content','id_user'];
        
            // Vérification de la présence de tous les champs requis
            foreach ($requiredFields as $field) {
                if (!isset($content[$field])) {
                    return $this->json([
                        'error' => "Missing field '$field'"
                    ], 400);
                }
            }
        
            // Vérification des types des champs requis
            if (!is_string($content['content']) || !is_numeric($content['id_user'])) {
                return $this->json([
                    'error' => 'One or more filled-in field(s) has/have a wrong type'
                ], 400);
            }
        
            $user = $userRepository->find($content['id_user']);
            if (!$user) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }
            
            $context = (new ObjectNormalizerContextBuilder())
            ->withGroups('user')
            ->toArray();
            
            $anecdote = $serializer->deserialize($request->getContent(), Anecdote::class, 'json', $content);
            
            $anecdote->setUser($user);
            $anecdote->setIsValidate(false);

            $entityManager->persist($anecdote);
            $entityManager->flush();
        
            return $this->json($anecdote, 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


}