@extends('layouts.admin')

@section('title', "Users")
    
@section('content')
<div class="p-8 flex flex-col gap-4">
    @foreach ($users as $user)
        <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
            <div class="w-12 h-12 font-bold text-white rounded-lg flex items-center justify-center bg-[{{ $user->color }}]">
                {{ initial($user->name) }}
            </div>
            <div class="flex flex-col gap-[2px] basis-32 grow">
                <div class="text-slate-800 font-medium">{{ $user->name }}</div>
                <div class="text-slate-500 text-xs">{{ $user->email }}</div>
            </div>
            <div class="flex flex-col gap-[2px] basis-32 grow">
                <ion-icon name="storefront-outline" class="text-sm text-slate-500"></ion-icon>
                <div></div>
                @foreach ($user->accesses as $access)
                    <div class="flex items-center gap-1 text-xs">
                        <div class="text-slate-500 italic">{{ strtoupper($access->role) }}</div>
                        <a href="{{ route('admin.master.stores.detail', $access->store->username) }}" class="text-primary font-medium">{{ $access->store->name }}</a>
                    </div>
                @endforeach
            </div>
            <div class="flex flex-col gap-1 basis-32 grow">
                <ion-icon name="time-outline" class="text-sm text-slate-500"></ion-icon>
                <div class="text-sm text-slate-700 font-medium">
                    {{ $user->created_at }}
                </div>
            </div>
            <button class="w-10 h-10 bg-primary text-white flex items-center justify-center rounded-lg">
                <ion-icon name="eye-outline"></ion-icon>
            </button>
        </div>
    @endforeach
</div>
@endsection