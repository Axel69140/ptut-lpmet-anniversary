<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\EntryDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/activities')]
class ActivityController extends AbstractController
{
    // Get activities
    #[Route('/', name: 'app_api_activity_get', methods: ['GET'])]
    public function getActivities(ActivityRepository $activityRepository): JsonResponse
    {
        try {

            $anecdotes = $activityRepository->findAll();

            return $this->json($anecdotes, 200, [], ['groups' => ['activity-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get one activity
    #[Route('/{id}', name: 'app_api_activity_get_one', methods: ['GET'])]
    public function getActivityById(ActivityRepository $activityRepository, int $id): JsonResponse
    {
        try {

            $activity = $activityRepository->findOneBy(['id' => $id]);

            if (!$activity) {
                return $this->json([
                    'error' => 'Anecdote not found'
                ], 404);
            }

            return $this->json($activity, 200, [], ['groups' => ['activity-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Create activity
    #[Route('/create', name: 'app_api_activity_post', methods: ['POST'])]
    public function createActivity(Request $request, EntityManagerInterface $em, ActivityRepository $activityRepository, EntryDataService $entryDataService, ValidatorInterface $validator): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $activity = new Activity();
            $activity = $entryDataService->defineKeysInEntity($content, $activity, $em);
            if ($activity === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity creation'
                ], 400);
            }

            //Symfony validation
            $errors = $validator->validate($activity);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $activityRepository->save($activity, true);
            return $this->json($activity, 201, [], ['groups' => ['activity-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage()
            ], 500);

        }
    }

    // Update activity
    #[Route('/{id}', name: 'app_api_activity_update', methods: ['PATCH'])]
    public function updateActivity(Request $request, ActivityRepository $activityRepository, EntityManagerInterface $em, EntryDataService $entryDataService, int $id, ValidatorInterface $validator): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $activityToUpdate = $activityRepository->findOneBy(['id' => $id]);
            if (!$activityToUpdate) {
                return $this->json([
                    'error' => 'Activity not found'
                ], 404);
            }

            $activityToUpdate = $entryDataService->defineKeysInEntity($content, $activityToUpdate, $em);
            if ($activityToUpdate === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity modification'
                ], 400);
            }

            //Symfony validation
            $errors = $validator->validate($activityToUpdate);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $activityRepository->save($activityToUpdate, true);
            return $this->json($activityToUpdate, 200, [], ['groups' => ['activity-return']]);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Delete activities
    #[Route('/many', name: 'app_api_activity_delete_many', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function deleteActivities(Request $request, EntityManagerInterface $entityManager, ActivityRepository $activityRepository): Response
    {
        try {

            $data = json_decode($request->getContent(), true);
            $ids = $data['id'] ?? [];

            if (empty($ids)) {
                return $this->json([
                    'error' => 'No IDs provided'
                ], 400);
            }

            $anecdotes = $activityRepository->findBy(['id' => $ids]);

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

    // Clear activities
    #[Route('/clear', name: 'app_api_activity_delete_all', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function clearActivities(ActivityRepository $activityRepository): Response
    {
        try {

            $anecdotes = $activityRepository->findAll();

            foreach ($anecdotes as $anecdote) {
                $activityRepository->remove($anecdote, true);
            }

            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Delete activity
    #[Route('/{id}', name: 'app_api_activity_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function deleteActivity(int $id, ActivityRepository $activityRepository): Response
    {
        try {

            $anecdoteToDelete = $activityRepository->findOneBy(['id' => $id]);

            if (!$anecdoteToDelete) {
                return $this->json([
                    'error' => 'Anecdote not found'
                ], 404);
            }

            $activityRepository->remove($anecdoteToDelete, true);
            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }
}
