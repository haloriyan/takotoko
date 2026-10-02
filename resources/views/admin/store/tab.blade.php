@php
    $route = Route::currentRouteName();
    $routes = explode(".", $route);
@endphp
<div class="bg-white rounded-full p-2 flex items-center shadow">
    <a 
        href="{{ route('admin.master.stores.detail', $store->username) }}" 
        class="p-2 px-4 rounded-full text-sm font-medium {{ $routes[3] == 'detail' ? 'bg-primary-transparent text-primary font-bold' : '' }}"
    >
        Info
    </a>
    <a 
        href="{{ route('admin.master.stores.membership', $store->username) }}" 
        class="p-2 px-4 rounded-full text-sm font-medium {{ $routes[3] == 'membership' ? 'bg-primary-transparent text-primary font-bold' : '' }}"
    >
        Membership
    </a>
    <a 
        href="{{ route('admin.master.stores.product', $store->username) }}" 
        class="p-2 px-4 rounded-full text-sm font-medium {{ $routes[3] == 'product' ? 'bg-primary-transparent text-primary font-bold' : '' }}"
    >
        Produk
    </a>
    <a 
        href="{{ route('admin.master.stores.employee', $store->username) }}" 
        class="p-2 px-4 rounded-full text-sm font-medium {{ $routes[3] == 'employee' ? 'bg-primary-transparent text-primary font-bold' : '' }}"
    >
        Karyawan
    </a>
</div>