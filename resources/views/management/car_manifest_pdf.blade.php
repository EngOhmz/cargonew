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
        font-size:12px
    }
    
    .fontS{
        font-size:11px
    }
    .fontN{
        font-size:14px
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
                        <hr style="border: dashed;">      
                <h3 align="center" style="font-weight: normal;" class="fontL"><b>MANIFEST</b></h3>
                <hr style="border: dashed;">

                <div class="space" align="center">
                <p class="fontL" ><b class="fontN">Dereva:&nbsp;&nbsp;<?php echo $driver; ?></b></p>
<p class="space fontL" ><b>Phone: &nbsp;&nbsp;<?php echo $dr_no; ?> </b></p>
<p class="space fontL"><b>Gari: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $carNumber; ?></b></p>
                </div>
                
                        <?php
// OPTIMIZED: Removed model imports - using pre-fetched data instead
$count = 0;

                          if (!empty($pacelUnique)):foreach ($pacelUnique as $it):


                            $count = $count + 1;

                        // OPTIMIZED: Use pre-fetched data instead of database queries
                        // Production-safe array access with proper null handling
                        $pacelId = $it->pacel_id ?? null;
                        $pacelData = null;
                        if($pacelId && isset($customerPacels[$pacelId])){
                            $pacelData = $customerPacels[$pacelId];
                        }
                        
                        $delivery = ($pacelData && isset($pacelData['delivery'])) ? $pacelData['delivery'] : '';
                        $mteja = ($pacelData && isset($pacelData['mpokeaji']) && $pacelData['mpokeaji'] !== '')
                            ? $pacelData['mpokeaji']
                            : (($pacelData && isset($pacelData['mteja'])) ? $pacelData['mteja'] : '');
                        $receipt = ($pacelData && isset($pacelData['receipt'])) ? $pacelData['receipt'] : '';
                        $to = ($pacelData && isset($pacelData['mzigo_unapokwenda'])) ? $pacelData['mzigo_unapokwenda'] : '';

                        // OPTIMIZED: Use pre-fetched created_at instead of database query
                        $created_at = null;
                        if($pacelId && isset($pacelHistoryDates[$pacelId])){
                            $created_at = $pacelHistoryDates[$pacelId];
                            // Ensure it's a Carbon instance for production compatibility
                            if($created_at && !($created_at instanceof \Carbon\Carbon)){
                                try {
                                    $created_at = \Carbon\Carbon::parse($created_at);
                                } catch(\Exception $e){
                                    $created_at = null;
                                }
                            }
                        }

                        $total =  $pacels->where('pacel_id',$it->pacel_id)->where('activity', 'kupakia')->sum('jumla');


                        ?>
                        

                <div class="space  fontS">
                    <div class="col-md-12">                 
<p class="fontL" ><b class="fontN">#<?php echo $count; ?></b>&nbsp;&nbsp;<b>JINA:<?php echo $mteja; ?> &nbsp;&nbsp;&nbsp;REF:<?php echo $delivery; ?></b></p>
<p class="space fontL" ><b>RECEIPT: <?php echo $receipt; ?> &nbsp;&nbsp; DATE: <?php echo $created_at ? $created_at->format('Y-m-d') : 'N/A'; ?> @ <?php echo $created_at ? $created_at->format('H:i:s') : 'N/A'; ?></b></p>
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



