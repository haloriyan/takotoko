<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        $storeID = $user->access->store_id;

        $carts = Cart::where([
            ['user_id', $user->id],
        ])
        ->with(['product', 'stock'])
        ->get();

        return response()->json([
            'carts' => $carts,
        ]);
    }

    public function store(Request $request) {
        $user = $request->user();
        $storeID = $user->access->store_id;
        $productID = $request->product_id;
        $stockID = $request->stock_id;

        $sto = ProductStock::where('id', $stockID);
        $stock = $sto->first();
        
        if ($stock && $stock->quantity <= 0) {
            return response()->json([
                'message' => "Kesalahan"
            ], 404);
        }

        $product = json_decode(json_encode($request->product), false);

        $cart = Cart::where([
            ['product_id', $product->id],
            ['stock_id', $product->stock->id],
            ['user_id', $user->id]
        ])->first();

        if ($cart == null) {
            $cart = Cart::create([
                'product_id' => $product->id,
                'stock_id' => $product->stock->id,
                'store_id' => $storeID,
                'user_id' => $user->id,
                'price' => $product->price,
                'quantity' => 1,
                'total_price' => $product->price,
                'is_composition' => false,
            ]);

            foreach ($product->compositions as $c => $comp) {
                Cart::create([
                    'product_id' => $comp->composition->id,
                    'stock_id' => $comp->composition->stock->id,
                    'store_id' => $storeID,
                    'user_id' => $user->id,
                    'price' => $comp->composition->price,
                    'quantity' => $comp->quantity,
                    'total_price' => $comp->quantity * $comp->composition->price,
                    'is_composition' => true,
                    'cart_parent_id' => $cart->id,
                    'composition_id' => $comp->id,
                ]);

                ProductStock::where('id', $comp->composition->stock->id)
                ->decrement('quantity', $comp->quantity);
            }
        } else {
            $newQuantity = $cart->quantity + 1;
            $newPrice = $product->price * $newQuantity;

            Cart::where('id', $cart->id)->update([
                'quantity' => $newQuantity,
                'total_price' => $newPrice,
            ]);
        }

        $sto->decrement('quantity');

        // foreach ($product->compositions as $c => $comp) {
        //     ProductStock::where('id', $comp->composition->stock->id)->decrement('quantity');
        // }

        return response()->json(['ok']);
    }

    public function remove(Request $request) {
        $user = $request->user();
        $crt = Cart::where('id', $request->cart_id);
        $cart = $crt->with(['children.composition'])->first(); // "children" means for product composition

        if ($cart && $cart->quantity == 1) {
            $crt->delete();
        } else {
            $crt->decrement('quantity');
        }

        foreach ($cart->children as $c => $child) {
            $chi = Cart::where('id', $child->id);
            $childCart = $chi->with(['composition'])->first();

            if ($childCart) {
                $chi->increment('quantity', @$childCart->composition->quantity);
            }
            ProductStock::where('id', $child->stock_id)->increment('quantity', @$child->composition->quantity);
        }

        $sto = ProductStock::where('id', $cart->stock_id);
        $sto->increment('quantity');

        return response()->json(['ok']);
    }

    public function delete(Request $request) {
        $user = $request->user();
        $productID = $request->product_id;

        $cart = Cart::where([
            ['user_id', $user->id],
            ['product_id', $productID]
        ])
        ->with(['children.composition'])
        ->first();

        if ($cart) {
            $stockID = $cart->stock_id;
            $quantity = $cart->quantity;

            $cart->delete();

            ProductStock::where('id', $stockID)->increment('quantity', $quantity);

            foreach ($cart->children as $child) {
                ProductStock::where('id', $child->stock_id)
                ->increment('quantity', $child->composition->quantity);
            }
        }

        return response()->json(['ok']);
    }
}
