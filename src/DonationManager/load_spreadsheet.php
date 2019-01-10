<?php
require 'vendor/autoload.php';
require_once 'models/badge_types.php';
require_once 'models/donator.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class LoadSpreadsheet
{
    /**
     * @return array|bool
     */
    static function loadData()
    {
        try {
            $reader = new Xlsx();
            //$spreadsheet = $reader->load('xlsx/progetto20543.xlsx');
            $spreadsheet = $reader->load('/data/donations.xlsx');

            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $donators = [];

            foreach ($sheetData as $row)
            {
                if($row['A'] == 'Date' || empty($row['A']))
                    continue;

                $donator = new Donator($row);
                if(isset($donators[$donator->email])) {
                    // aggregate data if user donated more than once
                    $donator->donation = $donators[$donator->email]->donation + $donator->donation;
                    $donator->date = $donators[$donator->email]->date;
                }
                $donators[$donator->email] = $donator;
            }
            return $donators;
        } catch
        (\PhpOffice\PhpSpreadsheet\Exception $ex) {
            echo $ex;
            return false;
        }
    }
}