<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/medias')]
class MediaController extends AbstractController
{
    // Create user
    #[Route('/create', name: 'app_media_create', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        dd("r");
        try{
            // Get the uploaded file
            $file = $request->files->get('file');
            
            // Check if a file was uploaded
            if (!$file) {
                return $this->json([
                    'error' => 'Server error'
                ], 590);
            }

            // Generate a unique file name
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the uploads directory
            $file->move(
                $this->getParameter('uploads_directory'),
                $fileName
            );

            // Save the user and the file ID in the database
            // ...

            // Return the ID or URL of the stored file to the Vue.js front-end
            return $this->json('Done', 200);
        }catch(e){
            return $this->json([
                'error' => 'Server error'
            ], 510);
        }
        
    }
}
