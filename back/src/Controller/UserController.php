<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\RequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/users')]
class UserController extends AbstractController
{
    // API route
    #[Route('/', name: 'app_api_user_get', methods: ['GET'])]
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        return $this->json([
            'Content-Type' => $users
        ], 200);
    }

    #[Route('/register', name: 'app_api_user_post', methods: ['POST'])]
    public function register(Request $request, RequestService $requestService): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        /* Try to access essentials keys */
        try {
            $email = $content['email'];
            $password = $content['password'];
            $firstName = $content['firstName'];
            $lastName = $content['lastName'];
            $isPublicProfil = $content['isPublicProfil'];
            $activeYears = $content['activeYears'];
        } catch (\Exception $e) {
            return $this->json([
                'error_message' => 'Missing essential key'
            ], 400);
        }

        /* Check if essentials keys are not null ("" values) */
        if (empty($email) || empty($password) || empty($firstName) || empty($lastName) || ($isPublicProfil === null) || empty($activeYears)) {
            return $this->json([
                'error_message' => 'A required field is missing'
            ], 400);
        } elseif (!is_string($email) || !is_string($password) || !is_string($firstName) || !is_string($lastName) || !is_bool($isPublicProfil) || !is_array($activeYears)) {
            return $this->json([
                'error_message' => 'One or more filled-in field(s) has/have a wrong type'
            ], 400);
        }



        return $this->json([
            'Content-Type' => 'ok'
        ], 200);

//        $postalCode = $request->query->get('postalCode');
//
//        $json = $request->getContent();
//
//        $user = $serializer->deserialize($json, User::class, 'json');
//
//        $errors = $validator->validate($user);
//
//        if (count($errors) > 0) {
//            return new Response($serializer->serialize($errors, 'json'), 400, [
//                'Content-Type' => 'application/json'
//            ]);
//        }
//
//        $user->setPassword(
//            $userPasswordHasher->hashPassword(
//                $user,
//                json_decode($json)->password
//            )
//        );
//        $entityManager->persist($user);
//        $entityManager->flush();
//
//        $json = $serializer->serialize($user, 'json', ['groups' => 'user']);
//
    }

    #[Route('/{id}', name: 'app_api_user_patch', methods: ['PATCH'])]
    public function updateUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, User $user, EntityManagerInterface $entityManager): Response
    {
        $json = $request->getContent();

        $user = $serializer->deserialize($json, User::class, 'json', ['object_to_populate' => $user]);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return new Response($serializer->serialize($errors, 'json'), 400, [
                'Content-Type' => 'application/json'
            ]);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $json = $serializer->serialize($user, 'json', ['groups' => 'user']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/{id}', name: 'app_api_user_delete', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return new Response(null, 204);
    }
}
