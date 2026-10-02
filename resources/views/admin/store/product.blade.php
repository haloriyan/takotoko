@extends('layouts.admin')

@section('title', "Produk " . $store->name)
    
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
                        Produk
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="bg-neutral-primary border-b border-default">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-slate-100">
                                    @if ($product->images->count() == 0)
                                        <ion-icon name="image-outline" class="text-lg text-slate-800"></ion-icon>
                                    @else
                                        <img 
                                            src="/storage/{{ $product->store_id }}/product_images/{{ $product->id }}/{{ $product->images[0]->filename }}" 
                                            alt="{{ $product->name }}"
                                            class="w-12 h-12 rounded-lg object-cover"
                                        >
                                    @endif
                                </div>
                                <div class="flex flex-col gap-1">
                                    <div class="text-sm text-slate-800 font-medium">{{ $product->name }}</div>
                                    <div class="text-xs text-primary">{{ currency_encode($product->price) }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="flex">
                                <a href="{{ route('admin.master.stores.product.delete', [$store->username, $product->id]) }}" class="p-2 px-3 rounded-lg text-xs text-white font-bold bg-red-500 flex items-center" onclick="DeleteProduct(event, '{{ $product }}')">
                                    <ion-icon name="trash-outline" class="text-lg"></ion-icon>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="h-8"></div>
    {{ $products->links() }}
</div>
@endsection

@section('ModalArea')

@include('admin.store.product_delete')

@endsection

@section('javascript')
<script>
    const DeleteProduct = (e, data) => {
        data = JSON.parse(data);
        const link = e.currentTarget.href;
        console.log(link);
        

        toggleHidden('#DeleteProduct');
        select("#DeleteProduct #name").innerHTML = data.name;
        select("#DeleteProduct form").setAttribute('action', link);

        e.preventDefault();
    }
</script>
@endsection