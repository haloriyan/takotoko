<?php

namespace App\Http\Controllers;

use App\Models\CmsCategory;
use App\Models\GeminiKey;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use ZipArchive;

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

    public function geminiKeys() {
        $keys = GeminiKey::orderBy('created_at', 'DESC')->get();
        $message = Session::get('message');

        return view('admin.settings.geminiKeys.index', [
            'keys' => $keys,
            'message' => $message,
        ]);
    }

    public function backupSettings(Request $request) {
        if ($request->method() == "GET") {
            return view('admin.settings.backup', [
                'message' => Session::get('message'),
            ]);
        }

        // action=backup will trigger backup creation
        if ($request->has('action') && $request->action == 'backup') {
            $tablesRaw = DB::select('SHOW TABLES');
            $tables = [];
            foreach ($tablesRaw as $row) {
                $rowArr = (array)$row;
                $tables[] = array_values($rowArr)[0];
            }

            $tmpDir = storage_path('app/backups/tmp_' . uniqid());
            File::makeDirectory($tmpDir, 0755, true);

            foreach ($tables as $table) {
                $rows = DB::table($table)->get()->map(function ($r) { return (array)$r; })->toArray();
                File::put($tmpDir . '/' . $table . '.json', json_encode($rows));
            }

            $zipName = 'backup_' . Carbon::now()->format('YmdHis') . '.zip';
            $zipPath = storage_path('app/backups/' . $zipName);

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
                // add json dumps
                $files = File::files($tmpDir);
                foreach ($files as $f) {
                    $zip->addFile($f->getPathname(), $f->getFilename());
                }

                // include public/storage directory (if exists)
                $publicStorage = public_path('storage');
                if (File::exists($publicStorage)) {
                    $all = File::allFiles($publicStorage);
                    foreach ($all as $f) {
                        $abs = $f->getPathname();
                        $relative = ltrim(str_replace($publicStorage, '', $abs), DIRECTORY_SEPARATOR);
                        $zip->addFile($abs, 'public_storage/' . $relative);
                    }

                    // also add empty directories under public/storage
                    $dirs = File::allDirectories($publicStorage);
                    foreach ($dirs as $d) {
                        $relDir = ltrim(str_replace($publicStorage, '', $d), DIRECTORY_SEPARATOR);
                        if ($relDir != '') {
                            $zip->addEmptyDir('public_storage/' . $relDir);
                        }
                    }
                }

                $zip->close();
            }

            File::deleteDirectory($tmpDir);

            // Return the ZIP as a download and delete it after sending
            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        }

        return redirect()->back();
    }

    public function restoreSettings(Request $request) {
        if (!$request->hasFile('restore_file')) {
            return redirect()->back()->withErrors(['Tidak ada file yang diupload']);
        }

        $file = $request->file('restore_file');
        $name = 'restore_upload_' . uniqid() . '.zip';
        $stored = null;
        $zipPath = storage_path('app/backups/' . $name);

        // Try storing the file and record results. Provide multiple fallbacks.
        $attempts = [];

        // 1) default storeAs
        Log::info("1. Handling uploaded zip");
        try {
            $stored = $file->storeAs('backups', $name);
            $attempts['storeAs'] = $stored;
        } catch (\Exception $e) {
            $attempts['storeAs_exception'] = $e->getMessage();
            Log::info($e->getMessage());
        }

        // 2) try move()
        Log::info("2. Moving");
        if (!File::exists($zipPath) || !is_readable($zipPath)) {
            try {
                $moved = $file->move(storage_path('app/backups'), $name);
                $attempts['move'] = $moved ? 'ok' : 'failed';
            } catch (\Exception $e) {
                $attempts['move_exception'] = $e->getMessage();
                Log::info($e->getMessage());
            }
        }

        // 3) try copy via real path
        Log::info("3. Copy via real path");
        if ((!File::exists($zipPath) || !is_readable($zipPath)) && $file->getRealPath()) {
            try {
                File::copy($file->getRealPath(), $zipPath);
                $attempts['copy_realpath'] = 'ok';
            } catch (\Exception $e) {
                $attempts['copy_realpath_exception'] = $e->getMessage();
                Log::info($e->getMessage());
            }
        }

        // 4) try stream copy as last resort
        Log::info("4. Stream copy as last resort");
        if (!File::exists($zipPath) || !is_readable($zipPath)) {
            try {
                $streamIn = $file->openFile('r');
                $out = fopen($zipPath, 'wb');
                while (!$streamIn->eof()) {
                    fwrite($out, $streamIn->fgets());
                }
                fclose($out);
                $attempts['stream_copy'] = 'ok';
            } catch (\Exception $e) {
                $attempts['stream_copy_exception'] = $e->getMessage();
                Log::info($e->getMessage());
            }
        }

        // Diagnostic logging
        Log::info("5. Diagnostic logging");
        try {
            $size = File::exists($zipPath) ? File::size($zipPath) : null;
        } catch (\Exception $e) {
            $size = null;
            Log::info($e->getMessage());
        }
        
        $tmpDir = storage_path('app/backups/restore_' . uniqid());
        File::makeDirectory($tmpDir, 0755, true);

        // basic file checks
        if (!File::exists($zipPath) || File::size($zipPath) === 0) {
            File::delete($zipPath);
            File::deleteDirectory($tmpDir);
            return redirect()->back()->withErrors(['File zip tidak valid atau kosong']);
        }

        $zip = new ZipArchive();
        $res = $zip->open($zipPath);
        if ($res === true) {
            Log::info("Extracting " . $zipPath);
            $zip->extractTo($tmpDir);
            $zip->close();
        } else {
            Log::info("Deleting " . $zipPath);
            Log::info("Deleting " . $tmpDir);
            File::delete($zipPath);
            File::deleteDirectory($tmpDir);
            return redirect()->back()->withErrors(['File zip tidak valid (kode: ' . $res . ')']);
        }

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $files = File::files($tmpDir);
        foreach ($files as $f) {
            $table = pathinfo($f->getFilename(), PATHINFO_FILENAME);
            $content = File::get($f->getPathname());
            $rows = json_decode($content, true);
            if (is_array($rows)) {
                DB::table($table)->truncate();
                if (count($rows) > 0) {
                    $chunks = array_chunk($rows, 1000);
                    foreach ($chunks as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Restore public/storage files
        $backupStorage = $tmpDir . '/public_storage';
        $publicStorage = public_path('storage');

        if (File::exists($backupStorage)) {
            // Ensure destination exists
            File::ensureDirectoryExists($publicStorage);

            // Copy all files and directories recursively
            File::copyDirectory($backupStorage, $publicStorage);

            Log::info("Public storage restored successfully");
        }

        Log::info("Deleting files and temps");
        File::delete($zipPath);
        File::deleteDirectory($tmpDir);

        return redirect()->back()->with([
            'message' => 'Restore selesai',
        ]);
    }

    // Restore directly from an existing file in storage/app/backups
    public function restoreFromStorage(Request $request) {
        $filename = $request->input('filename');
        if (!$filename) {
            return redirect()->back()->withErrors(['Tidak ada nama file diberikan']);
        }

        $zipPath = storage_path('app/backups/' . $filename);
        if (!File::exists($zipPath)) {
            return redirect()->back()->withErrors(['File tidak ditemukan di storage/backups']);
        }

        $tmpDir = storage_path('app/backups/restore_' . uniqid());
        File::makeDirectory($tmpDir, 0755, true);

        $zip = new ZipArchive();
        $res = $zip->open($zipPath);
        if ($res === true) {
            $zip->extractTo($tmpDir);
            $zip->close();
        } else {
            File::deleteDirectory($tmpDir);
            return redirect()->back()->withErrors(['File zip tidak dapat dibuka (kode: ' . $res . ')']);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $files = File::files($tmpDir);
        foreach ($files as $f) {
            $table = pathinfo($f->getFilename(), PATHINFO_FILENAME);
            $content = File::get($f->getPathname());
            $rows = json_decode($content, true);
            if (is_array($rows)) {
                DB::table($table)->truncate();
                if (count($rows) > 0) {
                    $chunks = array_chunk($rows, 1000);
                    foreach ($chunks as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                }
            }
        }

        DB::statement('SET_FOREIGN_KEY_CHECKS=1');

        File::deleteDirectory($tmpDir);

        return redirect()->back()->with(['message' => 'Restore from storage selesai']);
    }

    // Restore by accepting raw binary stream (application/octet-stream) in request body
    // Use curl: curl --data-binary @backup.zip "http://host/admin/settings/restore-stream?filename=backup.zip"
    public function restoreFromStream(Request $request) {
        $filename = $request->query('filename') ?? $request->header('X-Filename');
        if (!$filename) {
            return response()->json(['error' => 'filename required'], 400);
        }

        $zipPath = storage_path('app/backups/' . basename($filename));

        // Read raw body
        $content = $request->getContent();

        if ($content === false || strlen($content) === 0) {
            Log::error('restoreFromStream: empty body');
            return response()->json(['error' => 'empty body'], 400);
        }

        try {
            File::put($zipPath, $content);
        } catch (\Exception $e) {
            Log::error('restoreFromStream: write failed', ['err' => $e->getMessage()]);
            return response()->json(['error' => 'failed to write file'], 500);
        }

        Log::info('restoreFromStream saved', ['path' => $zipPath, 'size' => File::size($zipPath)]);

        // reuse restoreFromStorage logic by creating a fake request
        $fake = new Request();
        $fake->replace(['filename' => basename($filename)]);

        return $this->restoreFromStorage($fake);
    }

    public function tripaySettings(Request $request) {
        if ($request->method() == "GET") {
            $message = Session::get('message');
            return view('admin.settings.tripay', [
                'message' => $message,
            ]);
        } else {
            $mode = env('TRIPAY_MODE');
            $keysToChange = [
                'TRIPAY_MERCHANT_CODE',
                'TRIPAY_API_KEY',
                'TRIPAY_PRIVATE_KEY'
            ];

            foreach ($keysToChange as $key) {
                changeEnv($key, $request->{$key});
            }

            return redirect()->back()->with([
                'message' => "Berhasil menyimpan pengaturan Tripay"
            ]);
        }
    }
    public function tripayMode($mode = null) {
        if ($mode == null) {
            return response()->json([
                'mode' => env('TRIPAY_MODE')
            ]);
        } else {
            changeEnv('TRIPAY_MODE', strtolower($mode));
        
            return redirect()->back()->with([
                'message' => "Berhasil mengubah mode Tripay"
            ]);
        }
    }
}
