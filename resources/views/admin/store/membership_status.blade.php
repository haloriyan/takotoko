<div class="fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-30" id="StatusChanger">
    <form method="POST" class="bg-white shadow-lg rounded-lg p-10 w-4/12 mobile:w-10/12 flex flex-col gap-4 mt-4">
        <div class="flex items-center gap-4 mb-4">
            <h3 class="text-lg text-slate-700 font-medium flex grow">Ubah Status</h3>
            <ion-icon name="close-outline" class="cursor-pointer text-3xl" onclick="toggleHidden('#StatusChanger')"></ion-icon>
        </div>

        <div class="text-sm text-slate-600">
            Ubah status pembayaran menjadi
        </div>
        <select name="status" id="status" class="border rounded-lg text-sm text-slate-800 h-14 outline-none cursor-pointer" required>
            <option value="">Pilih...</option>
            <option value="PAID">PAID</option>
            <option value="CANCELLED">CANCELLED</option>
        </select>

        <div class="flex items-center justify-end gap-4 mt-4">
            <button class="p-3 px-6 rounded-lg text-sm bg-slate-200 text-slate-700" type="button" onclick="toggleHidden('#StatusChanger')">Batal</button>
            <button class="p-3 px-6 rounded-lg text-sm bg-green-500 text-white font-medium">Ubah</button>
        </div>
    </form>
</div>