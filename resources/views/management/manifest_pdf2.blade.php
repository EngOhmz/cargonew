<!DOCTYPE html>
<html>

<head>
    <title>MANIFEST</title>

    <style>
    body, html { 
            
            margin: 0px 5px 10px 5px !important;
            padding: 1px 1px 0px 1px !important;
        }
    table {
       
        width: 100%;
    }

    td,
    th {
        
    }
    .space{
        margin-top: -1em;
    }
    .fontL{
        font-size: 12px;
    }
    
    .fontS{
        font-size: 11px;
    }
    .page-break {
            page-break-inside: avoid;
        }
    </style>

</head>

<body>

        <header>
            <img style="margin-left: 5%; width: 240px;  height: 60px" src="{{ $logoUrl }}" />
            @if($systemLogoTitle)
                <h3 align="center" style="font-weight: bold; margin-top: 5px;">{{ $systemLogoTitle }}</h3>
            @endif
        </header>
    @include('management.partials.document_header')
                        <hr style="border: dashed;" />      
                <h3 align="center" style="font-weight: normal;" class="fontL"><b>INVOICE</b></h3>
                <hr style="border: dashed;" />
                
                        <?php
                        $pacel = $data;
                        $delivery = $pacel->delivery;
                        $receipt = $pacel->receipt;
                        $to = $pacel->mzigo_unapokwenda;
                        $created_at = $pacel->created_at;
                        $total = $pacel->jumla;
                        ?>
   
                <h3 align="center" style="font-weight: normal;" class="fontL"><b>INVOICE</b></h3>
                    <div class="col-md-12">
<p class="fontL"><b>JINA:  &nbsp;&nbsp;&nbsp;&nbsp;{{ $pacel->mpokeaji }}</b></p>
<p class="space fontL"><b>REF: &nbsp;&nbsp;&nbsp;&nbsp;{{ $delivery }}</b></p>
<p class="space fontL"><b>RECEIPT: &nbsp;&nbsp;&nbsp;&nbsp;{{ $receipt }}</b></p>
<p class="space fontL"><b>DATE: &nbsp;&nbsp;&nbsp;&nbsp;{{ $created_at->format('Y-m-d') }} @ {{ $created_at->format('H:i:s') }}</b></p>
<p class="space fontL"><b>TO: &nbsp;&nbsp;&nbsp;&nbsp;{{ $to }}</b></p>

@if( empty($pacel->hashtag) )
<p class="space fontS" class="space">CARGO: &nbsp;&nbsp;&nbsp;&nbsp;{{ $pacel->name }}</p>
<p class="space fontS" class="space">QUANTITY:&nbsp;&nbsp; {{ $pacel->idadi }}&nbsp;&nbsp; @ {{ number_format(floatval($pacel->bei)) }}</p>
<p class="space fontS">TOTAL: {{ number_format(floatval($pacel->jumla)) }}TSH/= &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CASH RECEIVED: {{ number_format(floatval($pacel->ela_iliyopokelewa)) }} TSH/=</p>
<hr style="border: dashed;" /> 
@else
<p class="space fontS" class="space">CARGO: &nbsp;&nbsp;&nbsp;&nbsp;{{ $pacel->name }}</p>
<p class="space fontS" class="space">hashtag: &nbsp;&nbsp;&nbsp;&nbsp;{{ $pacel->hashtag }}</p>
<p class="space fontS" class="space">QUANTITY:&nbsp;&nbsp; {{ $pacel->idadi }}&nbsp;&nbsp; @ {{ number_format(floatval($pacel->bei)) }}</p>
<p class="space fontS">TOTAL: {{ number_format(floatval($pacel->jumla)) }}TSH/= &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CASH RECEIVED: {{ number_format(floatval($pacel->ela_iliyopokelewa)) }} TSH/=</p>
<hr style="border: dashed;" /> 
@endif

<br><p class="space fontL"><b>TOTAL PRICE: {{ number_format(floatval($total)) }} TSH/=</b></p>
<p align="center">======****======</p>
                

                      



                    </div>
                </div>
    </div>

    @if(!empty($directPrint))
        @include('partials.direct_print_script')
    @else
    <script type="text/php">
        if ( isset($pdf) ) {
        $x = 20;
        $y = 820;
        $text = "madekenya                                            - - - -    {PAGE_NUM} of {PAGE_COUNT} pages    - - - - ";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);


     }


</script>
    @endif

</body>

</html>
