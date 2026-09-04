<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserStore;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function store(Request $request) {
        $user = $request->user();
        $storeID = $user->access->store_id;
        $email = $request->email;

        $employee = User::where('email', $email)->first();
        if ($employee == null) {
            $employee = User::create([
                'name' => explode("@", $email)[0],
                'email' => $email,
                'password' => bcrypt('123456')
            ]);
        }

        $access = UserStore::create([
            'store_id' => $storeID,
            'user_id' => $employee->id,
            'role' => $request->role,
        ]);

        User::where('id', $employee->id)->update([
            'access_id' => $access->id,
        ]);

        return response()->json([
            'message' => "Berhasil menambahkan karyawan"
        ]);
    }
    public function delete(Request $request) {
        $acc = UserStore::where([
            ['user_id', $request->user_id],
            ['store_id', $request->store_id],
        ]);
        $access = $acc->with(['user'])->first();

        if ($access) {
            $acc->delete();
        }

        return response()->json([
            'message' => "Berhasil menghapus akses " . $access->user->name
        ]);
    }
    public function role(Request $request) {
        $role = $request->role;
        $acc = UserStore::where([
            ['user_id', $request->user_id],
            ['store_id', $request->store_id],
        ]);
        $access = $acc->with(['user'])->first();

        if ($access) {
            $acc->update(['role' => strtolower($role)]);
        }

        return response()->json([
            'message' => "Berhasil mengubah hak akses " . $access->user->name . " menjadi " . ucwords($role)
        ]);
    }
}
