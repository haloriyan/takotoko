<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleUser;
use App\Models\UserStore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function index(Request $request, $date) {
        $user = $request->user();
        $storeID = $user->access->store_id;
        $store = $user->access->store;
        $plan = config('plans')[$store->package];
        $canAbsen = $plan['absensi'];

        $schedules = Schedule::where([
            ['store_id', $storeID],
            ['date', $date]
        ])
        ->with(['users.user'])
        ->get();

        foreach ($schedules as $s => $sched) {
            $inCount = 0;
            $outCount = 0;
            $inDiffSum = 0;

            foreach ($sched->users as $u => $user) {
                if ($user->check_in_at != null) {
                    $inCount += 1;
                }
                if ($user->check_out_at != null) {
                    $outCount += 1;
                }

                $inDiffSum += $user->check_in_diff;
            }

            $schedules[$s]->check_in_count = $inCount;
            $schedules[$s]->check_out_count = $outCount;
            $schedules[$s]->avg_in = $inDiffSum / $sched->users->count();
        }

        return response()->json([
            'schedules' => $schedules,
            'can_absen' => $canAbsen,
        ]);
    }
    public function search(Request $request, $date) {
        $user = $request->user();
        $storeID = $user->access->store_id;

        $employees = UserStore::where('store_id', $storeID)
        ->whereNot('user_id', $user->id)
        ->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'LIKE', '%'.$request->q.'%');
        })
        ->with([
            'user'
        ])
        ->get();
        
        return response()->json([
            'employees' => $employees,
        ]);
    }
    public function store(Request $request, $date) {
        $user = $request->user();
        $storeID = $user->access->store_id;

        $inTime = $request->check_in_time;
        $outTime = $request->check_out_time;
        $duration = Carbon::parse($inTime)->diffInMinutes(
            Carbon::parse($outTime)
        );

        $schedule = Schedule::create([
            'store_id' => $storeID,
            'date' => $date,
            'label' => $request->label,
            'check_in_time' => $inTime,
            'check_out_time' => $outTime,
            'duration' => $duration,
        ]);

        foreach ($request->user_ids as $userID) {
            ScheduleUser::create([
                'schedule_id' => $schedule->id,
                'user_id' => $userID,
                'store_id' => $storeID,
                'date' => $date,
            ]);
        }

        return response()->json([
            'message' => "Berhasil membuat jadwal",
            'schedule' => $schedule,
        ]);
    }
    public function check(Request $request, $date, $directFuncCall = false) {
        $user = $request->user();
        $storeID = $user->access->store_id;
        $store = $user->access->store;

        $query = ScheduleUser::where([
            ['user_id', $user->id],
            ['date', $date],
            ['store_id', $storeID],
        ]);
        $schedule = $query
        ->first();

        $response = [
            'has_checked_in' => @$schedule->check_in_at != null ?? false,
            'has_checked_out' => @$schedule->check_out_at != null ?? false,
            'user' => $user,
            'store' => $store,
            'store_id' => $storeID,
        ];

        if ($directFuncCall) {
            $response['query'] = $query;
            return $response;
        }

        return response()->json($response);
    }
    function distance($coords)
    {
        $earthRadius = 6371000; // meters

        $lat1 = $coords['from'][0];
        $lon1 = $coords['from'][1];
        $lat2 = $coords['to'][0];
        $lon2 = $coords['to'][1];

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1))
            * cos(deg2rad($lat2))
            * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
    public function present(Request $request, $date) {
        $cek = $this->check($request, $date, true);
        $store = $cek['store'];
        $image = $request->file('image');
        $imageFileName = $image->getClientOriginalName();
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $address = $request->address;
        $dist = $this->distance([
            'from' => [$store->latitude, $longitude],
            'to' => [$latitude, $longitude]
        ]);

        if ($dist >= $store->max_radius_distance) {
            return response()->json([
                'message' => 'Presensi gagal. Lokasi terlalu jauh.',
                'distance' => $dist,
            ], 422);
        }

        $data = $cek['query']->with([
            'schedule'
        ])->first();
        $schedule = @$data->schedule ?? null;

        $toUpdate = [];
        
        if (!$cek['has_checked_in']) {
            $mode = "in";
        } else if (!$cek['has_checked_out']) {
            $mode = "out";
        }

        $diff = Carbon::now()->diffInMinutes(
            Carbon::parse($schedule->{"check_{$mode}_time"})
        );

        $toUpdate["check_{$mode}_at"] = Carbon::now()->format('Y-m-d H:i:s');
        $toUpdate["check_{$mode}_latitude"] = $latitude;
        $toUpdate["check_{$mode}_longitude"] = $longitude;
        $toUpdate["check_{$mode}_address"] = $address;
        $toUpdate["check_{$mode}_image"] = $imageFileName;
        $toUpdate["check_{$mode}_diff"] = floor($diff);

        if ($mode == "out") {
            $toUpdate['in_out_diff'] = Carbon::parse($data->check_in_at)->diffInMinutes(
                Carbon::parse($data->check_out_at)
            );
        }

        $cek['query']->update($toUpdate);
        $image->move(
            public_path("storage/presence_photos/{$schedule->store_id}/"), $imageFileName
        );

        return response()->json([
            'message' => "Berhasil " . $mode == 'in' ? "masuk" : "pulang",
        ]);
    }
}
