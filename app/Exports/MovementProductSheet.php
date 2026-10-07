<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Override;

class MovementProductSheet implements FromView, ShouldAutoSize, WithTitle {
    public $date;
    public $products;

    public function __construct($props)
    {
        $this->date = $props['date'];
        $this->products = $props['products'];
    }
    public function title(): string
    {
        return Carbon::parse($this->date)->isoFormat('DD MMMM');
    }

    public function view(): View
    {
        return view('export.movement_product', [
            'date' => $this->date,
            'products' => $this->products,
        ]);
    }
}