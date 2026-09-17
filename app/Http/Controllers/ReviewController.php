<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Sales;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $invoiceNumber) {
        $rate = $request->rate;
        if ($rate == null) {
            return redirect()->back()->withErrors([
                'Kesalahan dalam mengisi ulasan'
            ]);
        }

        $sales = Sales::where('invoice_number', $invoiceNumber)
        ->with(['store', 'customer', 'items.product.images'])
        ->first();

        $review = Review::create([
            'store_id' => $sales->store_id,
            'sales_id' => $sales->id,
            'customer_id' => $sales->customer_id,
            'rate' => $rate,
            'body' => $request->body,
        ]);

        return redirect()->back()->with([
            'message' => "Berhasil mengirimkan ulasan"
        ]);
    }
}
