<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users')]
class UserController extends AbstractController
{
    // API route
    #[Route('/', name: 'app_api_user_get', methods: ['GET'])]
    public function getUsers(SerializerInterface $serializer, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        $json = $serializer->serialize($users, 'json');

        return new Response($json, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/register', name: 'app_api_user_post', methods: ['POST'])]
    public function register(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
    {
        $json = $request->getContent();

        $user = $serializer->deserialize($json, User::class, 'json');

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return new Response($serializer->serialize($errors, 'json'), 400, [
                'Content-Type' => 'application/json'
            ]);
        }

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                json_decode($json)->password
            )
        );
        $entityManager->persist($user);
        $entityManager->flush();

        $json = $serializer->serialize($user, 'json', ['groups' => 'user']);

        return new Response($json, 201, [
            'Content-Type' => 'application/json'
        ]);
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
