@extends('layouts.admin')

@section('title', "Stores")
    
@section('content')
<div class="p-8 flex flex-col gap-8">
    <div class="flex items-center gap-2">
        <div class="p-2 px-5 rounded-full cursor-pointer text-sm border {{ $request->paket == '' ? 'bg-primary border-primary text-white font-medium' : 'bg-white' }}" onclick="addFilter('paket', null)">
            Semua
        </div>
        <div class="p-2 px-5 rounded-full cursor-pointer text-sm border {{ $request->paket == 'basic' ? 'bg-primary border-primary text-white font-medium' : 'bg-white' }}" onclick="addFilter('paket', 'basic')">
            Basic ({{ $basics->count() }})
        </div>
        <div class="p-2 px-5 rounded-full cursor-pointer text-sm border {{ $request->paket == 'starter' ? 'bg-primary border-primary text-white font-medium' : 'bg-white' }}" onclick="addFilter('paket', 'starter')">
            Starter ({{ $starters->count() }})
        </div>
        <div class="p-2 px-5 rounded-full cursor-pointer text-sm border {{ $request->paket == 'pro' ? 'bg-primary border-primary text-white font-medium' : 'bg-white' }}" onclick="addFilter('paket', 'pro')">
            PRO ({{ $pros->count() }})
        </div>
        <div class="flex grow"></div>
        <form class="bg-white rounded-full flex items-center px-4 gap-3 basis-96 shadow">
            <ion-icon name="search-outline" class="text-lg"></ion-icon>
            <input type="text" name="q" class="flex grow h-12 outline-none text-sm" placeholder="Cari nama toko" value="{{ $request->q }}">
            @if ($request->q != "")
                <div class="cursor-pointer flex items-center" onclick="addFilter('q', null)">
                    <ion-icon name="close-outline" class="text-lg text-red-500"></ion-icon>
                </div>
            @endif
        </form>
    </div>

    <div class="flex flex-col gap-3">
        @foreach ($stores as $store)
            @php
                if ($store->package == 'basic') {
                    $paketTextColor = "text-slate-600";
                    $paketBgColor = "bg-slate-100";
                } else if ($store->package == 'starter') {
                    $paketTextColor = "text-white";
                    $paketBgColor = "bg-green-500";
                } else if ($store->package == 'pro') {
                    $paketTextColor = "text-white";
                    $paketBgColor = "bg-primary";
                }
            @endphp
            <a href="{{ route('admin.master.stores.detail', $store->username) }}" class="bg-white rounded-lg p-4 shadow flex items-center gap-4">
                <div class="w-14 h-14 flex items-center justify-center text-sm font-bold rounded-lg bg-slate-100">
                    @if ($store->icon == null)
                        <ion-icon name="storefront-outline" class="text-lg"></ion-icon>
                    @else
                        <img 
                            src="{{ asset('storage/store_icons/' . $store->icon) }}" 
                            alt="{{ $store->name }}"
                            class="w-14 h-14 object-cover rounded-lg"
                        >
                    @endif
                </div>
                <div class="flex flex-col gap-1 basis-32 grow">
                    <div class="text-sm text-slate-800 font-medium">{{ $store->name }}</div>
                    <div class="text-xs text-slate-500">{{ "@".$store->username }}</div>
                </div>
                <div class="flex flex-col gap-1 basis-32 grow">
                    <div class="text-xs text-slate-500">Paket</div>
                    <div class="flex">
                        <div class="text-xs font-medium p-1 px-3 rounded font-medium {{ $paketBgColor }} {{ $paketTextColor }}">{{ strtoupper($store->package) }}</div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{ $stores->links() }}
</div>
@endsection