@extends('layouts.admin')

@section('title', 'User Management')

@section('subtitle')
    <div class="text-xs text-slate-500">Master Data &gt; Users</div>
@endsection

@section('content')
<div class="p-6 space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ion-icon name="checkmark-circle-outline" class="text-lg"></ion-icon>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
    @endif

    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.master.users.index') }}" class="flex items-center gap-2 grow max-w-md">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email user..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-primary bg-white">
                <ion-icon name="search-outline" class="absolute left-3 top-2.5 text-slate-400 text-base"></ion-icon>
            </div>
            <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition">
                Cari
            </button>
            @if($search)
                <a href="{{ route('admin.master.users.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-300 transition">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('admin.master.users.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 transition flex items-center justify-center gap-2">
            <ion-icon name="add-outline" class="text-lg"></ion-icon>
            Tambah User
        </a>
    </div>

    <div class="w-full overflow-x-auto border border-slate-200 rounded-lg bg-white shadow-sm">
        <table class="min-w-full text-left border-collapse text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-3 whitespace-nowrap">#</th>
                    <th class="px-6 py-3 whitespace-nowrap">Nama</th>
                    <th class="px-6 py-3 whitespace-nowrap">Email</th>
                    <th class="px-6 py-3 whitespace-nowrap">Tanggal Dibuat</th>
                    <th class="px-6 py-3 whitespace-nowrap text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900">
                            {{ $users->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-800">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                            {{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="group relative inline-block text-left">
                                <button type="button" class="w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 flex items-center justify-center text-slate-600 transition">
                                    <ion-icon name="ellipsis-vertical" class="text-base"></ion-icon>
                                </button>
                                <div class="hidden group-hover:block absolute right-0 top-full mt-1 w-32 bg-white border border-slate-200 rounded-lg shadow-lg z-30 py-1">
                                    <a href="{{ route('admin.master.users.edit', $user->id) }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 hover:bg-slate-100 transition">
                                        <ion-icon name="create-outline" class="text-sm"></ion-icon>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.master.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs text-red-600 hover:bg-slate-100 transition">
                                            <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                            Tidak ada data user ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pt-2">
        {{ $users->links() }}
    </div>
</div>
@endsection
