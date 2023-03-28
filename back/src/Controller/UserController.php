<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\RequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

#[Route('/users')]
class UserController extends AbstractController
{
    // Get users
    #[Route('/', name: 'app_api_user_get', methods: ['GET'])]
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        try {
            $users = $userRepository->findAll();
            return $this->json($users, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Get medias's users
    #[Route('/medias', name: 'app_api_users_medias_get', methods: ['GET'])]
    public function getUsersMedias(Request $request, UserRepository $userRepository): JsonResponse
    {
        try {
            // Récupérer la limite et l'offset depuis les paramètres de requête
            $limite = $request->query->get('limite', 30);
            $offset = $request->query->get('offset', 0);

            // Récupérer les utilisateurs avec la limite et l'offset
            $users = $userRepository->findBy([], ['id' => 'ASC'], $limite, $offset);

            // Créer un tableau avec les valeurs de profilPicture
            $profilPictures = [];
            foreach ($users as $user) {
                $profilPictures[] = $user->getProfilPicture();
            }

            // Retourner le tableau en JSON
            return $this->json($profilPictures, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Get user's media
    #[Route('/{id}/media', name: 'app_api_users_media_get', methods: ['GET'])]
    public function getUsersMedia(UserRepository $userRepository, int $id, SerializerInterface $serializer): JsonResponse
    {
        try {
            $user = json_decode($this->getUserById($userRepository, $id)->getContent());
            dd($user['profilePicture']);
//            return $this->json($user->, 200);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Get one user
    #[Route('/{id}', name: 'app_api_user_get_one', methods: ['GET'])]
    public function getUserById(UserRepository $userRepository, int $id): JsonResponse
    {
        try {
            $user = $userRepository->find($id);

            if (!$user) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            return $this->json($user, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Log user
    #[Route('/login', name: 'app_api_user_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                return $this->json([
                    'error' => 'No data provided'
                ], 400);
            }

            $requiredFields = ['email', 'password'];

            // Vérification de la présence de tous les champs requis
            foreach ($requiredFields as $field) {
                if (!isset($content[$field])) {
                    return $this->json([
                        'error' => "Missing field '$field'"
                    ], 400);
                }
            }

            // Vérification des types des champs requis
            if (!is_string($content['email']) || !is_string($content['password'])) {
                return $this->json([
                    'error' => 'One or more filled-in field(s) has/have a wrong type'
                ], 400);
            }

            $email = $content['email'];

            // Vérification de l'existence de l'utilisateur
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser === null) {
                return $this->json([
                    'error' => 'User not found'
                ], 400);
            }

            if(!password_verify($content['password'], $existingUser->getPassword())) {
                return $this->json([
                    'error' => 'Bad password'
                ], 400);
            }

            $token = $jwtManager->create($existingUser->getEmail());

            return $this->json($token, 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Create user
    #[Route('/register', name: 'app_api_user_post', methods: ['POST'])]
    public function register(Request $request, SerializerInterface $serializer, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {

        try {
            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                return $this->json([
                    'error' => 'No data provided'
                ], 400);
            }

            $requiredFields = ['email', 'password', 'firstName', 'lastName', 'isPublicProfil', 'activeYears'];

            // Vérification de la présence de tous les champs requis
            foreach ($requiredFields as $field) {
                if (!isset($content[$field])) {
                    return $this->json([
                        'error' => "Missing field '$field'"
                    ], 400);
                }
            }

            // Vérification des types des champs requis
            if (!is_string($content['email']) || !is_string($content['password']) || !is_string($content['firstName']) || !is_string($content['lastName']) || !is_bool($content['isPublicProfil']) || !is_array($content['activeYears'])) {
                return $this->json([
                    'error' => 'One or more filled-in field(s) has/have a wrong type'
                ], 400);
            }

            $email = $content['email'];

            // Vérification de l'existence de l'utilisateur
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser !== null) {
                return $this->json([
                    'error' => 'Email already exists'
                ], 400);
            }

            $user = $serializer->deserialize($request->getContent(), User::class, 'json');

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $content['password']
                )
            );

            //Symfony validation
            $errors = $validator->validate($user);

            if(count($errors) > 0)
            {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json($user, 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }

    }

    // Update user
    #[Route('/{id}', name: 'app_api_user_update', methods: ['PATCH'])]
    public function updateUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        try {
            $user = $entityManager->getRepository(User::class)->find($id);

            // Vérification de l'existence de l'utilisateur
            if ($user === null) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                return $this->json([
                    'error' => 'No data provided'
                ], 400);
            }

            foreach ($content as $key => $value) {
                // Vérification de l'existence de la propriété dans l'objet User
                if (!property_exists(User::class, $key)) {
                    return $this->json([
                        'error' => "Unknown property '$key'"
                    ], 400);
                }

                $setter = 'set' . ucfirst($key);

                // Vérification de l'existence de la méthode setter pour la propriété
                if (!method_exists(User::class, $setter)) {
                    return $this->json([
                        'error' => "No setter found for property '$key'"
                    ], 500);
                }

                // Appel de la méthode setter pour modifier la propriété
                $user->$setter($value);
            }

            $entityManager->flush();

            return $this->json($user, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Delete users
    #[Route('/many', name: 'app_api_user_delete_many', methods: ['DELETE'])]
    public function deleteUsers(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $ids = $data['id'] ?? [];
    
            if (empty($ids)) {
                return $this->json([
                    'error' => 'No IDs provided'
                ], 400);
            }
    
            $users = $entityManager->getRepository(User::class)->findBy([
                'id' => $ids
            ]);
    
            if (empty($users)) {
                return $this->json([
                    'error' => 'Users not found'
                ], 404);
            }
    
            foreach ($users as $user) {
                $entityManager->remove($user);
            }
    
            $entityManager->flush();
    
            return $this->json([], 204);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }  

    // Clear users
    #[Route('/clear', name: 'app_api_user_delete_all', methods: ['DELETE'])]
    public function clearUsers(EntityManagerInterface $entityManager): Response
    {
        try {
            $userRepository = $entityManager->getRepository(User::class);
            $users = $userRepository->findAll();

            foreach ($users as $user) {
                $entityManager->remove($user);
            }

            $entityManager->flush();

            return $this->json([], 204);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Delete user
    #[Route('/{id}', name: 'app_api_user_delete', methods: ['DELETE'])]
    public function deleteUser(User $user = null, EntityManagerInterface $entityManager): Response
    {
        try {
            if (!$user) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            $entityManager->remove($user);
            $entityManager->flush();

            return $this->json([], 204);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }    
}
