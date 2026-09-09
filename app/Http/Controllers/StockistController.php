<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StockistController extends Controller
{
    public function home(Request $request) {
        $user = $request->user();
        $storeID = $user->access->store_id;
        $startDate = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');

        $filter = [
            ['store_id', $storeID]
        ];

        if ($request->q != "") {
            array_push($filter, [
                'name', 'LIKE', '%'.$request->q.'%'
            ]);
        }

        $products = Product::where($filter)
        ->with([
            'available_stocks',
            'movement_items.movement' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            },
            'images'
        ])
        ->orderBy('updated_at', 'DESC')
        ->paginate(25);
        
        $products->map(function ($prod, $p) {
            $prod->quantity = 0;
            $prod->inbound = 0;
            $prod->outbound = 0;
            $prod->opname = 0;

            $prod->available_stocks->map(function ($sto, $s) use ($prod) {
                $prod->quantity += $sto->quantity;
            });

            $prod->movement_items->map(function ($item) use ($prod) {
                if (isset($item->movement)) {
                    if ($item->movement->type == "IN") {
                        $prod->inbound += $item->quantity;
                    } else if ($item->movement->type == "OUT") {
                        $prod->outbound += $item->quantity;
                    } else {
                        $prod->opname += $item->quantity;
                    }
                }
            });
        });

        return response()->json([
            'products' => $products,
        ]);
    }
    public function searchProduct(Request $request) {
        $user = $request->user();
        $storeID = $user->access->store_id;

        $filter = [
            ['name', 'LIKE', '%'.$request->q.'%'],
            ['store_id', $storeID]
        ];

        $products = Product::where($filter)
        ->with([
            'images', 'available_stocks'
        ])
        ->take(10)->get();

        $products->map(function ($prod, $p) {
            $prod->available_stocks->map(function ($sto, $s) use ($prod) {
                $prod->quantity += $sto->quantity;
            });
        });

        return response()->json([
            'products' => $products,
        ]);
    }

    public function store(Request $request) {
        $user = $request->user();
        $storeID = $user->access->store_id;
        $items = $request->items;

        // Log::info(json_encode(
        //     $items, JSON_PRETTY_PRINT
        // ));

        $totalQuantity = 0;
        $totalCostPrice = 0;
        foreach ($items as $item) {
            $totalQuantity += $item['quantity'];
            $totalCostPrice += $item['cost_price'] * $item['quantity'];
        }

        $movement = StockMovement::create([
            'user_id' => $user->id,
            'store_id' => $storeID,
            'label' => "IN" . Carbon::now()->isoFormat('YYYYMMDDHHmmss'),
            'total_quantity' => $totalQuantity,
            'total_price' => $totalCostPrice,
            'type' => "IN"
        ]);

        foreach ($items as $item) {
            $stock = ProductStock::create([
                'store_id' => $storeID,
                'product_id' => $item['product']['id'],
                'label' => Str::slug($item['product']['name']) . "-" . Carbon::now()->isoFormat('YYYYMMDDHHmmss'),
                'cost_price' => $item['cost_price'],
                'quantity' => $item['quantity'],
                'start_quantity' => $item['quantity'],
            ]);

            $movementItem = StockMovementItem::create([
                'store_id' => $storeID,
                'movement_id' => $movement->id,
                'product_id' => $item['product']['id'],
                'stock_id' => $stock->id,
                'price' => $item['cost_price'],
                'quantity' => $item['quantity'],
                'total_price' => $item['cost_price'] * $item['quantity'],
            ]);
        }

        return response()->json([
            'message' => "Berhasil menambahkan stok produk"
        ]);
    }
}
