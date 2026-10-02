@extends('layouts.admin')

@section('title', "Membership " . $store->name)

@php
    use Carbon\Carbon;

    $colors = [
        'basic' => [
            'text' => "text-slate-600",
            'bg' => "bg-slate-100",
        ],
        'starter' => [
            'text' => "text-white",
            'bg' => "bg-green-500"
        ],
        'pro' => [
            'text' => "text-white",
            'bg' => "bg-primary"
        ],
        'paid' => [
            'text' => "text-green-500",
            'bg' => "bg-green-100",
        ],
        'pending' => [
            'text' => "text-yellow-500",
            'bg' => "bg-yellow-100",
        ],
        'cancelled' => [
            'text' => "text-red-500",
            'bg' => "bg-red-100",
        ],
    ];
@endphp
    
@section('content')
<div class="p-8 flex flex-col">
    <a href="{{ route('admin.master.stores') }}" class="flex items-center gap-2">
        <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
        <div class="text-xs text-slate-700">Kembali</div>
    </a>

    <div class="flex mt-4">
        @include('admin.store.tab')
        <div class="flex grow"></div>
        <div class="flex flex-col gap-1">
            <div class="text-xs text-slate-800 font-medium">Membership Saat Ini</div>
            <div class="flex">
                <div class="p-1 px-3 rounded-lg text-sm font-bold {{ $colors[$store->package]['bg'] }} {{ $colors[$store->package]['text'] }}">
                    {{ strtoupper($store->package) }}
                </div>
            </div>
        </div>
    </div>

    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-lg border border-default mt-8 bg-white">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-white border-b rounded-lg border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Paket
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Durasi
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Nominal
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Status
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $trx)
                    <tr class="bg-neutral-primary border-b border-default">
                        <td class="px-6 py-4">
                            <div class="flex">
                                <div class="p-1 px-3 rounded-lg text-sm font-bold {{ $colors[$trx->plan]['bg'] }} {{ $colors[$trx->plan]['text'] }}">
                                    {{ strtoupper($trx->plan) }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-800 font-medium">
                                {{ $trx->quantity }} bulan
                            </div>
                            <div class="text-xs text-slate-500">Exp : {{ Carbon::parse($trx->expired_at)->isoFormat('DD MMM YYYY, HH:mm') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            {{ currency_encode($trx->payment_amount) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="p-1 px-3 rounded-lg text-sm font-bold {{ $colors[strtolower($trx->payment_status)]['bg'] }} {{ $colors[strtolower($trx->payment_status)]['text'] }}">
                                    {{ strtoupper($trx->payment_status) }}
                                </div>
                                <a href="{{ route('admin.master.stores.membership.status', [$store->username, $trx->id]) }}" class="flex items-center cursor-pointer" onclick="ChangeStatus(event, '{{ $trx }}')">
                                    <ion-icon name="settings-outline" class="text-lg text-primary"></ion-icon>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('ModalArea')
    
@include('admin.store.membership_status')

@endsection

@section('javascript')
<script>
    const ChangeStatus = (event, data) => {
        event.preventDefault();
        data = JSON.parse(data);
        const link = event.currentTarget;
        
        select("#StatusChanger form").setAttribute('action', link.href);
        select(`#StatusChanger #status option[value=${data.payment_status}]`).selected = true;
        toggleHidden('#StatusChanger');
    }
</script>
@endsection