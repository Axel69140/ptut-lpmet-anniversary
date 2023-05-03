<?php

namespace App\Service;

use Doctrine\Common\Util\ClassUtils;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\Response;
use ReflectionClass;

class ExportDataService
{

    public function exportAllCSV($repositories, $em){

        // Create a new spreadsheet object
        $spreadsheet = new Spreadsheet();

// Add data to the first sheet
        $worksheet1 = $spreadsheet->getActiveSheet();
        $worksheet1->setTitle('Sheet 1');
        $worksheet1->setCellValue('A1', 'Value 1');
        $worksheet1->setCellValue('B1', 'Value 2');
        $worksheet1->setCellValue('C1', 'Value 3');

// Add data to the second sheet
        $worksheet2 = $spreadsheet->createSheet();
        $worksheet2->setTitle('Sheet 2');
        $worksheet2->setCellValue('A1', 'Value 4');
        $worksheet2->setCellValue('B1', 'Value 5');
        $worksheet2->setCellValue('C1', 'Value 6');

// Create a new CSV writer object
        $writer = new Csv($spreadsheet);

// Set the delimiter to a comma
        $writer->setDelimiter(',');

// Set the sheet index to export
        $writer->setSheetIndex(0);

// Save the CSV file for sheet 1
        $csvData1 = $writer->getContent();

// Set the sheet index to export
        $writer->setSheetIndex(1);

// Save the CSV file for sheet 2
        $csvData2 = $writer->getContent();

// Set the filename and response headers
        $filename = 'example.csv';
        $response = new Response($csvData1 . $csvData2);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

// Return the response
        return $response;

//        header('Content-Type: text/csv; charset=utf-8');
//        header('Content-Disposition: attachment; filename=export-' . uniqid() . '.csv');
//
//        $output = fopen( 'export-' . /*uniqid() . */'.csv', 'w' );
//
//        ob_end_clean();
//
//        foreach ($repositories as $repository)
//        {
//            //$spreadsheet->createSheet();
//            //$spreadsheet->setActiveSheetIndexByName($em->getClassMetadata($repository->getClassName()));
//            $activeSheet = $spreadsheet->getActiveSheet();
//            $activeSheet->setTitle('test');
//            $datas = [];
//
//            $entities = $em->getRepository($em->getClassMetadata($repository->getClassName())->getName())->findAll();
//            $reflectionClass = new ReflectionClass($entities[0]::class);
//
//            $header_args = [];
//
//            foreach ($reflectionClass->getProperties() as $property) {
//                array_push($header_args, $property->getName());
//            }
//
//            fputcsv($output, $header_args);
//
//            foreach ($entities as $entity)
//            {
//                $entityDatas = [];
//                foreach ((array)$entity as $value) {
//                    if(gettype($value) === 'object')
//                    {
//                        if(ClassUtils::getClass($value) === 'App\Entity\User')
//                        {
//                            array_push($entityDatas, '(' . $value->getId() . ') ' . $value->getFirstName() . ' ' . $value->getLastName());
//                        }
//                    } else {
//                        array_push($entityDatas, (string)$value);
//                    }
//                }
//                array_push($datas, $entityDatas);
//            }
//
//            $activeSheet->fromArray($datas);
//        }

    }
}