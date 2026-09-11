<table>
    <tr>
        <th colspan="4" style="font-size: 32px;font-weight: 700;background: #eeeeee;text-align: center;vertical-align: middle">Laporan Penjualan</th>
    </tr>
    <tr>
        <td>Periode</td>
        <td colspan="3">
            {{ $start_date }} - {{ $end_date }}
        </td>
    </tr>
    <tr>
        <td style="background: #2196f3;color: #ffffff;font-size: 16px;font-weight: 500;">Omset</td>
        <td style="font-size: 16px;">{{ currency_encode(25000) }}</td>
        <td style="background: #2ecc71;color: #ffffff;font-size: 16px;font-weight: 500;">Margin</td>
        <td style="font-size: 16px;">{{ currency_encode(12000) }}</td>
    </tr>
</table>