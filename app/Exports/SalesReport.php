<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SalesReport implements Export, WithMultipleSheets
{
    public $dates;
    public $theSheets;

    public function __construct($props)
    {
        $this->dates = $props['dates'];
        $theSheets = [];

        foreach ($props['dates'] as $dt => $sales) {
            array_push($theSheets, new SalesSheet([
                'date' => $dt,
                'sales' => $sales,
            ]));
        }

        $this->theSheets = $theSheets;
    }

    public function sheets(): array
    {
        return $this->theSheets;
    }
}
