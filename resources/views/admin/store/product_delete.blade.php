<div class="fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-30" id="DeleteProduct">
    <form method="GET" class="bg-white shadow-lg rounded-lg p-10 w-4/12 mobile:w-10/12 flex flex-col gap-4 mt-4">
        <div class="flex items-center gap-4 mb-4">
            <h3 class="text-lg text-slate-700 font-medium flex grow">Hapus Produk</h3>
            <ion-icon name="close-outline" class="cursor-pointer text-3xl" onclick="toggleHidden('#DeleteProduct')"></ion-icon>
        </div>

        <div class="text-sm text-slate-600">
            Yakin ingin menghapus <span id="name"></span>?
        </div>

        <div class="flex items-center justify-end gap-4 mt-4">
            <button class="p-3 px-6 rounded-lg text-sm bg-slate-200 text-slate-700" type="button" onclick="toggleHidden('#DeleteProduct')">Batal</button>
            <button class="p-3 px-6 rounded-lg text-sm bg-red-500 text-white font-medium">Hapus</button>
        </div>
    </form>
</div>