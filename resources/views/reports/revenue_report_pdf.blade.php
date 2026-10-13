<!DOCTYPE html>
<html>
<head>
    <title>Revenue Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { margin-top: 20px; font-weight: bold; }
        .totals-table td { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REVENUE REPORT</h2>
        <p>{{ $branch }} | {{ $contact }}</p>
        <p>Period: @if($endDate){{ $startDate }} to {{ $endDate }}@else{{ $startDate }}@endif</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tarehe</th>
                <th>Mteja</th>
                <th>Mpokeaji (Ofisi)</th>
                <th>Mzigo</th>
                <th>Route</th>
                <th>Bei ya Mzigo</th>
                <th>Hela Iliyopokelewa</th>
                <th>Jumla</th>
                <th>Hela Iliyobaki</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row->created_at->format('Y-m-d') }}</td>
                <td>{{ $row->mteja }}</td>
                <td>{{ $row->mpokeaji }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->mzigo_unapotoka }} - {{ $row->mzigo_unapokwenda }}</td>
                <td>{{ number_format($row->bei) }}</td>
                <td>{{ number_format($row->ela_iliyopokelewa) }}</td>
                <td>{{ number_format($row->jumla) }}</td>
                <td>{{ number_format((float)$row->jumla - (float)$row->ela_iliyopokelewa) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #eee; font-weight: bold;">
                <td colspan="5" style="text-align: right;">TOTAL:</td>
                <td></td>
                <td>{{ number_format($totals['received']) }}</td>
                <td>{{ number_format($totals['jumla']) }}</td>
                <td>{{ number_format($totals['balance']) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Printed on: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>{{ $tunasafirisha }}</p>
    </div>
</body>
</html>
