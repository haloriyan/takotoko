@extends('layouts.admin')

@section('title', "Karyawan " . $store->name)

@php
    $roleColors = [
        'owner' => "bg-primary",
        'cashier' => "bg-green-500",
        'stockist' => "bg-orange-500"
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
        <form class="bg-white rounded-full flex items-center px-4 gap-3 basis-96 shadow">
            <button class="flex items-center">
                <ion-icon name="search-outline" class="text-lg"></ion-icon>
            </button>
            <input type="text" name="q" class="flex grow h-12 outline-none text-sm" placeholder="Cari nama" value="{{ $request->q }}">
            @if ($request->q != "")
                <div class="cursor-pointer flex items-center" onclick="addFilter('q', null)">
                    <ion-icon name="close-outline" class="text-lg text-red-500"></ion-icon>
                </div>
            @endif
        </form>
    </div>
    
    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-lg border border-default mt-8 bg-white">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-white border-b rounded-lg border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Access ID
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Nama
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Role
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                    <tr class="bg-neutral-primary border-b border-default">
                        <td class="px-6 py-4">
                            {{ $employee->id }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $employee->user->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $employee->user->email }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex">
                                <div class="p-1 px-3 rounded text-xs text-white font-bold {{ $roleColors[$employee->role] }}">
                                    {{ strtoupper($employee->role) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection