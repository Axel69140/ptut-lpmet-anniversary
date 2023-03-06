<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Kernel;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, MediaRepository $mediaRepository, FileUploader $fileUploader, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            if($request->files->all()[$form->getName()]['picture'] !== null){
                $media = $fileUploader->toMedia($request->files->all()[$form->getName()]['picture'], $this->getParameter('upload_user_img'));
                $mediaRepository->save($media);
                $user->setPicture($media);
            };

            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, MediaRepository $mediaRepository, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($request->files->all()[$form->getName()]['picture'] !== null){
                $media = $fileUploader->toMedia($request->files->all()[$form->getName()]['picture'], $this->getParameter('upload_user_img'));
                $mediaRepository->save($media);
                if($user->getPicture() !== null)
                {
                    $pictureToDelete = $user->getPicture();
                    unlink($pictureToDelete->getPath());
                    $user->setPicture($media);
                    $mediaRepository->remove($pictureToDelete, true);
                } else {
                    $user->setPicture($media);
                }
            }

            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if($user->getPicture() !== null){
                unlink($user->getPicture()->getPath());
            }

            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/user', name: 'app_api_user_get', methods: ['GET'])]
    public function getUsers(SerializerInterface $serializer, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        $json = $serializer->serialize($users, 'json');

        return new Response($json, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/api/register', name: 'app_api_user_post', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
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

    #[Route('/api/user/{id}', name: 'app_api_user_patch', methods: ['PATCH'])]
    public function update(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, User $user, EntityManagerInterface $entityManager): Response
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

    #[Route('/api/user/{id}', name: 'app_api_user_delete', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return new Response(null, 204);
    }
}
