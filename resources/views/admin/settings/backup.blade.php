@extends('layouts.admin')

@section('title', "Pengaturan Backup")
    
@section('content')
<div class="p-8 mobile:p-4">
    <form action="{{ route('admin.settings.backup') }}" method="POST" class="p-8 mobile:p-6 flex flex-col gap-6 bg-white rounded-lg shadow m-8">
        @csrf

        <div class="text-sm text-slate-700">Fitur ini akan membuat backup seluruh tabel database ke dalam file ZIP berisi JSON per tabel.</div>
        <div class="text-xs text-rose-500">Perhatian: foreign key checks akan dinonaktifkan saat restore.</div>

        <div class="flex justify-end">
            <button name="action" value="backup" class="p-3 px-5 rounded-lg bg-green-500 text-white text-sm font-medium">Buat Backup</button>
        </div>
    </form>

    <form action="{{ route('admin.settings.restore') }}" method="POST" enctype="multipart/form-data" class="p-8 mobile:p-6 flex flex-col gap-6 bg-white rounded-lg shadow m-8">
        @csrf

        <div class="text-sm text-slate-700">Upload file backup (ZIP) untuk merestore data.</div>
        <input type="file" name="restore_file" accept=".zip" required />

        <div class="flex justify-end">
            <button class="p-3 px-5 rounded-lg bg-yellow-500 text-white text-sm font-medium">Restore</button>
        </div>
    </form>
    <div class="p-8 mobile:p-6 flex flex-col gap-6 bg-white rounded-lg shadow m-8">
        <div class="text-sm text-slate-700">Restore dari file yang sudah ada di server (storage/app/backups)</div>
        @php
            $backups = [];
            try {
                $files = \Illuminate\Support\Facades\File::exists(storage_path('app/backups')) ? \Illuminate\Support\Facades\File::files(storage_path('app/backups')) : [];
                foreach ($files as $f) {
                    $backups[] = $f->getFilename();
                }
            } catch (Exception $e) {
                $backups = [];
            }
        @endphp

        <form action="{{ route('admin.settings.restore.file') }}" method="POST" class="flex gap-4 items-center">
            @csrf
            <select name="filename" class="border rounded px-3 py-2">
                @foreach($backups as $b)
                    <option value="{{ $b }}">{{ $b }}</option>
                @endforeach
            </select>
            <button class="p-2 px-4 rounded bg-red-500 text-white">Restore From Storage</button>
        </form>
    </div>
</div>
@endsection
