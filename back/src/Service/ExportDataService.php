<?php

namespace App\Service;

use Doctrine\Common\Util\ClassUtils;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ReflectionClass;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExportDataService
{

    public function exportAllCSV($repositories, $em)
    {
        try {

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
                        if (!$value) {
                            array_push($entityDatas, 'null');
                        } else if (gettype($value) === 'object') {
                            if (ClassUtils::getClass($value) === 'App\Entity\User') {
                                array_push($entityDatas, '(' . $value->getId() . ') ' . $value->getFirstName() . ' ' . $value->getLastName());
                            } else if (ClassUtils::getClass($value) === 'App\Entity\Media') {
                                array_push($entityDatas, '(' . $value->getId() . ') ' . $value->getName());
                            } else if (ClassUtils::getClass($value) === 'DateTime') {
                                array_push($entityDatas, $value->format('H:i:s'));
                            } else if (ClassUtils::getClass($value) === 'Doctrine\ORM\PersistentCollection') {
                                if (!$value[0]) {
                                    array_push($entityDatas, 'null');
                                } else {

                                    if (ClassUtils::getClass($value[0]) === 'App\Entity\User' || ClassUtils::getClass($value[0]) === 'App\Entity\Guest') {
                                        $users = "";
                                        foreach ($value as $item) {
                                            $users = $users . '(' . $item->getId() . ') ' . $item->getFirstName() . ' ' . $item->getLastName() . ' ';
                                        }
                                        array_push($entityDatas, $users);
                                    } else if (ClassUtils::getClass($value[0]) === 'App\Entity\Media') {
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
                                    }
                                }
                            }
                        } else if (gettype($value) === 'array') {
                            $strings = "";
                            foreach ($value as $item) {
                                $strings = $strings . $item;
                            }
                            array_push($entityDatas, $strings);

                        } else if (gettype($value) === 'boolean') {
                            if ($value === 0) {
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

            $writer = new Xlsx($spreadsheet);
            $writer->save('export' . uniqid() . '.xlsx');

        } catch (\Exception $e) {
            return null;
        }
    }
}