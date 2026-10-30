@extends('layouts.admin')

@section('title', "Stores")
    
@section('content')
<div class="p-8 mobile:p-4 flex flex-col gap-8 mobile:gap-4">
    <div class="flex items-center gap-2 w-full overflow-x-auto whitespace-nowrap scrollbar-hide">
        <div class="p-2 px-5 rounded-full cursor-pointer text-sm mobile:text-xs border {{ $request->paket == '' ? 'bg-primary border-primary text-white font-medium' : 'bg-white' }}" onclick="addFilter('paket', null)">
            Semua
        </div>
        <div class="p-2 px-5 rounded-full cursor-pointer text-sm mobile:text-xs border {{ $request->paket == 'basic' ? 'bg-primary border-primary text-white font-medium' : 'bg-white' }}" onclick="addFilter('paket', 'basic')">
            Basic ({{ $basics->count() }})
        </div>
        <div class="p-2 px-5 rounded-full cursor-pointer text-sm mobile:text-xs border {{ $request->paket == 'starter' ? 'bg-primary border-primary text-white font-medium' : 'bg-white' }}" onclick="addFilter('paket', 'starter')">
            Starter ({{ $starters->count() }})
        </div>
        <div class="p-2 px-5 rounded-full cursor-pointer text-sm mobile:text-xs border {{ $request->paket == 'pro' ? 'bg-primary border-primary text-white font-medium' : 'bg-white' }}" onclick="addFilter('paket', 'pro')">
            PRO ({{ $pros->count() }})
        </div>
        <div class="flex grow"></div>
        <form class="bg-white rounded-full flex items-center px-4 gap-3 basis-96 shadow mobile:hidden">
            <ion-icon name="search-outline" class="text-lg"></ion-icon>
            <input type="text" name="q" class="flex grow h-12 outline-none text-sm" placeholder="Cari nama toko" value="{{ $request->q }}">
            @if ($request->q != "")
                <div class="cursor-pointer flex items-center" onclick="addFilter('q', null)">
                    <ion-icon name="close-outline" class="text-lg text-red-500"></ion-icon>
                </div>
            @endif
        </form>
        <button class="flex items-center gap-2 bg-primary text-white rounded-full p-3 px-5 text-sm font-medium" onclick="toggleHidden('#CreateStore')">
            <ion-icon name="add-outline" class="text-lg"></ion-icon>
            Buat Toko
        </button>
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
            <div class="bg-white rounded-lg p-4 shadow flex items-center gap-4">
                <a href="{{ route('admin.master.stores.detail', $store->username) }}" class="flex items-center gap-4 grow">
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
                <a href="{{ route('admin.master.stores.delete', $store->id) }}" class="bg-red-500 text-white p-2 px-4 rounded-lg" onclick="DeleteStore('{{ $store }}', event)">
                    <ion-icon name="trash-outline"></ion-icon>
                </a>
            </div>
        @endforeach
    </div>

    {{ $stores->links() }}
</div>
@endsection

@section('ModalArea')
<div class="fixed bottom-0 left-0 right-0 p-4 desktop:hidden flex items-center gap-2">
    <form class="flex items-center grow bg-white rounded-full border pe-4">
        <button class="w-12 h-12 rounded-full flex items-center justify-center">
            <ion-icon name="search-outline" class="text-lg"></ion-icon>
        </button>
        <input type="text" name="q" value="{{ $request->q }}" class="flex grow h-12 text-sm outline-none" placeholder="Cari toko">
    </form>
    @if ($request->q != "")
        <div class="w-12 h-12 rounded-full flex items-center justify-center bg-white border rounded" onclick="addFilter('q', null)">
            <ion-icon name="close-outline" class="text-lg text-slate-800"></ion-icon>
        </div>
    @endif
</div>

@include('admin.store.add')
@include('admin.store.delete')
@endsection

@section('javascript')
<script>
    const DeleteStore = (data, e) => {
        e.preventDefault();

        let link = e.currentTarget;
        data = JSON.parse(data);
        select("#DeleteStore #name").innerHTML = data.name;
        select("#DeleteStore form").setAttribute('action', link.href);

        toggleHidden("#DeleteStore");
    }
</script>
@endsection