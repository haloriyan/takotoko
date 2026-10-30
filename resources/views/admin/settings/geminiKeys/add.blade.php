<div class="fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-30" id="AddKey">
    <form action="{{ route('admin.settings.geminiKeys.store') }}" method="POST" class="bg-white shadow-lg rounded-lg p-10 w-4/12 mobile:w-10/12 flex flex-col gap-4 mt-4">
        @csrf
        <div class="flex items-center gap-4 mb-4">
            <h3 class="text-lg text-slate-700 font-medium flex grow">Tambahkan API Key</h3>
            <ion-icon name="close-outline" class="cursor-pointer text-3xl" onclick="toggleHidden('#AddKey')"></ion-icon>
        </div>

        <div class="flex flex-col gap-2">
            <div class="text-xs text-slate-500">Label</div>
            <input type="text" name="label" class="w-full h-12 outline-none text-sm text-slate-800 border rounded-lg px-4" placeholder="Label / Email">
        </div>
        <div class="flex flex-col gap-2">
            <div class="text-xs text-slate-500">Key</div>
            <input type="text" name="value" class="w-full h-12 outline-none text-sm text-slate-800 border rounded-lg px-4" required>
        </div>

        <div class="flex items-center justify-end gap-4 mt-4">
            <button class="p-3 px-6 rounded-lg text-sm bg-slate-200 text-slate-700" type="button" onclick="toggleHidden('#AddKey')">Batal</button>
            <button class="p-3 px-6 rounded-lg text-sm bg-green-500 text-white font-bold">Tambahkan</button>
        </div>
    </form>
</div>