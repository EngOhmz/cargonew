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
        font-size:12px
    }
    .fontN{
        font-size:18px
    }
    .page-break {
            page-break-inside: avoid;
        }
    </style>

</head>

<body>

        <header>
            <img style="margin-left: 5%; width: 240px;  height: 60px" src="{{public_path('logo_madekenya.png')}}" />
        </header>

    <div >
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
                <h3 align="center" style="font-weight: normal;" class="fontL"><b>IN CAR INVOICE 3</b></h3>
                <hr style="border: dashed;">
                
                        <?php

use App\Models\Customer\CustomerPacel;
use App\Models\PacelHistory;
$count = 0;


                          if (!empty($pacelUnique)):foreach ($pacelUnique as $it):

                            $count = $count + 1;


                        $delivery = CustomerPacel::where('id', $it->pacel_id)->value('delivery');
                        
                        $mteja = CustomerPacel::where('id', $it->pacel_id)->value('mteja');

                        $receipt = CustomerPacel::where('id', $it->pacel_id)->value('receipt');

                        $to = CustomerPacel::where('id', $it->pacel_id)->value('mzigo_unapokwenda');


                        $created_at = PacelHistory::where('pacel_id', $it->pacel_id)->where('activity', 'kupakia')->latest()->first()->created_at;

                        $total =  $pacels->where('pacel_id',$it->pacel_id)->where('activity', 'kupakia')->sum('jumla');


                        ?>
  
                <h3 align="center" style="font-weight: normal;" class="fontL"><b>INVOICE</b></h3>
                <hr style="border: dashed;">
                        

                <div class="space">
                    <div class="col-md-12">
<p class="fontL"><b class="fontN">3 #<?php echo $count; ?></b>&nbsp;&nbsp;<b>JINA:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $mteja; ?></b></p>
<p class="space fontL"><b>REF: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $delivery; ?></b></p>
<p class="space fontL"><b>RECEIPT: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $receipt; ?></b></p>
<p class="space fontL"><b>DATE: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $created_at->format('Y-m-d'); ?> @ <?php echo $created_at->format('H:i:s'); ?></b></p>
<p class="space fontL"><b>TO: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $to; ?></b></p>



<?php $histories =  $pacels->where('pacel_id',$it->pacel_id); foreach ($histories as $data): if( $data->activity == "kupakia"): ?>
<br> 
<?php if( empty($data->hashtag)): ?>
<p class="space fontS" class="space">CARGO: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $data->name; ?></p>
<p class="space fontS" class="space">QUANTITY:&nbsp;&nbsp; <?php echo $data->idadi_kupakia; ?>&nbsp;&nbsp; @ <?php echo number_format(floatval($data->bei)); ?></p>
<p class="space fontS">TOTAL: <?php echo number_format(floatval($data->jumla)); ?>TSH/= &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CASH RECEIVED: <?php echo number_format(floatval($data->ela_iliyopokelewa)); ?> TSH/=</p>
<hr style="border: dashed;"> 
<?php else: ?>
<p class="space fontS" class="space">CARGO: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $data->name; ?></p>
<p class="space fontS" class="space">hashtag: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $data->hashtag; ?></p>
<p class="space fontS" class="space">QUANTITY:&nbsp;&nbsp; <?php echo $data->idadi_kupakia; ?>&nbsp;&nbsp; @ <?php echo number_format(floatval($data->bei)); ?></p>
<p class="space fontS">TOTAL: <?php echo number_format(floatval($data->jumla)); ?>TSH/= &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CASH RECEIVED: <?php echo number_format(floatval($data->ela_iliyopokelewa)); ?> TSH/=</p>
<hr style="border: dashed;"> 
<?php endif; ?>



                        <?php endif; ?>
                        <?php endforeach; ?>

<br><p class="space fontL"><b>TOTAL PRICE: <?php echo number_format(floatval($total)); ?> TSH/=</b></p>
<p align="center">======****======</p>


                        <?php endforeach; ?> 

                        <?php endif; ?>
                
                      




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