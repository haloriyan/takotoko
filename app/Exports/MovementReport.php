<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MovementReport implements Export, WithMultipleSheets
{
    public $dates;
    public $theSheets;

    public function __construct($props)
    {
        $this->dates = $props['dates'];
        $theSheets = [];

        foreach ($props['dates'] as $dt => $date) {
            array_push($theSheets, new MovementProductSheet([
                'date' => $dt,
                'products' => $date,
            ]));
        }

        $this->theSheets = $theSheets;
    }
    
    public function sheets(): array
    {
        return $this->theSheets;
    }
}
