<?php

namespace App\Service;

use App\Repository\AnecdoteRepository;
use App\Repository\UserRepository;

class ExportDataService
{
    public function exportAllCSV(AnecdoteRepository $anecdoteRepository){
        $header_args = ["Object", "ID"];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=csv_export.csv');

        $output = fopen( 'php://output', 'w' );

        ob_end_clean();

        fputcsv($output, $header_args);

        $datas = [];

        $anecdotes = $anecdoteRepository->findAll();

//        foreach ($anecdotes as $anecdote)
//        {
//            array_push($datas, )
//        }
    }
}