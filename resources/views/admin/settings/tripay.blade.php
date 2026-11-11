@extends('layouts.admin')

@section('title', "Tripay Settings")
    
@php
    $mode = env('TRIPAY_MODE');
    $isLive = $mode == "live";
@endphp

@section('content')
<form action="#" class="p-10 mobile:p-6 flex flex-col gap-6 bg-white rounded-lg shadow m-8" method="POST">
    @csrf

    <div class="flex items-center gap-4">
        <div class="text-sm text-slate-500 flex grow">Mode</div>
        <div class="bg-slate-200 rounded-lg p-2 flex items-center">
            <a href="{{ route('admin.settings.tripay.mode', ['LIVE']) }}" class="p-2 px-5 rounded-lg text-xs {{ $isLive ? 'bg-white font-medium text-primary' : 'text-slate-700' }}">
                LIVE
            </a>
            <a href="{{ route('admin.settings.tripay.mode', ['SANDBOX']) }}" class="p-2 px-5 rounded-lg text-xs {{ !$isLive ? 'bg-white font-medium text-primary' : 'text-slate-700' }}">
                SANDBOX
            </a>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div class="text-sm text-slate-500">Merchant Code</div>
        <div class="flex grow"></div>
        <input type="text" name="TRIPAY_MERCHANT_CODE" class="h-12 rounded-lg border outline-none text-sm text-slate-700 px-4" value="{{ env('TRIPAY_MERCHANT_CODE') }}" required>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-slate-500 flex grow">API Key</div>
        <input type="text" name="TRIPAY_API_KEY" class="h-12 rounded-lg border outline-none text-sm text-slate-700 px-4 flex grow" value="{{ env('TRIPAY_API_KEY') }}" required>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-slate-500 flex grow">Private Key</div>
        <input type="text" name="TRIPAY_PRIVATE_KEY" class="h-12 rounded-lg border outline-none text-sm text-slate-700 px-4 flex grow" value="{{ env('TRIPAY_PRIVATE_KEY') }}" required>
    </div>

    <div class="flex justify-end">
        <button class="p-3 px-4 rounded-lg bg-green-500 text-white text-sm font-medium">
            Simpan
        </button>
    </div>
</form>
@endsection