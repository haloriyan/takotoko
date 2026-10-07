@php
    use Carbon\Carbon;
@endphp

<table>
    <thead>
        <tr>
            <th colspan="4" style="font-size: 24px;text-align: center;">{{ Carbon::parse($date)->isoFormat('DD MMMM YYYY') }}</th>
        </tr>
        <tr>
            <th style="background: #dddddd;font-size: 16px;font-weight: bold;">Produk</th>
            <th style="background: #dddddd;font-size: 16px;font-weight: bold;">Masuk</th>
            <th style="background: #dddddd;font-size: 16px;font-weight: bold;">Keluar</th>
            <th style="background: #dddddd;font-size: 16px;font-weight: bold;">Opname</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td style="font-size: 14px;">{{ $product['name'] }}</td>
                <td style="font-size: 14px;">{{ $product['in_qty'] }}</td>
                <td style="font-size: 14px;">{{ $product['out_qty'] }}</td>
                <td style="font-size: 14px;">{{ $product['opn_qty'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>