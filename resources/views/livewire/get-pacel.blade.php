<div>
@if(!empty($mizigo))
                                @foreach($mizigo as $row)
                                <input type="hidden" name="name[]" value="{{$row->name}}" required>
                                <input type="hidden" name="idadi[]" value="{{$row->idadi}}">
                                <input type="hidden" name="bei[]" value="{{$row->bei}}">
                                <input type="hidden" name="receipt[]" value="{{$row->receipt}}">
                                <input type="hidden" name="mzigo_unapotoka[]" value="{{$row->mzigo_unapotoka}}">
                                <input type="hidden" name="mzigo_unapokwenda[]" value="{{$row->mzigo_unapokwenda}}" required>
                                <input type="hidden" name="jumla[]" value="{{$row->jumla}}" required>
                                <input type="hidden" name="ela_iliyopokelewa[]" value="{{$row->ela_iliyopokelewa}}">
                                
                                <div class="alert alert-purple alert-dismissible">
										<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                        <span><b><i>jIna la mzigo : </i></b><b>{{!empty($row->name)? $row->name : ''}}</b>( {{$row->idadi}} )</span><br>
										<span class="font-weight-semibold">(BEI :{{$row->jumla}})</span> <b>Kutoka :</b>  {{!empty($row->mzigo_unapotoka)? $row->mzigo_unapotoka : ''}} - <b> Kwenda : </b> {{!empty($row->mzigo_unapokwenda)? $row->mzigo_unapokwenda : ''}}  &nbsp;&nbsp;&nbsp; <a class="btn btn-danger" href="{{route('temp_pacel_delete',$row->id)}}" role="button">Delete</a>
								    </div>
                                @endforeach
                                @endif
                                
</div>
