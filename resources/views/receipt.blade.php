<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $sales->invoice_number }} - {{ env('APP_NAME') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {!! json_encode(config('tailwind')) !!}
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap');

        div, aside, header { transition: 0.4s; }
        body {
            font-family: "Open Sans", sans-serif;
            font-style: normal;
            font-weight: 400;
        }
    </style>
    @yield('head')
</head>
<body class="bg-slate-100 mobile:bg-white">

@php
    use Carbon\Carbon;
@endphp

<div class="flex justify-center desktop:p-20">
    <div class="bg-white p-8 mobile:p-4 mobile:py-10 rounded-lg desktop:border desktop:shadow w-full desktop:w-[60%]">
        <div class="flex items-center gap-4">
            <div class="flex flex-col gap-2 grow basis-32">
                <h1 class="text-3xl mobile:text-xl text-slate-800 font-bold">{{ $sales->invoice_number }}</h1>
                <div class="flex items-center gap-1">
                    <ion-icon name="calendar-outline"></ion-icon>
                    <div class="text-xs text-slate-500">{{ Carbon::parse($sales->created_at)->isoFormat('DD MMMM YYYY, HH:mm:ss') }}</div>
                </div>
            </div>
            @if ($sales->store->icon == null)
                <div class="w-16 h-16 rounded-full flex items-center justify-center bg-primary">
                    <ion-icon name="storefront-outline" class="text-2xl text-white"></ion-icon>
                </div>
            @else
                <img 
                    src="/storage/store_icons/{{ $sales->store->icon }}" 
                    alt="{{ $sales->store->name }}"
                    class="w-16 h-16 rounded-full object-cover"
                >
            @endif
        </div>

        <div class="grid grid-cols-2 gap-8 mt-8">
            <div class="flex flex-col gap-2">
                <div class="text-sm text-slate-800 font-bold">Pelanggan</div>
                <div class="text-sm text-slate-600">
                    {{ $sales->customer->name }}
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-sm text-slate-800 font-bold">
                    {{ $sales->store->name }}
                </div>
                <div class="text-sm text-slate-600">
                    {{ $sales->store->address ?? "-" }}
                </div>
            </div>
        </div>

        <div class="h-8"></div>

        <table class="w-full text-left">
            <thead>
                <tr>
                    <th class="py-2 border-b">Produk</th>
                    <th class="py-2 border-b">Harga</th>
                    <th class="py-2 border-b">Jumlah</th>
                    <th class="py-2 border-b">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales->items as $item)
                    <tr>
                        <td class="flex items-center gap-4 py-4">
                            <img 
                                src="/storage/product_images/{{ $item->product->id }}/{{ $item->product->images[0]->filename }}" 
                                alt="{{ $item->product->name }}"
                                class="w-12 h-12 rounded-lg object-cover"
                            >
                            <div class="text-slate-700 text-sm font-medium">
                                {{ $item->product->name }}
                            </div>
                        </td>
                        <td class="py-4 text-sm text-slate-700 font-medium">
                            {{ currency_encode($item->price) }}
                        </td>
                        <td class="py-4 text-sm text-slate-700 font-medium">
                            {{ $item->quantity }}
                        </td>
                        <td class="py-4 text-sm text-slate-700 font-medium">
                            {{ currency_encode($item->total_price) }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th class="py-2 border-t" colspan="3">Total</th>
                    <th class="py-2 border-t">
                        {{ currency_encode($sales->total_price) }}
                    </th>
                </tr>
            </tbody>
        </table>

        <div class="flex items-center justify-end mt-8 gap-4" id="ButtonArea">
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
            <button class="p-3 px-5 flex items-center gap-2 rounded-lg text-white font-medium text-sm bg-green-500" onclick="doPrint()">
                <ion-icon name="print-outline" class="text-lg"></ion-icon>
                Cetak
            </button>
            @if ($sales->review == null)
                <button class="p-3 px-5 flex items-center gap-2 rounded-lg text-white font-medium text-sm bg-primary" onclick="toggleHidden('#WriteReview')">
                    <ion-icon name="create-outline" class="text-lg"></ion-icon>
                    Tulis Ulasan
                </button>
            @endif
        </div>
    </div>
</div>

<div class="fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-30" id="WriteReview">
    <form action="{{ route('review.store', $sales->invoice_number) }}" method="POST" class="bg-white shadow-lg rounded-lg p-10 w-4/12 mobile:w-10/12 flex flex-col gap-4 mt-4">
        @csrf
        <div class="flex items-center gap-4 mb-4">
            <h3 class="text-lg text-slate-700 font-medium flex grow">Tulis Ulasan</h3>
            <ion-icon name="close-outline" class="cursor-pointer text-3xl" onclick="toggleHidden('#WriteReview')"></ion-icon>
        </div>

        <input type="hidden" name="rate" id="rate">

        <div class="flex items-center justify-center gap-2" id="ratingStars">
            @for ($i = 1; $i <= 5; $i++)
                <div class="cursor-pointer" data-rating="{{ $i }}">
                    <ion-icon name="star-outline" class="text-2xl text-yellow-500"></ion-icon>
                </div>
            @endfor
        </div>

        <textarea name="body" id="body" class="w-full text-sm p-4 border rounded-lg outline-none h-32" placeholder="Tulis sesuatu"></textarea>

        <div class="flex items-center justify-end gap-4 mt-4">
            <button class="p-3 px-6 rounded-lg text-sm bg-slate-200 text-slate-700" type="button" onclick="toggleHidden('#WriteReview')">Batal</button>
            <button class="p-3 px-6 rounded-lg text-sm bg-green-500 text-white font-medium">Kirim</button>
        </div>
    </form>
</div>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@yield('javascript')
<script>
    const select = dom => document.querySelector(dom);
    const selectAll = dom => document.querySelectorAll(dom);

    const toggleHidden = target => select(target).classList.toggle('hidden');
    let ButtonArea = select("#ButtonArea");

    const doPrint = () => {
        ButtonArea.classList.add('hidden');
        setTimeout(() => {
            window.print();
        }, 500);
    }

    const rateInput = select("#rate");
    const stars = selectAll("#ratingStars [data-rating]");

    stars.forEach(star => {
        star.addEventListener("click", () => {
            const rating = Number(star.dataset.rating);

            rateInput.value = rating;

            stars.forEach(item => {
                const itemRating = Number(item.dataset.rating);
                const icon = item.querySelector("ion-icon");

                icon.setAttribute(
                    "name",
                    itemRating <= rating ? "star" : "star-outline"
                );
            });
        });
    });
</script>

</body>
</html>