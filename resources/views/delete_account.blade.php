@extends('layouts.page')

@section('title', "Hapus Akun")
    
@section('content')

<section class="p-20 mobile:p-8 flex justify-center">
    <form class="bg-white rounded-lg p-8 border shadow w-[60%] flex flex-col gap-4" method="POST">
        <h2 class="text-2xl text-slate-800 font-medium">Hapus Akun</h2>
        <div class="text-sm text-slate-600">
            Ajukan permintaan penghapusan akun dan toko Anda dengan mengisi form di bawah ini.
        </div>

        <div></div>

        @csrf
        <div class="flex flex-col gap-2">
            <div class="text-xs text-slate-600">Email</div>
            <input type="email" name="email" class="h-12 w-full border rounded-lg text-sm text-slate-600 outline-none px-4" required>
        </div>

        <div class="flex justify-end">
            <button class="p-3 px-5 rounded-lg bg-primary text-white text-sm font-medium">Ajukan</button>
        </div>

        @if ($message != "")
            <div class="bg-green-100 text-green-500 text-sm font-medium p-4 rounded-lg">
                {{ $message }}
            </div>
        @endif
        @if ($errors->count() > 0)
            @foreach ($errors->all() as $err)
                <div class="bg-red-100 text-red-500 text-sm font-medium p-4 rounded-lg">
                    {{ $err }}
                </div>
            @endforeach
        @endif
    </form>
</section>

@endsection