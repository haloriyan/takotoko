@extends('layouts.admin')

@section('title', "Gemini Keys")

@php
    use Carbon\Carbon;
@endphp
    
@section('content')
<div class="m-8 p-8 bg-white rounded-lg shadow flex flex-col gap-8">
    <div class="flex items-center gap-4">
        <h2 class="text-xl text-slate-800 font-medium">Gemini Keys</h2>
        <div class="flex grow"></div>
        <button class="border border-slate-300 rounded-lg text-sm text-slate-800 font-medium p-2 px-4" onclick="toggleHidden('#AddKey')">
            Tambahkan
        </button>
    </div>

    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="border-b p-2 text-sm font-medium">Label</th>
                <th class="border-b p-2 text-sm font-medium">Key</th>
                <th class="border-b p-2 text-sm font-medium">Created</th>
                <th class="border-b p-2 text-sm font-medium"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($keys as $key)
                <tr class="text-slate-600">
                    <td class="p-2 text-sm">{{ $key->label }}</td>
                    <td class="p-2 text-sm flex items-center gap-3">
                        {{ $key->value }}
                        <span class="flex items-center cursor-pointer" onclick="CopyText('{{ $key->value }}', this)">
                            <ion-icon name="copy-outline"></ion-icon>
                        </span>
                    </td>
                    <td class="p-2 text-sm">{{ Carbon::parse($key->created_at)->isoFormat('DD MMM, HH:mm') }}</td>
                    <td class="p-2 text-sm flex">
                        <a href="{{ route('admin.settings.geminiKeys.delete', $key->id) }}" data-key="{{ $key }}" class="p-1 px-3 rounded-lg border-[0.5px] border-red-500 text-red-500 hover:bg-red-500 hover:text-white" onclick="DeleteKey(this, event)">
                            <ion-icon name="trash-outline"></ion-icon>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('ModalArea')
@include('admin.settings.geminiKeys.add')
@include('admin.settings.geminiKeys.delete')
@endsection

@section('javascript')
<script>
    const DeleteKey = (btn, e) => {
        e.preventDefault();
        let link = btn.href;
        let key = JSON.parse(btn.dataset.key);

        select("#DeleteKey form").setAttribute('action', link);
        select(`#DeleteKey #label`).innerHTML = key.label;
        toggleHidden("#DeleteKey");
    }
    const CopyText = (txt, btn) => {
        let ogBtn = btn.innerHTML;

        navigator.clipboard.writeText(txt);
        btn.innerHTML = "<ion-icon name='checkmark-outline' class='text-green-500 text-lg'></ion-icon> Disalin!";
        setTimeout(() => {
            btn.innerHTML = ogBtn;
        }, 1800);
    }
</script>
@endsection