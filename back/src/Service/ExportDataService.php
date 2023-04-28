<?php

namespace App\Service;

use App\Entity\Anecdote;
use App\Repository\AnecdoteRepository;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class ExportDataService
{

    public function exportAllCSV(AnecdoteRepository $anecdoteRepository){

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=csv_export.csv');

        $output = fopen( 'csv_export.csv', 'w' );

        ob_end_clean();

        $datas = [];

        $anecdotes = $anecdoteRepository->findAll();

        $header_args = [];
        dd(get_object_vars($anecdotes[0]));
        foreach (get_object_vars($anecdotes[0]) as $key => $value) {
            array_push($header_args, $key);
        }

        fputcsv($output, $header_args);

        foreach ($anecdotes as $anecdote)
        {
            $anecdoteDatas = [];
            foreach ((array)$anecdote as $value) {
                if(gettype($value) === 'object')
                {
                    if(ClassUtils::getClass($value) === 'App\Entity\User')
                    {
                        array_push($anecdoteDatas, '(' . $value->getId() . ') ' . $value->getFirstName() . ' ' . $value->getLastName());
                    }
                } else {
                    array_push($anecdoteDatas, (string)$value);
                }
            }
            array_push($datas, $anecdoteDatas);
        }

        foreach($datas AS $data){
            fputcsv($output, $data);
        }

    }
}