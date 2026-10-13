<!DOCTYPE html>
<html>

<head>
    <title>MANIFEST</title>

    <style>
      body, html { 
            
            margin: 0px 5px 10px 5px !important;
            padding: 1px 1px px 0px !important;
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
        font-size:15px
    }
    
    .fontS{
        font-size:13.5px
    }
    .fontR{
        font-size:10px
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
    <div>
        <h4 align="center" style="margin-top: 0%;" class="fontS">TUNASAFRISHA MIZIGO KUTOKA DAR ES SALAAM<h4>
                <h4 align="center" style="margin-top: 0%; font-size:9px;">KWENDA IRINGA, NJOMBE NA MBEYA</h4>
                
                <table  style=" border: none !important; family:source_sans_proregular; font-size:10px;">
                            <tbody>
                                <tr>
                                    <td align="">HEAD OFFICE</td>
                                    <td align="">BRANCH</td>

                                    <td align="">BRANCH</td>
                                    
                                    <!-- <td align="">BRANCH</td> -->
                                </tr>
                                <tr>
                                    <td align="">DAR ES SALAAM</td>
                                    <td align="">IRINGA</td>

                                    <td align="">NJOMBE</td>
                                    
                                   <!-- <td align="">MBEYA</td> -->
                                </tr>
                                <tr>
                                    <td align="">0715-896 930</td>
                                    <td align="">0715-896 930</td>

                                    <td align="">0715-896 930</td>
                                <!--    <td align="">0715-896 930</td> -->
                                </tr>
                            </tbody>
                        </table>
                        <hr style="border: dashed;">      
                <h3 align="center" style="font-weight: normal;" class="fontL"><b>WAY BILL NOTE</b></h3>
                <hr style="border: dashed;">
                


                <div class="space fontS">
                    <div class="col-md-12">
<p class="fontL"><b>JINA: {{ $data->mteja }}</b></p>
<p class="space fontL"><b>REF: {{ $data->delivery }}</b></p>
<p class="space fontL"><b>DATE: {{ $data->created_at->format('Y-m-d') }} @ {{ $data->created_at->format('H:i:s') }}<b></p><br>
<p class="space" class="space fontS">TO: {{ $data->mzigo_unapokwenda }} &nbsp;&nbsp;CARGO TYPE: {{ $data->name }}</p>
<p class="space fontS">FROM: {{$data->mzigo_unapotoka}}  &nbsp;&nbsp; QUANTITY: {{ $data->idadi }} </p>
<!-- <p class="space" class="space fontS"> PRICE: {{ $data->jumla }} @ {{ $data->bei }}/= TSH</p> -->
<p class="space fontS">RECEIPT: {{ $data->receipt }}</p>
<!-- <p class="space fontS">CASH RECEIVED {{ $data->ela_iliyopokelewa }} TSH/=</p> -->
<p class="space fontS"><b>TRANSPORT COST : {{ $data->jumla }} TSH</b></p>
 @php $user_ad = App\Models\User::find($data->added_by); @endphp
<p class="space  fontL"><b>RECEIVED BY : {{ $user_ad->name }} </b></p>

<div class="space fontR" style="margin: 5px;">
<ol>
<li>MTEJA HAKIKISHA UMEFUNGA MZIGO WAKO IMARA</li>
    <li>MTEJA MALI YOYOTE ITAKAYO HARIBIKA NJIANI HATUTOKUWA NA DHAMANA NAYO KUTOKANA NA HALI YA BARABARA NI MUHIMU KUKATIA MZIGO WAKO BIMA KWA KUWA MSAFIRISHAJI HATOHUSIKA NA BIMA YA MZIGO WAKO</li>
    <li>MZIGO WA KUVUNJIKA KAMA VILE VIGAE,VIOO,TV N.K HATUTAHUSIKA NA UHARIBIFU WOWOTE UTAKAO JITOKEZA</li>
    <li>HATUPOKEI MALALAMIKO YOYOTE KUHUSU MZIGO BAADA YA SIKU 7  TANGU KUPOKEA</li>
</ol>
</div>    




                    </div>
                </div>
    </div>

    <script type="text/php">
        if ( isset($pdf) ) {
        $x = 20;
        $y = 820;
        $text = "sanga                                            - - - -    {PAGE_NUM} of {PAGE_COUNT} pages    - - - - ";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);


     }


</script>

</body>

</html>