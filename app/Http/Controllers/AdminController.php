<?php

namespace App\Http\Controllers;

use App\Models\CmsCategory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function login(Request $request) {
        if ($request->method() == "GET") {
            return view('admin.login');
        } else {
            $loggingIn = Auth::guard('admin')->attempt([
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if (!$loggingIn) {
                return redirect()->back()->withErrors([
                    'Kombinasi email dan password salah'
                ]);
            }

            return redirect()->route('admin.dashboard');
        }
    }

    public function dashboard(Request $request) {
        $message = Session::get('message');

        return view('admin.dashboard', [
            'request' => $request,
            'message' => $message,
        ]);
    }
    public function cms() {
        $categories = CmsCategory::orderBy('name', 'ASC')->get();
        return view('admin.cms', [
            'categories' => $categories,
        ]);
    }

    public function user(Request $request) {
        $message = Session::get('message');
        $u = new User();

        $users = $u->with([
            'accesses.store'
        ])->paginate(25);

        return view('admin.user.index', [
            'request' => $request,
            'message' => $message,
            'users' => $users,
        ]);
    }
    public function stores(Request $request) {
        $message = Session::get('message');
        $sto = new Store();
        if ($request->paket != "") {
            $sto = $sto->where('package', $request->paket);
        }
        if ($request->q != "") {
            $sto = $sto->where('name', 'LIKE', '%'.$request->q.'%');
        }
        $stores = $sto->paginate(25);

        $allStores = Store::all(['id']);
        $basics = Store::where('package', 'basic')->get(['id']);
        $starters = Store::where('package', 'starter')->get(['id']);
        $pros = Store::where('package', 'pro')->get(['id']);

        return view('admin.store.index', [
            'request' => $request,
            'message' => $message,
            'stores' => $stores,
            'allStores' => $allStores,
            'basics' => $basics,
            'starters' => $starters,
            'pros' => $pros,
        ]);
    }
}
