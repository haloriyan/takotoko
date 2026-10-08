<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesSheet implements FromView, ShouldAutoSize, WithTitle
{
    public $date;
    public $sales;

    public function __construct($props)
    {
        $this->date = $props['date'];
        $this->sales = $props['sales'];
    }

    public function title(): string
    {
        return Carbon::parse($this->date)->isoFormat('DD MMMM');
    }

    public function view(): View
    {
        return view('export.sales', [
            'date' => $this->date,
            'sales' => $this->sales,
        ]);
    }
}
