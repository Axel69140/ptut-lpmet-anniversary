<?php

namespace App\Service;

use Doctrine\Common\Util\ClassUtils;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ReflectionClass;
use App\Repository\UserRepository;

class ExportDataService
{

    public function exportAllCSV($repositories, $em, $participants)
    {
        try {
            $path = 'Exports/';

            $spreadsheet = new Spreadsheet();

            foreach ($repositories as $repository) {
                $repoName = $em->getClassMetadata($repository->getClassName());

                if ($repository !== $repositories[0]) {
                    $activeSheet = $spreadsheet->createSheet();
                } else {
                    $activeSheet = $spreadsheet->getActiveSheet();
                }

                $activeSheet->setTitle($repoName->getTableName());
                $datas = [];

                $entities = $em->getRepository($repoName->getName())->findAll();

                if($entities)
                {
                    $reflectionClass = new ReflectionClass($entities[0]::class);

                    $header_args = [];

                    foreach ($reflectionClass->getProperties() as $key => $property) {
//                if ($property->getName() === 'password') {
//                    $indexToRemove = chr($key);
//                    dd($indexToRemove);
//                }
                        array_push($header_args, $property->getName());
                    }

                    array_push($datas, $header_args);

                    foreach ($entities as $entity) {
                        $entityDatas = [];
                        foreach ((array)$entity as $value) {
                            if ($value === null) {
                                array_push($entityDatas, ' - ');
                            } else if (gettype($value) === 'object') {
                                if(!ClassUtils::getClass($value))
                                {
                                    array_push($entityDatas, ' - ');
                                }else if (ClassUtils::getClass($value) === 'App\Entity\User') {
                                    array_push($entityDatas, '(' . $value->getId() . ') ' . $value->getFirstName() . ' ' . $value->getLastName());
                                } else if (ClassUtils::getClass($value) === 'App\Entity\Media') {
                                    array_push($entityDatas, '(' . $value->getId() . ') ' . $value->getName());
                                } else if (ClassUtils::getClass($value) === 'DateTime') {
                                    array_push($entityDatas, $value->format('H:i:s'));
                                } else if (ClassUtils::getClass($value) === 'Doctrine\ORM\PersistentCollection') {
                                    if (!$value[0]) {
                                        array_push($entityDatas, '-');
                                    } else {
                                        if (ClassUtils::getClass($value[0]) === 'App\Entity\User' || ClassUtils::getClass($value[0]) === 'App\Entity\Guest') {
                                            $users = "";
                                            foreach ($value as $item) {
                                                $users = $users . '(' . $item->getId() . ') ' . $item->getFirstName() . ' ' . $item->getLastName() . ' ';
                                            }
                                            array_push($entityDatas, $users);
                                        } else if (ClassUtils::getClass($value[0]) === 'App\Entity\Media' || ClassUtils::getClass($value[0]) === 'App\Entity\Activity') {
                                            $medias = "";
                                            foreach ($value as $item) {
                                                $medias = $medias . '(' . $item->getId() . ') ' . $item->getName() . ' ';
                                            }
                                            array_push($entityDatas, $medias);
                                        } else if (ClassUtils::getClass($value[0]) === 'App\Entity\Article' || ClassUtils::getClass($value[0]) === 'App\Entity\TimelineStep') {
                                            $articles = "";
                                            foreach ($value as $item) {
                                                $articles = $articles . '(' . $item->getId() . ') ' . $item->getTitle() . ' ';
                                            }
                                            array_push($entityDatas, $articles);
                                        } else if (ClassUtils::getClass($value[0]) === 'App\Entity\Anecdote') {
                                            $anecdotes = "";
                                            foreach ($value as $item) {
                                                $anecdotes = $anecdotes . '(' . $item->getId() . ') ';
                                            }
                                            array_push($entityDatas, $anecdotes);
                                        } else {
                                            array_push($entityDatas, ' - ');
                                        }
                                    }
                                } else {
                                    array_push($entityDatas, ' - ');
                                }
                            } else if (gettype($value) === 'array') {
                                $strings = "";
                                foreach ($value as $item) {
                                    $strings = $strings . ' ' . $item;
                                }
                                array_push($entityDatas, $strings);

                            } else if (gettype($value) === 'boolean') {
                                if ($value === false) {
                                    array_push($entityDatas, '0');
                                } else {
                                    array_push($entityDatas, '1');
                                }
                            } else {
                                array_push($entityDatas, (string)$value);
                            }
                        }
                        array_push($datas, $entityDatas);
                    }

                    $activeSheet->fromArray($datas);
//            $activeSheet->removeColumnByIndex($indexToRemove);
                }
            }

            if($participants)
            {

                $activeSheet = $spreadsheet->createSheet();
                $activeSheet->setTitle('Participants');

                $datas = [];

                $users = $em->getRepository('App\Entity\User')->findBy(['isParticipated' => true]);
                $guests = $em->getRepository('App\Entity\Guest')->findAll();

                array_push($datas, ["id", "Email", "Nom", "Prénom"]);

                if($users)
                {
                    foreach ($users as $user)
                    {
                        array_push($datas, [$user->getId(), $user->getEmail(), $user->getLastName(), $user->getFirstName()]);
                    }
                }

                if($guests)
                {
                    foreach ($guests as $guest)
                    {
                        array_push($datas, [$guest->getId(), $guest->getEmail(), $guest->getLastName(), $guest->getFirstName()]);
                    }
                }

                $activeSheet->fromArray($datas);
            }

            $writer = new Xlsx($spreadsheet);
            $saveName = $path . 'export-' . uniqid() . '.xlsx';
            $writer->save($saveName);
            return ['fileToDownload' => $saveName];

        } catch (\Exception $e) {
            return null;
        }
    }
}