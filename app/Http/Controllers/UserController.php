<?php

namespace App\Http\Controllers;

use App\Models\CmsContent;
use App\Models\Customer;
use App\Models\Otp;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\Store;
use App\Models\StorePlan;
use App\Models\User;
use App\Notifications\Otp as NotificationsOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user();
        $onboarding = false;
        $plans = config('plans');

        if ($user) {
            $user = User::where('id', $user->id)->with(['accesses.store.plan', 'access.store.plan'])->first();
            $needRefetch = false;

            if ($user->accesses->count() == 0) {
                $onboarding = true;
            }

            foreach ($user->accesses as $access) {
                $store = $access->store;
                $plan = StorePlan::where([
                    ['store_id', $store->id],
                    ['payment_status', 'PAID']
                ])
                ->orderBy('expired_at', 'DESC')
                ->first();

                if ($plan != null) {
                    $expiredAt = Carbon::parse($plan->expired_at);
                    if ($expiredAt->isPast()) {
                        Store::where('id', $store->id)->update([
                            'package' => 'basic',
                        ]);
                        $needRefetch = true;
                    } else {
                        if ($plan->plan != $store->package) {
                            Store::where('id', $store->id)->update([
                                'package' => $plan->plan,
                            ]);
                            $needRefetch = true;
                        }
                    }
                }
            }

            if ($needRefetch) {
                $user = User::where('id', $user->id)->with(['accesses.store', 'access.store'])->first();
            }
        }

        return response()->json([
            'user' => $user,
            'onboarding' => $onboarding,
            'plans' => $plans,
        ]);
    }

    public function switchAccess(Request $request)
    {
        $user = $request->user();
        $u = User::where('id', $user->id);
        $u->update([
            'access_id' => $request->access_id,
        ]);
        $user = $u->with(['access.store', 'accesses'])->first();

        return response()->json([
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        $email = $request->email;
        $status = 200;
        $message = 'Berhasil login';
        $whitelist = config('app.whitelist_emails');

        $u = User::where('email', $email);
        $user = $u->first();

        if ($user == null) {
            $name = $request->name ?? explode('@', $email)[0];

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('123456'),
            ]);
            $user = $u->first();
        }

        if (! in_array($email, $whitelist)) {
            $code = rand(1111, 9999);
            $otp = Otp::create([
                'user_id' => $user->id,
                'code' => $code,
                'has_used' => false,
                'purpose' => 'login',
                'expired_at' => Carbon::now()->addMinutes(30)->format('Y-m-d H:i:s'),
            ]);

            $user->notify(new NotificationsOtp([
                'user' => $user,
                'otp' => $otp,
            ]));
        }

        return response()->json([
            'status' => $status,
            'user' => $user,
        ]);
    }

    public function otp(Request $request)
    {
        $code = $request->code;
        $email = $request->email;
        $whitelist = config('app.whitelist_emails');

        $user = User::where('email', $email)->with(['accesses.store', 'access.store'])->first();
        $token = null;
        $onboarding = ($user && $user->accesses->count() == 0) ? true : false;

        if (! $user) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found',
            ]);
        }

        // ✅ Bypass OTP if email is whitelisted
        if (in_array($email, $whitelist)) {
            $token = $user->createToken('app')->plainTextToken;

            return response()->json([
                'status' => 200,
                'message' => 'ok (whitelist)',
                'token' => $token,
                'user' => $user,
                'onboarding' => $onboarding,
            ]);
        }

        $o = Otp::where([
            ['user_id', $user->id],
            ['code', $code],
            ['has_used', false],
            ['expired_at', '>', Carbon::now()],
        ]);

        $otp = $o->first();

        if ($otp === null) {
            return response()->json([
                'status' => 405,
                'message' => 'Kode OTP tidak valid',
                'token' => null,
                'onboarding' => $onboarding,
            ]);
        }

        $token = $user->createToken('app')->plainTextToken;
        $o->update(['has_used' => true]);

        return response()->json([
            'status' => 200,
            'message' => 'ok',
            'token' => $token,
            'user' => $user,
            'onboarding' => $onboarding,
        ]);
    }
    public function home(Request $request) {
        $user = $request->user();
        $storeID = $user->access->store_id;
        $date = Carbon::now();

        $contents = CmsContent::inRandomOrder()
        ->with(['categories', 'slides'])
        ->take(15)->get();

        $revenue = SalesItem::where('store_id', $storeID)
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::now()->endOfDay()->format('Y-m-d H:i:s'),
            ])
            ->sum('total_price');

        $highestVolume = SalesItem::where('store_id', $storeID)
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::now()->endOfDay()->format('Y-m-d H:i:s'),
            ])
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with('product.images')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->get();

        // This is ChatGPT generated code, for getting peak hours of a specific date, 
        // please DO NOT edit partially
        $sales = DB::table('sales')
            ->selectRaw('
                FLOOR(HOUR(created_at) / 3) AS period,
                COUNT(*) AS transactions,
                SUM(total_quantity) AS quantity,
                SUM(total_price) AS revenue
            ')
            ->where('store_id', $storeID)
            ->whereBetween('created_at', [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
            ])
            ->groupByRaw('FLOOR(HOUR(created_at) / 3)')
            ->orderBy('period')
            ->get();

        $hours = collect(range(0, 7))
            ->map(function (int $period) use ($sales) {
                $sale = $sales->firstWhere('period', $period);

                $startHour = $period * 3;
                $endHour = $startHour + 3;

                $time = sprintf(
                    '%02d:00-%02d:00',
                    $startHour,
                    $endHour === 24 ? 0 : $endHour
                );

                return [
                    'period' => $period,
                    'time' => $time,
                    'transactions' => (int) ($sale?->transactions ?? 0),
                    'quantity' => (int) ($sale?->quantity ?? 0),
                    'revenue' => (int) ($sale?->revenue ?? 0),
                ];
            });

        return response()->json([
            'user' => $user,
            'revenue' => (int) $revenue,
            'highest_volume' => $highestVolume,
            'hours' => $hours,
            'contents' => $contents,
        ]);
    }
    public function homeSales(Request $request) {
        $user = $request->user();
        $storeID = $user->access->store_id;

        $sales = Sales::where([
            ['store_id', $storeID]
        ])
        ->whereBetween('created_at', [
            Carbon::now()->startOfDay()->format('Y-m-d H:i:s'),
            Carbon::now()->endOfDay()->format('Y-m-d H:i:s'),
        ])
        ->orderBy('created_at', 'DESC')
        ->with(['customer', 'items.product.images', 'user'])
        ->paginate(15);

        return response()->json([
            'sales' => $sales,
        ]);
    }
    public function homeCustomer(Request $request) {
        $user = $request->user();
        $storeID = $user->access->store_id;
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        $customers = Customer::where('store_id', $storeID)
            ->whereHas('sales', function ($query) use ($storeID, $startOfDay, $endOfDay) {
                $query->where('store_id', $storeID)
                    ->whereBetween('created_at', [$startOfDay, $endOfDay]);
            })
            ->with([
                'sales' => function ($query) use ($storeID, $startOfDay, $endOfDay) {
                    $query->where('store_id', $storeID)
                        ->whereBetween('created_at', [$startOfDay, $endOfDay])
                        ->latest('created_at')
                        ->limit(1);
                }
            ])
            ->paginate(10);

        return response()->json([
            'customers' => $customers,
        ]);
    }

    public function plan() {
        return response()->json(config('plans'));
    }
}
