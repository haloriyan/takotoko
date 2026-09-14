<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $storeID = $user->access->store_id;
        $store = $user->access->store;
        $canAdd = false;
        $canScan = false;

        $plan = paket($storeID);
        $thePlan = config('plans')[$plan->plan];
        $canScan = $thePlan['ai'];

        if (gettype($thePlan['transaksi']) == "integer") {
            $salesCount = Sales::where('store_id', $storeID)
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::now()->endOfDay()->format('Y-m-d H:i:s'),
            ])
            ->get(['id'])->count();
            $canAdd = $salesCount < $thePlan['transaksi'];
        } else {
            $canAdd = true;
        }

        if (!$canAdd) {
            return response()->json([
                'categories' => [],
                'carts' => [],
                'can_add' => $canAdd,
                'can_scan' => false,
            ]);
        }

        $carts = Cart::where([
            ['user_id', $user->id],
        ])
        ->with([
            'product.images',
            'stock',
        ])
        ->get();

        $inventoryMethod = strtoupper($store->inventory_method);
        $today = now()->toDateString();

        $stockQuery = function ($query) use ($inventoryMethod, $today) {
            $query->where('quantity', '>', 0)
                ->where(function ($q) use ($today) {
                    $q->whereNull('expired_at')
                        ->orWhereDate('expired_at', '>=', $today);
                });

            switch ($inventoryMethod) {
                case 'FIFO':
                    $query->orderBy('created_at');
                    break;

                case 'LIFO':
                    $query->orderByDesc('created_at');
                    break;

                case 'FEFO':
                    $query->orderByRaw('expired_at IS NULL')
                        ->orderBy('expired_at')
                        ->orderBy('created_at');
                    break;

                case 'CHEAPEST':
                    $query->orderBy('cost_price')
                        ->orderBy('created_at');
                    break;
            }

            $query->take(1);
        };

        $categories = Category::where([
            ['store_id', $storeID],
            ['pos_available', true],
        ])
        ->whereHas('products.stock', $stockQuery)
        ->with([
            'products' => function ($query) use ($stockQuery) {
                $query->whereHas('stock', $stockQuery);
            },
            'products.images',
            'products.stock' => $stockQuery,
        ])
        ->orderBy('position', 'ASC')
        ->get();

        $uncategorizedProducts = Product::where('store_id', $storeID)
            ->whereDoesntHave('categories')
            ->whereHas('stock', $stockQuery)
            ->with([
                'images',
                'stock' => $stockQuery,
            ])
            ->get();

        $categories->push(collect([
            'store_id' => $storeID,
            'name' => 'Lainnya',
            'icon' => null,
            'color' => '#2196f3',
            'position' => $categories->count(),
            'pos_available' => true,
            'products' => $uncategorizedProducts,
        ]));

        return response()->json([
            'categories' => $categories,
            'carts' => $carts,
            'can_add' => $canAdd,
            'can_scan' => $canScan,
        ]);
    }
    public function indexOri(Request $request) {
        $user = $request->user();
        $storeID = $user->access->store_id;
        $store = $user->access->store;
        
        $inventoryMethod = strtoupper($store->inventory_method); // Could be FIFO, LIFO, FEFO, CHEAPEST (sort )

        $categories = Category::where([
            ['store_id', $storeID],
            ['pos_available', true]
        ])
        ->whereHas('products.stock', function ($query) {
            $query->where('quantity', '>', 0);
            $query->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhereDate('expired_at', '>=', now()->toDateString());
            });
        })
        ->with([
            'products' => function ($query) {
                $query->whereHas('stock', function ($q) {
                    $q->where('quantity', '>', 0);
                    $q->where(function ($sq) {
                        $sq->whereNull('expired_at')
                            ->orWhereDate('expired_at', '>=', now()->toDateString());
                    });
                });
            },
            'products.images',
            'products.stock' => function ($query) use ($inventoryMethod) {
                $query->where('quantity', '>', 0);
                $query->where(function ($q) {
                    $q->whereNull('expired_at')
                        ->orWhereDate('expired_at', '>=', now()->toDateString());
                });

                switch ($inventoryMethod) {
                    case 'FIFO':
                        $query->orderBy('created_at');
                        break;

                    case 'LIFO':
                        $query->orderByDesc('created_at');
                        break;

                    case 'FEFO':
                        $query->orderByRaw('expired_at IS NULL')
                            ->orderBy('expired_at')
                            ->orderBy('created_at');
                        break;

                    case 'CHEAPEST':
                        $query->orderBy('cost_price')
                            ->orderBy('created_at');
                        break;
                }

                $query->take(1);
            }
        ])
        ->orderBy('position', 'ASC')
        ->get();

        // Continue this
        $uncategorizedProducts = Product::where('store_id', $storeID)
        ->whereDoesntHave('categories')
        ->with([
            'images', 'stock'
        ])
        ->get();

        $categories[] = collect([
            'store_id' => $storeID,
            'name' => "Lainnya",
            'icon' => null, 'color' => "#2196f3", 'position' => count($categories) - 1,
            'pos_available' => true,
            'products' => $uncategorizedProducts,
        ]);

        $carts = Cart::where([
            ['user_id', $user->id],
        ])
        ->with([
            'product.images',
            'stock'
        ])
        ->get();

        return response()->json([
            'categories' => $categories,
            'carts' => $carts,
        ]);
    }
    public function place(Request $request) {
        $customer = $request->customer;
        $user = $request->user();
        $storeID = $user->access->store_id;
        $totalMargin = 0;
        $totalQuantity = 0;
        $totalPrice = 0;
        $totalCostPrice = 0;

        if (gettype($customer) == "string") {
            $customer = Customer::create([
                'store_id' => $storeID,
                'name' => $request->customer,
                'point' => 0,
            ]);
        } else {
            $customer = Customer::where('id', $customer['id'])->first();
        }

        $carts = Cart::where([
            ['store_id', $storeID],
            ['user_id', $user->id]
        ])
        ->with(['product', 'stock'])
        ->get();

        foreach ($carts as $cart) {
            $product = $cart->product;
            $stock = $cart->stock;
            $quantity = $cart->quantity;

            $margin = ($product->price - $stock->cost_price) * $quantity;
            $sumPrice = $quantity * $product->price;

            $totalPrice += $sumPrice;
            $totalMargin += $margin;
            $totalQuantity += $quantity;
            $totalCostPrice += $stock->cost_price;
        }

        $movement = StockMovement::create([
            'store_id' => $storeID,
            'user_id' => $user->id,
            'label' => "OUT" . Carbon::now()->format('YmdHis'),
            'type' => "OUT",
            'total_quantity' => $totalQuantity,
            'total_price' => $totalCostPrice,
        ]);

        $sales = Sales::create([
            'store_id' => $storeID,
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'movement_id' => $movement->id,
            'invoice_number' => "INV-".$storeID."-".Carbon::now()->format('YmdHis'),
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
            'total_margin' => $totalMargin,
        ]);

        foreach ($carts as $cart) {
            $product = $cart->product;
            $stock = $cart->stock;
            $quantity = $cart->quantity;

            $movementItem = StockMovementItem::create([
                'store_id' => $storeID,
                'movement_id' => $movement->id,
                'product_id' => $product->id,
                'stock_id' => $stock->id,
                'price' => $stock->cost_price,
                'quantity' => $cart->quantity,
                'total_price' => $cart->quantity * $cart->stock->cost_price,
            ]);

            $salesItem = SalesItem::create([
                'store_id' => $storeID,
                'sales_id' => $sales->id,
                'product_id' => $product->id,
                'stock_id' => $stock->id,
                'movement_item_id' => $movementItem->id,
                'price' => $product->price,
                'quantity' => $quantity,
                'total_price' => $quantity * $product->price,
                'margin' => ($product->price - $stock->cost_price) * $quantity,
                'notes' => $cart->notes,
            ]);

            Cart::where('id', $cart->id)->delete();
        }

        $sales = Sales::where('id', $sales->id)
        ->with([
            'customer', 'user', 'items.product.images'
        ])
        ->first();

        return response()->json([
            'sales' => $sales,
        ]);

    }
}
