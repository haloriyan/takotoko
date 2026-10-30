<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Models\UserStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function store(Request $request) {
        $email = $request->email;
        $user_name = explode("@", $email)[0];

        $store = Store::create([
            'package' => "basic",
            'name' => $request->name,
            'username' => Str::random(8),
            'address' => $request->address,
            'lattitude' => $request->lattitude,
            'longitude' => $request->longitude,
            'max_radius_attendance' => 10,
            'inventory_method' => "LIFO"
        ]);

        $user = User::create([
            'name' => $user_name,
            'email' => $email,
        ]);

        $access = UserStore::create([
            'role' => "owner",
            'user_id' => $user->id,
            'store_id' => $store->id,
        ]);

        User::where('id', $user->id)->update([
            'access_id' => $access->id,
        ]);

        return redirect()->route('admin.master.stores.detail', $store->username)->with([
            'message' => "Berhasil membuat toko",
        ]);
    }
    public function delete($id) {
        $sto = Store::where('id', $id);
        $store = $sto->first();

        if ($store->icon != null) {
            Storage::delete('public/store_icons/' . $store->icon);
        }

        $sto->delete();

        return redirect()->back()->with([
            'message' => $store->name . " berhasil dihapus"
        ]);
    }
}
