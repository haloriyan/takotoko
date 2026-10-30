<?php

namespace App\Http\Controllers;

use App\Models\GeminiKey;
use Illuminate\Http\Request;

class GeminiKeyController extends Controller
{
    public function store(Request $request) {
        $key = GeminiKey::create([
            'label' => $request->label,
            'value' => $request->value,
        ]);

        return redirect()->back()->with([
            'message' => "Berhasil menambahkan API Key Gemini ({$request->label})"
        ]);
    }
    public function delete($id) {
        $k = GeminiKey::where('id', $id);
        $key = $k->first();

        $k->delete();
        
        return redirect()->back()->with([
            'message' => "Berhasil menghapus API Key ({$key->label})"
        ]);
    }
}
