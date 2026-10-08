@php
    use Carbon\Carbon;
@endphp

<table>
    <thead>
        <tr>
            <th colspan="7" style="font-size: 24px;font-weight: 700;background: #eeeeee;text-align: center;vertical-align: middle">{{ isset($date) ? Carbon::parse($date)->isoFormat('DD MMMM YYYY') : 'Laporan Penjualan' }}</th>
        </tr>
        <tr>
            <th style="background: #dddddd;font-weight: 700;">Invoice</th>
            <th style="background: #dddddd;font-weight: 700;">Pelanggan</th>
            <th style="background: #dddddd;font-weight: 700;">Tanggal</th>
            <th style="background: #dddddd;font-weight: 700;">Total</th>
            <th style="background: #dddddd;font-weight: 700;">Pembayaran</th>
            <th style="background: #dddddd;font-weight: 700;">Status</th>
            <th style="background: #dddddd;font-weight: 700;">Kasir</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $sale)
            <tr>
                <td>{{ $sale->invoice_number }}</td>
                <td>{{ $sale->customer->name ?? '-' }}</td>
                <td>{{ Carbon::parse($sale->created_at)->format('d M Y, H:i') }}</td>
                <td>{{ currency_encode($sale->total_price) }}</td>
                <td>{{ $sale->payment_method }}</td>
                <td>{{ $sale->payment_status }}</td>
                <td>{{ $sale->user->name ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>