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
                <h3 align="center" style="font-weight: normal;" class="fontL"><b>MANIFEST 3</b></h3>
                <hr style="border: dashed;">

                <div class="space" align="center">
                <p class="fontL" ><b class="fontN">Dereva:&nbsp;&nbsp;<?php echo $driver; ?></b></p>
<p class="space fontL" ><b>Phone: &nbsp;&nbsp;<?php echo $dr_no; ?> </b></p>
<p class="space fontL"><b>Gari: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $carNumber; ?></b></p>
                </div>
                
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
                        

                <div class="space  fontS">
                    <div class="col-md-12">                 
<p class="fontL" ><b class="fontN">3 #<?php echo $count; ?></b>&nbsp;&nbsp;<b>JINA:<?php echo $mteja; ?> &nbsp;&nbsp;&nbsp;REF:<?php echo $delivery; ?></b></p>
<p class="space fontL" ><b>RECEIPT: <?php echo $receipt; ?> &nbsp;&nbsp; DATE: <?php echo $created_at->format('Y-m-d'); ?> @ <?php echo $created_at->format('H:i:s'); ?></b></p>
<p class="space fontL"><b>TO: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $to; ?></b></p>

<?php $histories =  $pacels->where('pacel_id',$it->pacel_id); foreach ($histories as $data): if( $data->activity == "kupakia"): ?>
<br>
<?php if( empty($data->hashtag)): ?>

<p class="space" class="space">Cargo: <?php echo $data->name; ?> &nbsp;&nbsp; QUANTITY: <?php echo $data->idadi_kupakia; ?></p>
<p class="space">PRICE: <?php echo number_format(floatval($data->jumla)); ?>TSH/= @ <?php echo number_format(floatval($data->bei)); ?> TSH/= &nbsp;&nbsp;CASH RECEIVED: <?php echo number_format(floatval($data->ela_iliyopokelewa)); ?> TSH/=</p>
<hr style="border: dashed;"> 

<?php else: ?>
<p class="space" class="space">Cargo: <?php echo $data->name; ?></p>
<p class="space" class="space">hashtag: &nbsp;&nbsp; <?php echo $data->hashtag; ?> &nbsp;&nbsp; QUANTITY: <?php echo $data->idadi_kupakia; ?></p>
<p class="space">PRICE: <?php echo number_format(floatval($data->jumla)); ?>TSH/= @ <?php echo number_format(floatval($data->bei)); ?> TSH/= &nbsp;&nbsp;CASH RECEIVED: <?php echo number_format(floatval($data->ela_iliyopokelewa)); ?> TSH/=</p>
<hr style="border: dashed;"> 

<?php endif; ?>



                        <?php endif; ?>
                        <?php endforeach; ?>

<br><p class="space"><b>TRANSPORT PRICE(In car) : <?php echo number_format(floatval($total)); ?> TSH/=</b></p>
<hr style="border: dashed;">

                        <?php endforeach; ?> 
                        

                        <?php endif; ?>     
                <h3 align="center" style="font-weight: normal;" class="fontL"><b>SUMMARY</b></h3>
                <hr style="border: dashed;">

<ol>
    <li>VEHICLE: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $carNumber; ?></li>
    <li>TO:  &nbsp;&nbsp;&nbsp;&nbsp;<?php foreach ($to_list as $data): echo $data->mzigo_unapokwenda;?>&nbsp;&nbsp;&nbsp;<?php endforeach; ?></li>
    <li>CUSTOMERS: &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $total_customers; ?></li>
    <li>ITEMS:  &nbsp;&nbsp;&nbsp;&nbsp;  <?php echo $total_pacel; ?></li>
    <li>VALUE:  &nbsp;&nbsp;&nbsp;&nbsp; <?php echo number_format($value); ?></li>
    <li>CASH RECEIVED: &nbsp;&nbsp;&nbsp;&nbsp; <?php echo number_format($total_paid); ?></li>
    <li style="font-size:11px;">PRINTED:  &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $printed->format('Y-m-d');  ?>@ <?php echo $printed->format('H:i:s');  ?></li>

</ol>

<p align="center">======End======</p>
</div></div></div>



