<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    public function detail($id) {
        $sales = Sales::where('id', $id)
        ->with([
            'customer', 'user', 'items.product.images'
        ])
        ->first();

        return response()->json([
            'sales' => $sales,
        ]);
    }

    public function callbackTripay(Request $request) {
        $invoiceNumber = $request->merchant_ref;
        $sl = Sales::where('invoice_number', $invoiceNumber);
        $sales = $sl->with([
            'user.devices',
            'customer',
            'items.product.images'
        ])
        ->first();
    

        $sl->update([
            'payment_status' => strtoupper($request->status),
        ]);

        foreach ($sales->user->devices as $dev) {
            $response = Http::withHeaders([
                'Content-Type' => "application/json"
            ])
            ->post("https://exp.host/--/api/v2/push/send", [
                'to' => $dev->push_token,
                'title' => "Pesanan " . $invoiceNumber . " Berhasil Dibayar!",
                'body' => $sales->customer->name . " telah membayar pesanan sebesar " . currency_encode($sales->total_pay),
                'data' => [
                    'action' => 'sales_paid',
                    'payload' => $sales
                ]
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function accept(Request $request, $invoiceNumber) {
        $sl = Sales::where('invoice_number', $invoiceNumber);
        $sales = $sl->with(['items'])->first();

        $sl->update([
            'payment_status' => "PAID"
        ]);

        return response()->json([
            'message' => "Pembayaran berhasil diterima."
        ]);
    }
    public function cancel(Request $request, $invoiceNumber) {
        $sl = Sales::where('invoice_number', $invoiceNumber);
        $sales = $sl->with(['items'])->first();

        $sl->update([
            'payment_status' => "CANCELLED"
        ]);

        foreach ($sales->items as $item) {
            ProductStock::where('id', $item->stock_id)->increment('quantity', $item->quantity);
        }

        return response()->json([
            'message' => "Pesanan dibatalkan."
        ]);
    }
}
