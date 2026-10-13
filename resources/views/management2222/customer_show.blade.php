@extends('layout.master')

@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/apex/apexcharts.css') !!}
{!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
{!! Html::style('plugins/flatpickr/flatpickr.css') !!}
{!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
{!! Html::style('assets/css/elements/tooltip.css') !!}

<script src="{{asset('global_assets/js/plugins/forms/inputs/inputmask.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/selects/bootstrap_multiselect.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/uploaders/bs_custom_file_input.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/extensions/jquery_ui/core.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/tags/tagsinput.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/tags/tokenfield.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/touchspin.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/maxlength.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/formatter.min.js')}}"></script>
<script src="{{asset('global_assets/js/demo_pages/form_floating_labels.js')}}"></script>
@endpush
@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">

            <div class="col-12 col-sm-12 col-lg-12">

                <div class="card">
                                    <div class="card-body">
                                    
                                    <div class="">                            
                                        <p class="form-control-static" style="text-align:center;"><strong>REF: </strong><?php echo $data->delivery; ?></p>
                                    </div>

                                    <div class="">                            
                                        <p class="form-control-static" style="text-align:center;"><strong>MTEJA  : </strong><?php echo $data->mteja; ?></p>
                                    </div>
                                    <div class="">
                                            <p class="form-control-static" style="text-align:center;"><strong>MPOKEAJI  : </strong><?php echo $data->mpokeaji; ?></p>    
                                    </div>

                                    <div class="">                            
                                        <p class="form-control-static" style="text-align:center;"><strong>MUDA  : </strong><?php echo $data->created_at->format('Y-m-d H:i:s'); ?></p>
                                    </div>

                                    <div class="">                            
                                        <p class="form-control-static" style="text-align:center;"><strong>VERSION  : </strong><?php echo $data->hashtag; ?></p>
                                    </div>
                    <div class="card-body">
                         <!-- Tabs within a box -->
                         <ul class="nav nav-tabs">
                            <li class="nav-item"><a
                                    class="nav-link @if(empty($id)) active show @endif" href="#home2"
                                    data-toggle="tab">Registered</a>
                            </li>

                            <li class="nav-item"><a class="nav-link @if(!empty($id)) active show @endif"
                                    href="#profile2" data-toggle="tab">On Store</a>
                            </li>

                            <li class="nav-item"><a class="nav-link @if(!empty($id)) active show @endif"
                                    href="#profile3" data-toggle="tab">On Car</a>
                            </li>


                        </ul>
                        <div class="tab-content tab-bordered">
                             <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2">
                   
                                <div class="card">
                                    
 <hr><br>  

 <div class="row">

    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-12">
    <div class="panel panel-custom">

                    <div class="panel-body">

                <div class="card-body table-responsive" >
                    <table class="table table-bordered table-striped bg-light">
                       
                         <tr>
                            <th>DESCRIPTION:</th>
                            <td><strong><?php echo $data->name; ?></strong></td>
                        </tr>
                         <tr>
                            <th>OUANTITY:</th>
                            <td><strong><?php echo $data->idadi; ?></strong></td>
                        </tr>
                        <tr>
                            <th>@PRICE:</th>
                            <td><strong><?php echo $data->bei; ?></strong></td>
                        </tr>
                         <tr>
                            <th>TOTAL PRICE:</th>
                            <td><strong><?php echo $data->jumla; ?></strong></td>
                        </tr>
                        <tr>
                            <th>TOTAL PAID:</th>
                            <td><strong><?php echo $data->ela_iliyopokelewa; ?></strong></td>
                        </tr>
                        <?php
                            use App\Models\User;
                         $user = User::where('id', $data->added_by)->first();
                        $mwandishi = $user->email;?>
                         <tr>
                            <th>SIGNATURE:</th>
                            <td><strong><?php echo $mwandishi. " ".$data->created_at->format('Y-m-d H:i:s'); ?></strong></td>
                        </tr>

                        <tr>
                            <td colspan="2"><strong><?php echo "PRINT DELIVERY"; ?></strong></td>
                        </tr>
                        

                    </table>
                </div>

                    </div>
    </div>
    </div>
 </div>
                                    </div>

                                </div>

                             <div class="tab-pane fade @if(!empty($id)) active show @endif" id="profile2">

                             <div class="card">

 <hr><br> 
 
<div class="row">

<div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-12">
<div class="panel panel-custom">

                <div class="panel-body">

            <div class="card-body table-responsive" >
                <table class="table table-bordered table-striped bg-light">
                   
                     <tr>
                        <th>DESCRIPTION:</th>
                        <td><strong><?php echo $data2->name; ?></strong></td>
                    </tr>
                     <tr>
                        <th>OUANTITY:</th>
                        <td><strong><?php echo $data2->idadi_stoo; ?></strong></td>
                    </tr>
                    <tr>
                        <th>@PRICE:</th>
                        <td><strong><?php echo $data2->bei; ?></strong></td>
                    </tr>
                     <tr>
                        <th>TOTAL PRICE:</th>
                        <td><strong><?php echo $data2->jumla; ?></strong></td>
                    </tr>
                    <tr>
                        <th>TOTAL PAID:</th>
                        <td><strong><?php echo $data2->ela_iliyopokelewa; ?></strong></td>
                    </tr>
                    <?php
                     $user = User::where('id', $data->added_by)->first();
                    $mwandishi = $user->email;?>
                     <tr>
                        <th>SIGNATURE:</th>
                        <td><strong><?php echo $mwandishi. " ".$data->created_at->format('Y-m-d H:i:s'); ?></strong></td>
                    </tr>

                    <tr>
                        <td colspan="2"><strong><?php echo "PRINT DELIVERY"; ?></strong></td>
                    </tr>
                    

                </table>
            </div>

                </div>
</div>
</div>
</div>
 
 



                             </div>

                             </div>

                             <div class="tab-pane fade @if(!empty($id)) active show @endif" id="profile3">

                             <div class="card">

                             <hr><br>  

 <div class="row">

    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-xs-12">
    <div class="panel panel-custom">

                    <div class="panel-body">

                <div class="card-body table-responsive" >
                    <table class="table table-bordered table-striped bg-light">
                       <thead>
                         <tr>
                            <th>DESCRIPTION:</th>
                            <th>OUANTITY:</th>
                            <th>CAR:</th>
                            <th>@PRICE:</th>
                            <th>TOTAL PRICE:</th>
                            <th>TOTAL PAID:</th>
                            <th>DATE:</th>
                            <th>SIGNATURE:</th>







                        </tr>
                        </thead>
                        <tbody>
                        <?php

                            use App\Models\Management\Car;
                            use App\Models\Management\Driver;


                                            if (!empty($data3)):foreach ($data3 as $row):

                                            $cr = Car::find($row->car_id)->carNumber;
                                            $dr_id = Car::find($row->car_id)->driver_id;

                                            $dr = Driver::find($dr_id)->name;

                                         

                        ?>
                         <tr>
                            
                            <td><strong><?php echo $row->name; ?></strong></td>

                            <td><strong><?php echo $row->idadi; ?></strong></td>

                            <td><strong><?php echo $cr." ".$dr; ?></strong></td>

                            <td><strong><?php echo $row->bei; ?></strong></td>

                            <td><strong><?php echo $row->jumla; ?></strong></td>

                            <td><strong><?php echo $row->ela_iliyopokelewa; ?></strong></td>

                            <td><strong><?php echo $row->created_at->format('Y-m-d'); ?></strong></td>

                            <?php
                         $user = User::where('id', $row->added_by)->first();
                        $mwandishi = $user->email;?>

                            <td><strong><?php echo $mwandishi." "."($cr $dr)".$data->created_at->format('Y-m-d H:i:s'); ?></strong></td>






                        </tr>
                        <?php endforeach; ?> 

                                        <?php else: ?>

                                            <tr>
                                            <td colspan="3">No data available in table </td>
                                            </tr>

                                        <?php endif; ?>


                        </tbody>
                        

                    </table>
                </div>

                    </div>
    </div>
    </div>
 </div>



                             </div>

                             </div>

                             </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>




                    

</div>
</section>

<!-- discount Modal -->
<div class="modal inmodal show" id="appFormModal" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg">
</div>
</div>
</div>
</div>

@endsection

@push('plugin-scripts')
{!! Html::script('assets/js/loader.js') !!}
{!! Html::script('plugins/apex/apexcharts.min.js') !!}
{!! Html::script('plugins/flatpickr/flatpickr.js') !!}
{!! Html::script('assets/js/dashboard/dashboard_1.js') !!}
@endpush

@push('custom-scripts')
<script type="text/javascript">
$(function() {



var table = $('.data-table').DataTable({

processing: true,

serverSide: true,

ajax: "{{ route('list_ya_wateja.index') }}",

columns: [

{
    data: 'id',
    name: 'id'
},

{
    data: 'mteja',
    name: 'mteja'
},

{
    data: 'mpokeaji',
    name: 'mpokeaji'
},

{
    data: 'action',
    name: 'action',
    orderable: false,
    searchable: false
},

]

});


$.fn.dataTable.ext.errMode = 'none';

$('#table').on( 'error.dt', function ( e, settings, techNote, message ) {
console.log( 'An error has been reported by DataTables: ', message );
} ) ;



});
</script>

<script type="text/javascript">
function model(id, type) {

let url = '{{ route("list_ya_wateja.detail", ":id") }}';
url = url.replace(':id', id)

$.ajax({
type: 'GET',
url: url,
data: {
    'type': type,
},
cache: false,
async: true,
success: function(data) {
    //alert(data);
    $('.modal-dialog').html(data);
},
error: function(error) {
    $('#appFormModal').modal('toggle');

}
});

}
</script>
@endpush