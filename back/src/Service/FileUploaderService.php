<?php

namespace App\Service;

use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderService extends AbstractController
{
    // TODO : remake with right code
    public function toMedia(UploadedFile $picture, string $dir): Media
    {
        $media = new Media();
        $originalFileName = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $originalFileName . '-' . uniqid() . '.' . $picture->guessExtension();
        $media->setPath($dir.'/'. $fileName);
        $media->setName($fileName);
        $media->setFormat($picture->getClientOriginalExtension());
        $picture->move($dir, $fileName);
        return $media;
    }
}