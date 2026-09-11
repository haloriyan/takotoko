<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SalesReport implements FromView, ShouldAutoSize
{
    public $store;
    public $sales;
    public $start_date;
    public $end_date;

    public function __construct($props)
    {
        $this->store = $props['store'];
        $this->sales = $props['sales'];
        $this->start_date = $props['start_date'];
        $this->end_date = $props['end_date'];
    }

    public function view(): View
    {
        return view('export.sales', [
            'store' => $this->store,
            'sales' => $this->sales,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);
    }
}
