<div class="fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-30" id="CreateStore">
    <form action="{{ route('admin.master.stores.store') }}" method="POST" class="bg-white shadow-lg rounded-lg p-10 w-4/12 mobile:w-10/12 flex flex-col gap-4 mt-4">
        <div class="flex items-center gap-4 mb-4">
            <h3 class="text-lg text-slate-700 font-medium flex grow">Buat Toko</h3>
            <ion-icon name="close-outline" class="cursor-pointer text-3xl" onclick="toggleHidden('#CreateStore')"></ion-icon>
        </div>

        <div class="flex flex-col gap-1">
            <div class="text-xs text-slate-600">Nama Toko</div>
            <input type="text" name="name" class="w-full h-12 rounded-lg border px-4 text-sm outline-none" required>
        </div>

        <div class="flex flex-col gap-1">
            <div class="text-xs text-slate-600">Alamat</div>
            <textarea name="address" id="address" class="w-full rounded-lg border p-4 text-sm outline-none" rows="4" required></textarea>
        </div>

        <div class="flex flex-col gap-1">
            <div class="text-xs text-slate-600">Koordinat</div>
            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="latitude" class="w-full h-12 rounded-lg border px-4 text-sm outline-none" placeholder="Lat">
                <input type="text" name="longitude" class="w-full h-12 rounded-lg border px-4 text-sm outline-none" placeholder="Long">
            </div>
        </div>

        <div></div>

        <div class="relative group flex justify-center mt-2">
            <div class="w-full h-[1px] bg-slate-200"></div>
            <div class="absolute bg-white p-1 px-3 text-xs text-slate-500 top-[-12px]">
                Informasi User
            </div>
        </div>

        <div class="flex flex-col gap-1">
            <div class="text-xs text-slate-600">Email</div>
            <input type="email" name="email" class="w-full h-12 rounded-lg border px-4 text-sm outline-none" required>
        </div>

        <div class="flex items-center justify-end gap-4 mt-4">
            <button class="p-3 px-6 rounded-lg text-sm bg-slate-200 text-slate-700" type="button" onclick="toggleHidden('#CreateStore')">Batal</button>
            <button class="p-3 px-6 rounded-lg text-sm bg-green-500 text-white font-medium">Buat</button>
        </div>
    </form>
</div>