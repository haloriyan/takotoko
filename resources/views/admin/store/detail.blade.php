@extends('layouts.admin')

@section('title', $store->name)

@php
    use Carbon\Carbon;

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

@section('content')
<div class="p-8 flex flex-col">
    <a href="{{ route('admin.master.stores') }}" class="flex items-center gap-2">
        <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
        <div class="text-xs text-slate-700">Kembali</div>
    </a>

    <div class="flex mt-4">
        @include('admin.store.tab')
    </div>

    <div class="bg-white rounded-lg p-8 shadow mt-4">
        <div class="flex items-center gap-4">
            <div class="w-24 h-24 rounded-lg flex items-center justify-center rounded-lg">
                @if ($store->icon == null)
                    <ion-icon name="storefront-outline" class="text-lg text-slate-700"></ion-icon>
                @else
                    <img 
                        src="{{ asset('storage/store_icons/' . $store->icon) }}" 
                        alt="{{ $store->name }}"
                        class="w-24 h-24 object-cover rounded-lg"
                    >
                @endif
            </div>
            <div class="flex flex-col gap-[2px] basis-32 grow">
                <div class="text-slate-700 font-medium text-lg">{{ $store->name }}</div>
                <div class="text-slate-500 text-xs">{{ "@".$store->username }}</div>
                <div class="flex mt-4">
                    <div class="text-xs font-medium p-1 px-3 rounded font-medium {{ $paketBgColor }} {{ $paketTextColor }}">{{ strtoupper($store->package) }}</div>
                </div>
            </div>
            <div class="flex flex-col gap-2 basis-32 grow">
                <div class="flex items-center gap-2">
                    <ion-icon name="people-outline" class="text-primary text-lg"></ion-icon>
                    <div class="text-xs text-slate-500">Jumlah Karyawan</div>
                    <div class="text-sm text-slate-800 font-medium">{{ $store->accesses->count() }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <ion-icon name="cube-outline" class="text-primary text-lg"></ion-icon>
                    <div class="text-xs text-slate-500">Produk</div>
                    <div class="text-sm text-slate-800 font-medium">{{ $productsCount }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <ion-icon name="cart-outline" class="text-primary text-lg"></ion-icon>
                    <div class="text-xs text-slate-500">Transaksi</div>
                    <div class="text-sm text-slate-800 font-medium">{{ $salesCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-8 mt-8">
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 px-8 border-b font-medium text-slate-800 flex items-center gap-4">
                <ion-icon name="cube-outline" class="text-lg"></ion-icon>
                Produk
            </div>
            <div class="p-4 px-8 flex flex-col gap-4">
                @foreach ($products as $product)
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-lg bg-slate-100 border flex items-center justify-center">
                            @if ($product->images->count() == 0)
                                <ion-icon name="image-outline" class="text-lg"></ion-icon>
                            @else
                                <img 
                                    src="/storage/{{ $product->store_id }}/product_images/{{ $product->id }}/{{ $product->images[0]->filename }}"
                                    class="w-14 h-14 rounded-lg object-cover"
                                >
                            @endif
                        </div>
                        <div class="flex flex-col gap-1 basis-32 grow">
                            <div class="text-sm text-slate-800 font-medium">{{ $product->name }}</div>
                            <div class="text-xs text-primary">{{ currency_encode($product->price) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 px-8 border-b font-medium text-slate-800 flex items-center gap-4">
                <ion-icon name="cart-outline" class="text-lg"></ion-icon>
                Penjualan
            </div>
            <div class="p-4 px-8 flex flex-col gap-4">
                @foreach ($sales as $sale)
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col gap-2 basis-32 grow">
                            <div class="text-sm text-slate-800 font-medium">{{ $sale->invoice_number }}</div>
                            <div class="flex items-center gap-2 text-slate-500">
                                <ion-icon name="cash-outline"></ion-icon>
                                <div class="text-xs">{{ currency_encode($sale->total_pay) }}</div>
                            </div>
                        </div>
                        <div class="text-xs text-slate-500">
                            {{ Carbon::parse($sale->created_at)->isoFormat('DD MMM YYYY, HH:mm') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection