

<!-- Default dropstart button -->
<div class="dropdown">
  <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    Select Action
  </button>
  <ul class="dropdown-menu">
    <!-- Dropdown menu links -->
    <li><a href="{{ route('pacel_show',  $row->id) }}" class="dropdown-item edit btn btn-warning btn-xs">View More</a></li>

    <li><a href="{{route('car.show',$row->id)}}" class="dropdown-item edit btn btn-success btn-xs">Print Delivery</a></li>

  </ul>
</div>
<br>
                     <a class="btn btn-danger" href="{{route('pacel_delete',$row->id)}}" role="button">Delete</a><br> 
                    <br> 



             <!-- Button trigger modal -->
            <a type="button" class="btn btn-warning btn-xs edit-mzigo-22" data-pacel-id="{{ $row->id }}" data-toggle="modal" data-target="#exampleModalCenter">
            Edit Mzigo
            </a> 
            <br>
            <!-- Modal -->
            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Mzigo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>

              
                <form data-parsley-validate class="form-horizontal form-label-left" action="{{ route('pacel.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                   
                
                <input type="hidden" name="pacel_id" id="pacel_id" value="">
                <div class="form-group form-group-float">
                        <label class="control-label">Jina la Mteja</label>
                        <input type="text" class="form-control" name="mteja"  id="mteja">
                    </div>
                    <div class="form-group form-group-float">
                        <label class="control-label">Jina la Mpokeaji</label>
                        <input type="text" class="form-control" name="mpokeaji"  id="mpokeaji">
                    </div>
                <div class="form-group form-group-float">
                        <label class="control-label">jina la mzigo</label>
                        <input type="text" name="name" class="form-control" id="pacelName" required>
                    </div>
                    <div class="form-group form-group-float">
                        <label class="control-label">idadi ya mizigo</label>
                        <input type="number" class="form-control" name="idadi"  id="pacelIdadi">
                    </div>
                    <div class="form-group form-group-float">
                        <label class="control-label">bei ya kila mzigo</label>
                        <input type="number" class="form-control" name="bei"  id="pacelBei">
                    </div>
                    <div class="form-group form-group-float">
                        <label class="control-label">jumla kuu</label>
                        <input type="number"  class="form-control" name="jumla"  id="pacelJumla" readonly>
                    </div>
                    <div class="form-group form-group-float">
                        <label class="control-label">Kiasi cha kupokea (leo)</label>
                        <input type="number" class="form-control" name="ela_iliyopokelewa"  id="pacelEla_iliyopokelewa">
                    </div>
                    <div class="form-group form-group-float">
                        <label class="control-label">mzigo ulipotoka</label>
                        <input type="text" class="form-control"  name="mzigo_unapotoka" id="pacelMzigo_unapotoka">
                    </div>
                    <div class="form-group form-group-float">
                        <label class="control-label">mzigo unapokwenda</label>
                        <input type="text" class="form-control" name="mzigo_unapokwenda" id="pacelMzigo_unapokwenda">
                    </div>

                    <div class="border p-3 rounded">
                        <label class="">Je ?</label>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="receipt"  id="cr_l_i_s" value="R" checked>
                            <label class="custom-control-label" for="cr_l_i_s">Una Risiti</label>
                        </div>

                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input"  value="HR"  name="receipt" id="cr_l_i_u" >
                            <label class="custom-control-label" for="cr_l_i_u">Hauna Risiti</label>
                        </div>
                    </div>



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Mzigo</button>
                </div>
                </form>
                </div>
            </div>
            </div>

            <br>


            <!-- Button trigger modal -->
            <a type="button" class="btn btn-success btn-xs money-mzigo-22" data-pacel-id="{{ $row->id }}" data-toggle="modal" data-target="#exampleModalCenter22">

            Receive Money
            </a>

            <!-- Modal -->
            <div class="modal fade" id="exampleModalCenter22" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle22" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle22">Receive Money</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form data-parsley-validate class="form-horizontal form-label-left" action="{{ route('money.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    

                <input type="hidden" name="pacel_id" id="pacel_id2" value="">
                    <div class="form-group form-group-float">
                        <label class="control-label">ela iliyopokelewa</label>
                        <input type="number" class="form-control" name="amount_received"  id="pacelEla_iliyopokelewa2" min="0" step="any" required>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update changes</button>
                </div>

                </form>


                </div>
            </div>
            </div>


            
<script>
    function editbtn(id) {
        let url = '{{ route("pacel.edit", ":id") }}';
        url = url.replace(':id', id)

        // var id = $(this).val();


        $.ajax({

            type: 'GET',
            url: url,

            success:function(response){
                  $('#pacel_id').val(response.paceldata.id);
                  $('#mteja').val(response.paceldata.mteja);
                  $('#mpokeaji').val(response.paceldata.mpokeaji);
                  $('#pacelName').val(response.paceldata.name);
                  $('#pacelIdadi').val(response.paceldata.idadi);
                  $('#pacelBei').val(response.paceldata.bei);
                  $('#pacelJumla').val(response.paceldata.jumla);
                  $('#pacelEla_iliyopokelewa').val(response.paceldata.ela_iliyopokelewa);
                  $('#pacelMzigo_unapotoka').val(response.paceldata.mzigo_unapotoka);
                  $('#pacelMzigo_unapokwenda').val(response.paceldata.mzigo_unapokwenda);
                //   $('#pacel_id').val(id);
               }
        });

    }

    function moneybtn(id) {
        let url = '{{ route("money.edit", ":id") }}';
        url = url.replace(':id', id)

        // var id = $(this).val();


        $.ajax({

            type: 'GET',
            url: url,

            success:function(response){
                  $('#pacel_id2').val(response.paceldata2.id);
                  var total = parseFloat(String(response.paceldata2.jumla).replace(/,/g, '')) || 0;
                  var received = parseFloat(String(response.paceldata2.ela_iliyopokelewa).replace(/,/g, '')) || 0;
                  var remaining = Math.max(0, total - received);
                  // Prefill with remaining due (so you can complete payment fast).
                  $('#pacelEla_iliyopokelewa2').val(remaining);
               }
        });

    }

    // Use delegated handlers (avoid inline onclick) for cleaner JS/linting.
    $(document).on('click', '.edit-mzigo-22', function () {
        editbtn($(this).data('pacel-id'));
    });

    $(document).on('click', '.money-mzigo-22', function () {
        moneybtn($(this).data('pacel-id'));
    });
    // $(document).ready(function(){
    //      $(document).on('click','#editbtn',function(){
    //         var id = $(this).val();
    //         $.ajax({
    //            type:"GET",
    //            url:"management/pacel_edit/"+id+"/edit",
               
    //            success:function(response){
    //               $('#pacel_id').val(response.paceldata.id);
    //               $('#pacelName').val(response.paceldata.name);
    //               $('#pacelIdadi').val(response.paceldata.idadi);
    //               $('#pacelBei').val(response.paceldata.bei);
    //               $('#pacelJumla').val(response.paceldata.jumla);
    //               $('#pacelEla_iliyopokelewa').val(response.paceldata.ela_iliyopokelewa);
    //               $('#pacelMzigo_unapotoka').val(response.paceldata.mzigo_unapotoka);
    //               $('#pacelMzigo_unapokwenda').val(response.paceldata.mzigo_unapokwenda);
    //               $('#pacel_id').val(id);
    //            }
    //         });
    //      });
    //   });
</script>