<?php

namespace App\Controller;

use App\Service\EntryDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TimelineStepRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Service\RequestService;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\TimelineStep;

#[Route('/timelinesteps')]
class TimelineStepController extends AbstractController
{
    // Get timelineSteps
    #[Route('/', name: 'app_api_timeline_get', methods: ['GET'])]
    public function getTimelineSteps(TimelineStepRepository $timelineStepRepository): JsonResponse
    {
        try {

            $timelineSteps = $timelineStepRepository->findAll();
            
            return $this->json($timelineSteps, 200);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Get one timelineStep
    #[Route('/{id}', name: 'app_api_timeline_get_one', methods: ['GET'])]
    public function getTimelineStepById(int $id, TimelineStepRepository $timelineStepRepository): JsonResponse
    {
        try {

            $timelineStep = $timelineStepRepository->find($id);

            if (!$timelineStep) {
                return $this->json([
                    'error' => 'Timeline step not found'
                ], 404);
            }

            return $this->json($timelineStep, 200);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Create timelineStep
    #[Route('/create', name: 'app_api_timeline_post', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, EntryDataService $entryDataService, EntityManagerInterface $em, TimelineStepRepository $timelineStepRepository): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $timelineStep = new TimelineStep();
            $timelineStep = $entryDataService->defineKeysInEntity($content, $timelineStep, $em);
            if ($timelineStep === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity creation'
                ], 400);
            }

            //Symfony validation
            $errors = $validator->validate($timelineStep);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $timelineStepRepository->save($timelineStep, true);
            return $this->json($timelineStep, 201);

        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage()
            ], 500);

        }
    }

    // Update many timeline steps
    #[Route('/update-many', name: 'app_api_timeline_update_many', methods: ['PATCH'])]
    public function updateMany(Request $request, TimelineStepRepository $timelineStepRepository, EntryDataService $entryDataService, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $ids = $content['ids'];
            if(is_array($ids) && ($ids !== []))
            {
                $filteredArray = array_filter($ids, 'is_int');
                if(count($ids) !== count($filteredArray))
                {
                    return $this->json([
                        'error' => 'IDs need to be int'
                    ], 400);
                }
                unset($content['ids']);
            } else {
                return $this->json([
                    'error' => 'No IDs provided'
                ], 400);
            }

            foreach ($ids as $id)
            {
                $timeLineStepToUpdate = $timelineStepRepository->findOneBy(['id' => $id]);
                if (!$timeLineStepToUpdate) {
                    return $this->json([
                        'error' => 'Timeline step not found'
                    ], 404);
                }

                $timeLineStepToUpdate = $entryDataService->defineKeysInEntity($content, $timeLineStepToUpdate, $em);
                if ($timeLineStepToUpdate === null) {
                    return $this->json([
                        'error' => 'A problem has been encounter during entity modification'
                    ], 400);
                }

                //Symfony validation
                $errors = $validator->validate($timeLineStepToUpdate);
                if (count($errors) > 0) {
                    return $this->json([
                        'error' => $errors
                    ], 400);
                }

                $em->persist($timeLineStepToUpdate);
            }

            $em->flush();
            return $this->json([], 200);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Update timeline step
    #[Route('/{id}', name: 'app_api_timeline_update', methods: ['PATCH'])]
    public function update(int $id, Request $request, TimelineStepRepository $timelineStepRepository, EntryDataService $entryDataService, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $timeLineStepToUpdate = $timelineStepRepository->findOneBy(['id' => $id]);
            if (!$timeLineStepToUpdate) {
                return $this->json([
                    'error' => 'Timeline step not found'
                ], 404);
            }

            $timeLineStepToUpdate = $entryDataService->defineKeysInEntity($content, $timeLineStepToUpdate, $em);
            if ($timeLineStepToUpdate === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity modification'
                ], 400);
            }

            //Symfony validation
            $errors = $validator->validate($timeLineStepToUpdate);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $timelineStepRepository->save($timeLineStepToUpdate, true);
            return $this->json($timeLineStepToUpdate, 200);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Delete timeline steps
    #[Route('/many', name: 'app_api_timeline_delete_many', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function deleteTimelineSteps(Request $request, TimelineStepRepository $timelineStepRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        try {

            $data = json_decode($request->getContent(), true);
            $ids = $data['id'] ?? [];

            if (empty($ids)) {
                return $this->json([
                    'error' => 'No IDs provided'
                ], 400);
            }

            $timelineSteps = $timelineStepRepository->findBy(['id' => $ids]);

            if (empty($timelineSteps) || count($ids) !== count($timelineSteps)) {
                return $this->json([
                    'error' => 'One or more timeline step(s) are not found'
                ], 404);
            }

            foreach ($timelineSteps as $timelineStep) {
                $entityManager->remove($timelineStep);
            }

            $entityManager->flush();

            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Clear timeline steps
    #[Route('/clear', name: 'app_api_timeline_delete_all', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function clearTimelineSteps(TimelineStepRepository $timelineStepRepository): JsonResponse
    {
        try {

            $timelineSteps = $timelineStepRepository->findAll();

            foreach ($timelineSteps as $timelineStep) {
                $timelineStepRepository->remove($timelineStep, true);
            }

            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Delete timeline step
    #[Route('/{id}', name: 'app_api_timeline_delete', methods: ['DELETE'])]
    public function delete(int $id, TimelineStepRepository $timelineStepRepository): JsonResponse
    {
        try {

            $timelineStepToDelete = $timelineStepRepository->findOneBy(['id' => $id]);
            if (!$timelineStepToDelete) {
                return $this->json([
                    'error' => 'User not found'
                ], 404);
            }

            $timelineStepRepository->remove($timelineStepToDelete, true);
            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }
}
