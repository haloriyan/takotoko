@extends('layouts.admin')

@section('title', 'Tambah User')

@section('subtitle')
    <div class="text-xs text-slate-500">Master Data &gt; Users &gt; Tambah</div>
@endsection

@section('content')
<div class="p-6 max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-6">Form Tambah User</h2>

        <form action="{{ route('admin.master.users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border @error('name') border-red-500 @else border-slate-200 @enderror rounded-lg text-sm focus:outline-none focus:border-primary">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border @error('email') border-red-500 @else border-slate-200 @enderror rounded-lg text-sm focus:outline-none focus:border-primary">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required class="w-full px-4 py-2 border @error('password') border-red-500 @else border-slate-200 @enderror rounded-lg text-sm focus:outline-none focus:border-primary">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('admin.master.users.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
