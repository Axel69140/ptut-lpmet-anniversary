<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ActivityRepository;
use App\Service\RequestService;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Activity;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivityController extends AbstractController
{
    // Get activities
    #[Route('/', name: 'app_api_activity_get', methods: ['GET'])]
    public function getActivities(ActivityRepository $activityRepository): JsonResponse
    {
        try {
            $activities = $activityRepository->findAll();
            return $this->json($activities, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Get one activities
    #[Route('/{id}', name: 'app_api_activity_get_one', methods: ['GET'])]
    public function getActivityById(ActivityRepository $activityRepository, int $id): JsonResponse
    {
        try {
            $activity = $activityRepository->find($id);

            if (!$activity) {
                return $this->json([
                    'error' => 'Activity not found'
                ], 404);
            }

            return $this->json($activity, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Create activity
    #[Route('/create', name: 'app_api_activity_post', methods: ['POST'])]
    public function createActivity(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                return $this->json([
                    'error' => 'No data provided'
                ], 400);
            }

            $requiredFields = ['name', 'description', 'startHour', 'duration', 'id_user'];

            // Vérification de la présence de tous les champs requis
            foreach ($requiredFields as $field) {
                if (!isset($content[$field])) {
                    return $this->json([
                        'error' => "Missing field '$field'"
                    ], 400);
                }
            }

            // Vérification des types des champs requis
            if (!is_string($content['name']) || !is_string($content['description']) || !is_string($content['startHour']) || !is_string($content['duration']) || !is_numeric($content['id_user'])) {
                return $this->json([
                    'error' => 'One or more filled-in field(s) has/have a wrong type'
                ], 400);
            }

            $activity = $serializer->deserialize($request->getContent(), Activity::class, 'json');

            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->json($activity, 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Update activity
    #[Route('/{id}', name: 'app_api_activity_update', methods: ['PATCH'])]
    public function updateActivity(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        try {
            $activity = $entityManager->getRepository(Activity::class)->find($id);

            // Vérification de l'existence de l'activity
            if ($activity === null) {
                return $this->json([
                    'error' => 'Activity not found'
                ], 404);
            }

            $content = json_decode($request->getContent(), true);

            if (empty($content)) {
                return $this->json([
                    'error' => 'No data provided'
                ], 400);
            }

            foreach ($content as $key => $value) {
                // Vérification de l'existence de la propriété dans l'objet Activity
                if (!property_exists(Activity::class, $key)) {
                    return $this->json([
                        'error' => "Unknown property '$key'"
                    ], 400);
                }

                $setter = 'set' . ucfirst($key);

                // Vérification de l'existence de la méthode setter pour la propriété
                if (!method_exists(Activity::class, $setter)) {
                    return $this->json([
                        'error' => "No setter found for property '$key'"
                    ], 500);
                }

                // Appel de la méthode setter pour modifier la propriété
                $activity->$setter($value);
            }

            $entityManager->flush();

            return $this->json($activity, 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Delete activities
    #[Route('/many', name: 'app_api_activity_delete_many', methods: ['DELETE'])]
    public function deleteActivities(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $ids = $data['id'] ?? [];
    
            if (empty($ids)) {
                return $this->json([
                    'error' => 'No IDs provided'
                ], 400);
            }
    
            $activities = $entityManager->getRepository(Activity::class)->findBy([
                'id' => $ids
            ]);
    
            if (empty($activities)) {
                return $this->json([
                    'error' => 'Users not found'
                ], 404);
            }
    
            foreach ($activities as $activity) {
                $entityManager->remove($activity);
            }
    
            $entityManager->flush();
    
            return $this->json([], 204);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }  

    // Clear activities
    #[Route('/clear', name: 'app_api_activity_delete_all', methods: ['DELETE'])]
    public function clearActivities(EntityManagerInterface $entityManager): Response
    {
        try {
            $activityRepository = $entityManager->getRepository(Activity::class);
            $activities = $activityRepository->findAll();

            foreach ($activities as $activity) {
                $entityManager->remove($activity);
            }

            $entityManager->flush();

            return $this->json([], 204);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // Delete activity
    #[Route('/{id}', name: 'app_api_activity_delete', methods: ['DELETE'])]
    public function deleteActivity(Activity $activity = null, EntityManagerInterface $entityManager): Response
    {
        try {
            if (!$activity) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            $entityManager->remove($activity);
            $entityManager->flush();

            return $this->json([], 204);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Server error'
            ], 500);
        }
    }

    // #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    //    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    //    {
    //        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
    //            if($user->getPicture() !== null){
    //                unlink($user->getPicture()->getPath());
    //            }
    //
    //            $userRepository->remove($user, true);
    //        }
    //
    //        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    //    }
    //
    //    #[Route('/api/user', name: 'app_api_user_get', methods: ['GET'])]
    //    public function getUsers(SerializerInterface $serializer, UserRepository $userRepository): Response
    //    {
    //        $users = $userRepository->findAll();
    //
    //        $json = $serializer->serialize($users, 'json');
    //
    //        return new Response($json, 200, [
    //            'Content-Type' => 'application/json'
    //        ]);
    //    }
    //
    //    #[Route('/api/user', name: 'app_api_user_post', methods: ['POST'])]
    //    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
    //    {
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
    //        $entityManager->persist($user);
    //        $entityManager->flush();
    //
    //        $json = $serializer->serialize($user, 'json', ['groups' => 'user']);
    //
    //        return new Response($json, 201, [
    //            'Content-Type' => 'application/json'
    //        ]);
    //    }
    //
    //    #[Route('/api/user/{id}', name: 'app_api_user_patch', methods: ['PATCH'])]
    //    public function update(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, User $user, EntityManagerInterface $entityManager): Response
    //    {
    //        $json = $request->getContent();
    //
    //        $user = $serializer->deserialize($json, User::class, 'json', ['object_to_populate' => $user]);
    //
    //        $errors = $validator->validate($user);
    //
    //        if (count($errors) > 0) {
    //            return new Response($serializer->serialize($errors, 'json'), 400, [
    //                'Content-Type' => 'application/json'
    //            ]);
    //        }
    //
    //        $entityManager->persist($user);
    //        $entityManager->flush();
    //
    //        $json = $serializer->serialize($user, 'json', ['groups' => 'user']);
    //
    //        return new Response($json, 200, [
    //            'Content-Type' => 'application/json'
    //        ]);
    //    }
    //
    //    #[Route('/api/user/{id}', name: 'app_api_user_delete', methods: ['DELETE'])]
    //    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    //    {
    //        $entityManager->remove($user);
    //        $entityManager->flush();
    //
    //        return new Response(null, 204);
    //    }
}
