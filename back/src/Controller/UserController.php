<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Repository\GuestRepository;
use App\Repository\SettingsRepository;
use App\Repository\UserRepository;
use App\Service\EntryDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/users')]
class UserController extends AbstractController
{
    // Get users
    #[Route('/', name: 'app_api_user_get', methods: ['GET'])]
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        try {

            $users = $userRepository->findAll();

            if (!$users) {
                return $this->json([
                    'error' => 'Users not found'
                ], 404);
            }

            return $this->json($users, 200, [], ['groups' => ['user-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get medias's users
//    #[Route('/medias', name: 'app_api_users_medias_get', methods: ['GET'])]
//    public function getUsersMedias(Request $request, UserRepository $userRepository): JsonResponse
//    {
//
//        try {
//            // Récupérer la limite et l'offset depuis les paramètres de requête
//            $limite = $request->query->get('limite', 30);
//            $offset = $request->query->get('offset', 0);
//
//            // Récupérer les utilisateurs avec la limite et l'offset
//            $users = $userRepository->findBy([], ['id' => 'ASC'], $limite, $offset);
//
//            // Créer un tableau avec les valeurs de profilPicture
//            $profilPictures = [];
//            foreach ($users as $user) {
//                $profilPictures[] = $user->getProfilPicture();
//            }
//
//            // Retourner le tableau en JSON
//            return $this->json($profilPictures, 200);
//        } catch (\Exception $e) {
//            return $this->json([
//                'error' => 'Server error'
//            ], 500);
//        }
//
//    }

    // Get user's media
    #[Route('/{id}/media', name: 'app_api_users_media_get', methods: ['GET'])]
    public function getUsersMedia(int $id, UserRepository $userRepository): JsonResponse
    {
        try {

            $user = $userRepository->findOneBy(['id' => $id]);

            if (!$user) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            return $this->json($user->getProfilPicture(), 200, [], ['groups' => ['user-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get user's guests
    #[Route('/{id}/guests', name: 'app_api_users_guests_get', methods: ['GET'])]
    public function getUsersGuests(int $id, UserRepository $userRepository): JsonResponse
    {
        try {

            $user = $userRepository->findOneBy(['id' => $id]);

            if (!$user) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            return $this->json($user->getGuests(), 200, [], ['groups' => ['user-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get all functions
    #[Route('/functions', name: 'app_api_get_functions', methods: ['GET'])]
    public function getFunctions(SettingsRepository $settingsRepository): JsonResponse
    {
        try {

            $settings = $settingsRepository->findAll()[0];
            return $this->json($settings->getAllowedFunctions(), 200);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get one user
    #[Route('/{id}', name: 'app_api_user_get_one', methods: ['GET'])]
    public function getUserById(int $id, UserRepository $userRepository): JsonResponse
    {
        try {

            $user = $userRepository->findOneBy(['id' => $id]);

            if (!$user) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            return $this->json($user, 200, [], ['groups' => ['user-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get user by email
    #[Route('/{email}', name: 'app_api_user_get_one_by_email', methods: ['GET'])]
    public function getUserByEmail(string $email, UserRepository $userRepository): JsonResponse
    {
        try {

            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            return $this->json($user, 200, [], ['groups' => ['user-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get role's user
    #[Route('/{id}/role', name: 'app_api_users_role_get', methods: ['GET'])]
    public function getUserRole(int $id, UserRepository $userRepository): JsonResponse
    {
        try {

            $user = $userRepository->findOneBy(['id' => $id]);
            if (!$user) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            return $this->json(["role" => $user->getRoles()], 200, [], ['groups' => ['user-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Log user
    #[Route('/login', name: 'app_api_user_login', methods: ['POST'])]
    public function login(Request $request, JWTTokenManagerInterface $jwtManager, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);

            // Vérification de l'existence de la propriété dans l'objet User
            foreach ($content as $key => $value) {
                if (!property_exists(User::class, $key)) {
                    return $this->json([
                        'error' => 'Unknown property'
                    ], 400);
                }
            }

            // Check if user exists & if password is correct
            $user = $userRepository->findOneBy(['email' => $content['email']]);
            if (!$user || !$passwordHasher->isPasswordValid($user, $content['password'])) {
                return $this->json([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            $token = $jwtManager->create($user);
            return $this->json(["id" => $user->getId(), "email" => $user->getEmail(), "firstName" => $user->getFirstName(), "lastName" => $user->getLastName(), "token" => $token ], 201, [], ['groups' => ['user-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Create user
    #[Route('/register', name: 'app_api_user_post', methods: ['POST'])]
    public function register(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, ValidatorInterface $validator, EntryDataService $entryDataService, GuestRepository $guestRepository, EntityManagerInterface $em): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $user = new User();
            $user = $entryDataService->defineKeysInEntity($content, $user, $em);
            if ($user === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity creation'
                ], 400);
            }

            // Check if mail is available
            $existingEntities = $entryDataService->getEntityUsingMail($user->getEmail(), [$userRepository, $guestRepository]);
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

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $content['password']
                )
            );

            //Symfony validation
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }
            $userRepository->save($user, true);
            return $this->json($user, 201, [], ['groups' => ['user-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage()
            ], 500);

        }
    }

    // Update user
    #[Route('/{id}', name: 'app_api_user_update', methods: ['PATCH'])]
    public function updateUser(int $id, Request $request, UserRepository $userRepository, EntryDataService $entryDataService, ValidatorInterface $validator, GuestRepository $guestRepository, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $userToUpdate = $userRepository->findOneBy(['id' => $id]);
            if (!$userToUpdate) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            $userToUpdate = $entryDataService->defineKeysInEntity($content, $userToUpdate, $em);
            if ($userToUpdate === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity modification'
                ], 400);
            }

            // Check if mail is available
            $existingEntities = $entryDataService->getEntityUsingMail($userToUpdate->getEmail(), [$userRepository, $guestRepository]);
            if (is_null($existingEntities)) {

                return $this->json([
                    'error' => 'No repository provided'
                ], 403);

            } else if(count($existingEntities) > 1) {

                return $this->json([
                    'error' => 'Email already used'
                ], 403);

            } else if((count($existingEntities) === 1) && (get_class($userToUpdate) !== get_class($existingEntities[0]))) {

                return $this->json([
                    'error' => 'Email already used'
                ], 403);

            } else if ((count($existingEntities) === 1) && (get_class($userToUpdate) === get_class($existingEntities[0])) && ($existingEntities[0]->getId() !== $userToUpdate->getId())) {

                return $this->json([
                    'error' => 'Email already used'
                ], 403);

            }

            // Password check
            if(array_key_exists('password', $content) && !$passwordHasher->isPasswordValid($userToUpdate, $content['password']))
            {
                $userToUpdate->setPassword(
                    $passwordHasher->hashPassword(
                        $userToUpdate,
                        $content['password']
                    )
                );
            }

            //Symfony validation
            $errors = $validator->validate($userToUpdate);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $userRepository->save($userToUpdate, true);
            return $this->json($userToUpdate, 200, [], ['groups' => ['user-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage()
            ], 500);

        }
    }

    // Delete users
    #[Route('/many', name: 'app_api_user_delete_many', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function deleteUsers(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        try {

            $data = json_decode($request->getContent(), true);
            $ids = $data['id'] ?? [];

            if (empty($ids)) {
                return $this->json([
                    'error' => 'No IDs provided'
                ], 400);
            }

            $users = $userRepository->findBy(['id' => $ids]);

            if (empty($users) || count($ids) !== count($users)) {
                return $this->json([
                    'error' => 'One or more user(s) are not found'
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
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function clearUsers(UserRepository $userRepository): JsonResponse
    {
        try {

            $users = $userRepository->findAll();

            foreach ($users as $user) {
                $userRepository->remove($user, true);
            }

            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Delete user
    #[Route('/{id}', name: 'app_api_user_delete', methods: ['DELETE'])]
    public function deleteUser(int $id, UserRepository $userRepository): JsonResponse
    {
        try {

            $userToDelete = $userRepository->findOneBy(['id' => $id]);

            if (!$userToDelete) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            $userRepository->remove($userToDelete, true);
            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }
}
