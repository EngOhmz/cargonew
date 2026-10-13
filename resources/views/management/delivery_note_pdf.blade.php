<!DOCTYPE html>
<html>
<head>
    <title>DELIVERY NOTE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body, html {
            margin: 0px 5px 10px 5px !important;
            padding: 1px 1px 0px 1px !important;
            font-family: 'Courier New', Courier, monospace;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .space {
            margin-top: -1em;
        }
        .fontL {
            font-size: 12px;
        }
        .fontS {
            font-size: 11px;
        }
        .wave {
            border: dashed;
        }
        ol.kanuni {
            padding-left: 18px;
            margin: 10px 0 0 0;
            font-size: 10px;
            line-height: 1.4;
        }
        ol.kanuni li {
            margin-bottom: 6px;
        }
    </style>
</head>
<body>
    @php
        $pacel = $data;
        $delivery = $pacel->delivery ?: ($pacel->hashtag ? 'RG-' . $pacel->hashtag : '#' . $pacel->id);
        $receipt = $pacel->receipt;
        $to = $pacel->mzigo_unapokwenda;
        $from = $pacel->mzigo_unapotoka ?: '#';
        $created_at = $pacel->created_at;
        $qty = $pacel->idadi;
        $bei = (float) str_replace(',', '', (string) ($pacel->bei ?? 0));
        $jumla = (float) str_replace(',', '', (string) ($pacel->jumla ?? 0));
        $paid = (float) str_replace(',', '', (string) ($pacel->ela_iliyopokelewa ?? 0));
        $jina = $pacel->mpokeaji ?: ($pacel->mteja ?? '-');
    @endphp

    <header>
        <img style="margin-left: 5%; width: 240px; height: 60px;" src="{{ $logoUrl }}" />
        @if(!empty($systemLogoTitle))
            <h3 align="center" style="font-weight: bold; margin-top: 5px;">{{ $systemLogoTitle }}</h3>
        @endif
    </header>

    @include('management.partials.document_header')

    <hr class="wave" />
    <h3 align="center" style="font-weight: normal;" class="fontL"><b>DELIVERY NOTE</b></h3>
    <hr class="wave" />

    <div class="col-md-12">
        <p class="fontL"><b>JINA: &nbsp;&nbsp;&nbsp;&nbsp;{{ $jina }}</b></p>
        <p class="space fontL"><b>REF: &nbsp;&nbsp;&nbsp;&nbsp;{{ $delivery }}</b></p>
        <p class="space fontL"><b>RECEIPT: &nbsp;&nbsp;&nbsp;&nbsp;{{ $receipt }}</b></p>
        <p class="space fontL"><b>DATE: &nbsp;&nbsp;&nbsp;&nbsp;{{ $created_at ? $created_at->format('Y-m-d') . ' @ ' . $created_at->format('H:i:s') : '' }}</b></p>
        <p class="space fontL"><b>TO: &nbsp;&nbsp;&nbsp;&nbsp;{{ $to }}</b></p>
        <p class="space fontS">CARGO: &nbsp;&nbsp;&nbsp;&nbsp;{{ $pacel->name }}</p>
        <p class="space fontS">FROM: &nbsp;&nbsp;&nbsp;&nbsp;{{ $from }}</p>
        <p class="space fontS">QUANTITY:&nbsp;&nbsp; {{ $qty }}&nbsp;&nbsp; @ {{ number_format($bei) }}</p>
        <p class="space fontS">TOTAL: {{ number_format($jumla) }}TSH/= &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CASH RECEIVED: {{ number_format($paid) }} TSH/=</p>
        <hr class="wave" />
        <br>
        <p class="space fontL"><b>TOTAL PRICE: {{ number_format($jumla) }} TSH/=</b></p>
        <p align="center">======****======</p>
    </div>

    <ol class="kanuni">
        <li>{{ $kanuni1 ?? 'MTEJA HAKIKISHA UMEFUNGA MZIGO WAKO IMARA' }}</li>
        <li>{{ $kanuni2 ?? 'MTEJA MALI YOYOTE ITAKAYO HARIBIKA NJIANI HATUTOKUWA NA DHAMANA NAYO KUTOKANA NA HALI YA BARABARA NI MUHIMU KUKATIA MZIGO WAKO BIMA KWA KUWA MSAFIRISHAJI HATOHUSIKA NA BIMA YA MZIGO WAKO' }}</li>
        <li>{{ $kanuni3 ?? 'MZIGO WA KUVUNJIKA KAMA VILE VIGAE,VIOO,TV N.K HATUTAHUSIKA NA UHARIBIFU WOWOTE UTAKAO JITOKEZA' }}</li>
        <li>{{ $kanuni4 ?? 'HATUPOKEI MALALAMIKO YOYOTE KUHUSU MZIGO BAADA YA SIKU 20 TANGU KUPOKEA' }}</li>
    </ol>

    @if(!empty($directPrint))
        @include('partials.direct_print_script')
    @else
    <script type="text/php">
        if (isset($pdf)) {
            $x = 20;
            $y = 820;
            $text = "{PAGE_NUM} of {PAGE_COUNT}";
            $font = null;
            $size = 9;
            $color = array(0,0,0);
            $pdf->page_text($x, $y, $text, $font, $size, $color, 0.0, 0.0, 0.0);
        }
    </script>
    @endif
</body>
</html>
