<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sales;
use App\Models\Store;
use App\Models\StorePlan;
use App\Models\UserStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class AdminStoreController extends Controller
{
    public function index(Request $request, $username) {
        $store = Store::where('username', $username)
        ->with(['accesses'])
        ->first();

        $filter = [['store_id', $store->id]];

        $products = Product::where($filter)->orderBy('created_at', 'DESC')->take(5)->get();
        $sales = Sales::where($filter)->orderBy('created_at', 'DESC')->take(5)->get();

        $productsCount = Product::where('store_id', $store->id)->with(['images'])->get(['id'])->count();
        $salesCount = Sales::where('store_id', $store->id)->with(['customer'])->get(['id'])->count();

        return view('admin.store.detail', [
            'store' => $store,
            'salesCount' => $salesCount,
            'productsCount' => $productsCount,
            'products' => $products,
            'sales' => $sales,
        ]);
    }
    public function employee(Request $request, $username) {
        $store = Store::where('username', $username)
        ->with(['accesses'])
        ->first();

        $emp = UserStore::where('store_id', $store->id);
        if ($request->q != "") {
            $emp = $emp->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%');
            });
        }
        $employees = $emp->with(['user'])->get();

        return view('admin.store.employee', [
            'store' => $store,
            'request' => $request,
            'employees' => $employees,
        ]);
    }
    public function product(Request $request, $username) {
        $store = Store::where('username', $username)
        ->with(['accesses'])
        ->first();

        $filter = [['store_id', $store->id]];
        if ($request->q != "") {
            array_push($filter, ['name', 'LIKE', '%'.$request->q.'%']);
        }
        $products = Product::where($filter)
        ->with(['images'])
        ->paginate(25);

        return view('admin.store.product', [
            'store' => $store,
            'request' => $request,
            'products' => $products,
        ]);
    }
    public function productDelete(Request $request, $username, $productID) {
        $prod = Product::where('id', $productID);
        $product = $prod->with(['images'])->first();

        // $prod->delete();
        foreach ($product->images as $img) {
            // Storage::delete('public/')
            $path = public_path("storage/product_images/{$productID}/{$img->filename}");
            Log::info($path);
        }
    }
    public function membership(Request $request, $username) {
        $store = Store::where('username', $username)
        ->first();
        $message = Session::get('message');

        $transactions = StorePlan::where('store_id', $store->id)
        ->orderBy('created_at', 'DESC')
        ->paginate(25);

        return view('admin.store.membership', [
            'store' => $store,
            'request' => $request,
            'message' => $message,
            'transactions' => $transactions,
        ]);
    }
    public function membershipStatus(Request $request, $username, $trxID) {
        $status = strtoupper($request->status);
        $trx = StorePlan::where('id', $trxID);
        $trx->update([
            'payment_status' => $status,
        ]);

        return redirect()->back()->with([
            'message' => "Berhasil mengubah status transaksi menjadi " . $status
        ]);
    }
}
