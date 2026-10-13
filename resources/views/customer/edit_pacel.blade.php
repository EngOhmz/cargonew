@extends('layout.master')

@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/apex/apexcharts.css') !!}
{!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
{!! Html::style('plugins/flatpickr/flatpickr.css') !!}
{!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
{!! Html::style('assets/css/elements/tooltip.css') !!}
/list_ya_wateja
<style>
#loader {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    background: rgba(0,0,0,0.75) url("{{asset('assets/img/triangles_indicator.gif')}}") no-repeat center center;
    z-index: 99999;
}
</style>
@endpush

@section('content')
<div id='loader'></div>

<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Utambulisho </h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab2"
                                    data-toggle="tab" href="#home2" role="tab" aria-controls="home"
                                    aria-selected="true">Badilisha Taarifa za Mizigo
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content tab-bordered" id="myTab3Content">
                            <div class="tab-pane fade active show" id="home2" role="tabpanel" aria-labelledby="home-tab2">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Hariri Usajili</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12 ">

                                                {{ Form::open(['route' => 'customer.pacel.update']) }}
                                                @method('POST')

                                                <input type="hidden" name="c_id" value="{{ $data2->id }}" />

                                                <div class="row">
                                                    <div class="col-md-2">
                                                    </div>

                                                    <div class="col-md-8">
                                                        <div class="form-group">

                                                            <div class="form-group row">
                                                                <label class="col-lg-2 col-form-label">Jina La Mpokeaji (Ofisi)</label>
                                                                <div class="col-lg-4">
                                                                    <div class="input-group mb-3">
                                                                        <input type="text" name="mpokeaji" class="form-control" value="{{ $data2->mpokeaji }}" required/>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-lg-2 col-form-label">Namba ya Mpokeaji</label>
                                                                <div class="col-lg-4">
                                                                    <input type="text" name="receiver_phone" class="form-control" value="{{ $data2->receiver_phone }}" placeholder="Namba ya ofisi / mpokeaji" />
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-lg-2 col-form-label">Jina La Mteja</label>
                                                                <div class="col-lg-4">
                                                                    <input type="text" name="mteja" class="form-control" value="{{ $data2->mteja }}" required/>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-lg-2 col-form-label">Namba ya Mteja</label>
                                                                <div class="col-lg-4">
                                                                    <input type="text" name="customer_phone" class="form-control" value="{{ $data2->customer_phone }}" placeholder="Namba ya mteja (SMS)....." />
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-lg-2 col-form-label">Kitambulisho cha Kipekee</label>
                                                                <div class="col-lg-4">
                                                                    <input type="text" class="form-control" value="{{ $data2->unique_identifier }}" readonly />
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                    </div>
                                                </div>

                                                <br>
                                                <hr>
                                                <div align="center">
                                                    <h5 class="text-primary">Mizigo</h5>
                                                </div><br>
                                                <br>

                                                <div class="row">
                                                    <div class="col-md-3"></div>
                                                    <div class="col-md-6">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" id="cart">
                                                                @foreach($items as $index => $item)
                                                                @php
                                                                    $jumlaVal = (float) str_replace(',', '', $item->jumla);
                                                                @endphp
                                                                @if($index > 0)
                                                                <tr><td colspan="2"><hr style="border-top: 2px solid #eee;"></td></tr>
                                                                @endif
                                                                <tr>
                                                                    <th>Jina la Mzigo:</th>
                                                                    <td><input type="text" name="name[{{ $item->id }}]" class="form-control" placeholder="jina la mzigo" value="{{ $item->name }}" required /></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Idadi ya Mzigo:</th>
                                                                    <td><input type="number" name="quantity[{{ $item->id }}]" step="any" class="form-control" placeholder="idadi ya mzigo" value="{{ $item->idadi }}" required /></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Bei ya kila mzigo <small class="text-muted">(si lazima)</small>:</th>
                                                                    <td><input type="number" name="price[{{ $item->id }}]" step="any" class="form-control" placeholder="Bei (si lazima)" value="{{ str_replace(',', '', $item->bei) }}" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Jumla Kuu:</th>
                                                                    <td><input type="text" name="total_cost[{{ $item->id }}]" class="form-control" placeholder="jumla" value="{{ str_replace(',', '', $item->jumla) }}" readonly /></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Mzigo Ulipotoka:</th>
                                                                    <td><input type="text" name="from[{{ $item->id }}]" class="form-control" placeholder="mzigo ulipotoka" value="{{ $item->mzigo_unapotoka }}" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Mzigo Unapokwenda:</th>
                                                                    <td><input type="text" name="to[{{ $item->id }}]" class="form-control" placeholder="mzigo unapokwenda" value="{{ $item->mzigo_unapokwenda }}" required /></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Risiti:</th>
                                                                    <td>
                                                                        <div class="form-check">
                                                                            <input type="radio" class="form-check-input" name="receipt[{{ $item->id }}]" value="R" {{ $item->receipt === 'R' ? 'checked' : '' }}>R
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input type="radio" class="form-check-input" name="receipt[{{ $item->id }}]" value="HR" {{ $item->receipt === 'HR' ? 'checked' : '' }}>HR
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3"></div>
                                                </div>

                                                <br>
                                                <div class="form-group row">
                                                    <div class="col-lg-offset-2 col-lg-12">
                                                        <button class="btn btn-sm btn-primary float-right m-t-n-xs" type="submit" id="save">Save Changes</button>
                                                    </div>
                                                </div>
                                                {!! Form::close() !!}
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
@endsection

@push('plugin-scripts')
<script>
$(function() {
    $( "form" ).submit(function() {
        $('#loader').show();
    });
});
</script>
{!! Html::script('assets/js/loader.js') !!}
{!! Html::script('plugins/apex/apexcharts.min.js') !!}
{!! Html::script('plugins/flatpickr/flatpickr.js') !!}
{!! Html::script('assets/js/dashboard/dashboard_1.js') !!}
@endpush

@section('scripts')
<script>
    (function ($) {
        function getItemIdFromName(name) {
            var match = name.match(/\[(\d+)\]/);
            return match ? match[1] : null;
        }

        function recalcForItem(itemId) {
            if (!itemId) return;

            var qty = parseFloat($('input[name="quantity[' + itemId + ']"]').val()) || 0;
            var price = parseFloat($('input[name="price[' + itemId + ']"]').val()) || 0;

            var jumla = qty * price;
            $('input[name="total_cost[' + itemId + ']"]').val(jumla ? jumla.toFixed(2) : '0.00');
        }

        $(document).on('input change keyup', 'input[name^="quantity["], input[name^="price["]', function () {
            var itemId = getItemIdFromName($(this).attr('name'));
            recalcForItem(itemId);
        });
    })(jQuery);
</script>
@endsection