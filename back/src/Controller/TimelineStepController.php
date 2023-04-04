<?php

namespace App\Controller;

use App\Service\EntryDataService;
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

            if (!$timelineSteps) {
                return $this->json([
                    'error' => 'Timeline not found'
                ], 404);
            }
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
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, EntryDataService $entryDataService, EntityManagerInterface $em): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $timelineStep = new TimelineStep();
            $timelineStep = $entryDataService->defineKeysInEntity($content, $timelineStep, $em);
            // Vérification de la présence de tous les champs requis
            foreach ($requiredFields as $field) {
                if (!isset($content[$field])) {
                    return $this->json([
                        'error' => "Missing field '$field'"
                    ], 400);
                }
            }

            // Vérification des types des champs requis
            if (!is_string($content['title']) || !is_string($content['content'])) {
                return $this->json([
                    'error' => 'One or more filled-in field(s) has/have a wrong type'
                ], 400);
            }
            
            $timelineStep = new TimelineStep;
            $timelineStep->setDate(\DateTime::createFromFormat('Y-m-d', $content['date']));
            $timelineStep->setTitle($content['title']);
            $timelineStep->setContent($content['content']);
            $timelineStep->setIsValidate(true);

            //Symfony validation
            $errors = $validator->validate($timelineStep);

            if(count($errors) > 0)
            {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $entityManager->persist($timelineStep);
            $entityManager->flush();

            return $this->json($timelineStep, 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
