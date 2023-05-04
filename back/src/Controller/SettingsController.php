<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Repository\ActivityRepository;
use App\Repository\AnecdoteRepository;
use App\Repository\SettingsRepository;
use App\Repository\UserRepository;
use App\Service\ExportDataService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Service\EntryDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
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

            if (!$settings) {
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
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function resetSettings(SettingsRepository $settingsRepository): JsonResponse
    {
        try {

            $settings = $settingsRepository->findAll();

            if (!$settings) {
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

    // Update settings
    #[Route('/update', name: 'app_api_settings_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function updateSettings(Request $request, SettingsRepository $settingsRepository, EntryDataService $entryDataService, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);
            $settingsToUpdate = $settingsRepository->findAll();
            if (!$settingsToUpdate) {
                $this->resetSettings($settingsRepository);
                $settingsToUpdate = $settingsRepository->findAll();
            }

            $settingsToUpdate = $entryDataService->defineKeysInEntity($content, $settingsToUpdate[0], $em);
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

    // Export datas
    #[Route('/export-csv', name: 'app_settings_export_csv', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Vous n\'avez pas les droits suffisants')]
    public function exportCSV(Request $request, ExportDataService $exportDataService, EntityManagerInterface $em): JsonResponse
    {
        try {

            $content = json_decode($request->getContent(), true);

            if($content)
            {
                foreach ($content as $key => $item) {
                    if ($key === "datasToExport") {
                        $datasToExport = $item;
                    } else if ($key === "exportParticipant") {
                        $exportParticipant = $item;
                    } else {
                        return $this->json([
                            'error' => 'Unknown keys'
                        ], 403);
                    }
                }

                if (!$datasToExport || ($exportParticipant !== false && $exportParticipant !== true)) {
                    return $this->json([
                        'error' => 'No repository provided or asking for much properties'
                    ], 403);
                }

                $repos = [];

                foreach ($datasToExport as $data) {
                    try {
                        $repo = $em->getRepository("App\Entity\\" . $data);
                        array_push($repos, $repo);
                    } catch (\Exception $e) {
                        return $this->json([
                            'error' => 'Can\'t find repo'
                        ], 400);
                    }
                }

                $fileToReturn = $exportDataService->exportAllCSV($repos, $em, $exportParticipant);
                if ($fileToReturn === null) {
                    return $this->json([
                        'error' => 'A problem has been encounter during export'
                    ], 400);
                } else {
                    return $this->json($fileToReturn, 200);
                }
            } else {
                return $this->json([
                    'error' => 'No content provided'
                ], 403);
            }

        } catch (\Exception $e) {

            return $this->json([
                'error' => 'Server error' . $e->getMessage()
            ], 500);

        }
    }

}
