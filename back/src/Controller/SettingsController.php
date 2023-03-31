<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Service\EntryDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/settings')]
class SettingsController extends AbstractController
{

    // Get settings
    #[Route('/', name: 'app_settings', methods: ['GET'])]
    public function getSettings(SettingsRepository $settingsRepository): JsonResponse
    {
        try {

            $settings = $settingsRepository->findAll();

            if(!$settings)
            {
                $this->resetSettings($settingsRepository);
                $settings = $settingsRepository->findAll();
            }

            return $this->json($settings, 200);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

    // Reset settings
    #[Route('/reset', name: 'app_reset_settings', methods: ['PATCH'])]
    public function resetSettings(SettingsRepository $settingsRepository): JsonResponse
    {
        try {

            $settings = $settingsRepository->findAll();

            if(!$settings)
            {
                $defaultSettings = new Settings();
                $defaultSettings->setAllowedFunctions(["", "Enseignant", "Eleve", "Autre"])
                    ->setMaxNumberGuests(4);
            } else {
                $defaultSettings = $settings[0]->setAllowedFunctions(["", "Enseignant", "Eleve", "Autre"])
                    ->setMaxNumberGuests(4);
            }

            $settingsRepository->save($defaultSettings, true);
            return $this->json([], 204);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error' . $e
            ], 500);

        }
    }

    // Update user
    #[Route('/update', name: 'app_api_settings_update', methods: ['PATCH'])]
    public function updateSettings(Request $request, SettingsRepository $settingsRepository, EntryDataService $entryDataService, ValidatorInterface $validator): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $settingsToUpdate = $settingsRepository->findAll();
            if(!$settingsToUpdate)
            {
                $this->resetSettings($settingsRepository);
                $settingsToUpdate = $settingsRepository->findAll();
            }

            $settingsToUpdate = $entryDataService->defineKeysInEntity($content, $settingsToUpdate[0]);
            if ($settingsToUpdate === null) {
                return $this->json([
                    'error' => 'A problem has been encounter during entity modification'
                ], 400);
            }

            //Symfony validation
            $errors = $validator->validate($settingsToUpdate);
            if (count($errors) > 0) {
                return $this->json([
                    'error' => $errors
                ], 400);
            }

            $settingsRepository->save($settingsToUpdate, true);
            return $this->json($settingsToUpdate, 200);

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error'
            ], 500);

        }
    }

}
