<!DOCTYPE html>
<html>
<head>
    <title>Car Parcels Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; text-align: left; }
        th { background: #f3f4f6; font-size: 9px; text-transform: uppercase; }
        .header { text-align: center; margin-bottom: 12px; }
        .header h2 { margin: 0 0 4px; font-size: 16px; }
        .meta { margin: 0; color: #444; }
        .num { text-align: right; }
        tfoot td { font-weight: bold; background: #f3f4f6; }
        .footer { margin-top: 14px; font-size: 9px; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REPORT YA GARI, MIZIGO &AMP; WATEJA</h2>
        @if(!empty($branch) || !empty($contact))
            <p class="meta">{{ $branch }} @if(!empty($contact)) | {{ $contact }} @endif</p>
        @endif
        <p class="meta">Gari: {{ $carLabel }}</p>
        <p class="meta">
            Kipindi:
            @if($endDate)
                {{ $startDate }} - {{ $endDate }}
            @else
                {{ $startDate }}
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tarehe</th>
                <th>Gari</th>
                <th>REF</th>
                <th>Mteja</th>
                <th>Mpokeaji (Ofisi)</th>
                <th>Mzigo</th>
                <th>Route</th>
                <th>Idadi</th>
                <th>Jumla</th>
                <th>Imepokelewa</th>
                <th>Baki</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->created_at ? $row->created_at->format('Y-m-d H:i') : '' }}</td>
                    <td>{{ optional($row->car)->carNumber ?: '-' }}</td>
                    <td>{{ $row->delivery ?: '-' }}</td>
                    <td>{{ $row->mteja }}</td>
                    <td>{{ $row->mpokeaji }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->mzigo_unapotoka }} → {{ $row->mzigo_unapokwenda }}</td>
                    <td class="num">{{ $row->idadi_kupakia ?: $row->idadi }}</td>
                    <td class="num">{{ number_format((float) $row->jumla) }}</td>
                    <td class="num">{{ number_format((float) $row->ela_iliyopokelewa) }}</td>
                    <td class="num">{{ number_format((float) $row->jumla - (float) $row->ela_iliyopokelewa) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align:center;">Hakuna taarifa kwa filters ulizochagua.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="num">JUMLA</td>
                <td class="num">{{ number_format($totals['qty']) }}</td>
                <td class="num">{{ number_format($totals['jumla']) }}</td>
                <td class="num">{{ number_format($totals['received']) }}</td>
                <td class="num">{{ number_format($totals['balance']) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Imechapishwa: {{ now()->format('Y-m-d H:i:s') }}</p>
        @if(!empty($tunasafirisha))
            <p>{{ $tunasafirisha }}</p>
        @endif
    </div>
</body>
</html>
