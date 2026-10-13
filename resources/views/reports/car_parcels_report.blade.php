@extends('layout.master')

@push('plugin-styles')
    {!! Html::style('plugins/flatpickr/flatpickr.css') !!}
    {!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
    <style>
        .footer-totals { background-color: #f8f9fa; font-weight: bold; }
    </style>
@endpush

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="mb-0">Report ya Gari, Mizigo & Wateja</h4>
                        <button id="export_pdf" class="btn btn-danger btn-sm">
                            <i class="icon-file-pdf"></i> Print PDF
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="car_id">Gari</label>
                                <select id="car_id" class="form-control">
                                    <option value="">Gari zote</option>
                                    @foreach($cars as $car)
                                        <option value="{{ $car->id }}">{{ $car->carNumber }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="start_date">Tarehe ya Kuanzia</label>
                                <input type="text" id="start_date" class="form-control flatpickr" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date">Tarehe ya Kuishia</label>
                                <input type="text" id="end_date" class="form-control flatpickr" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="filter_button" class="btn btn-primary w-100">Chuja</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped data-table w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tarehe</th>
                                        <th>Gari</th>
                                        <th>REF</th>
                                        <th>Mteja</th>
                                        <th>Mpokeaji (Ofisi)</th>
                                        <th>Mzigo</th>
                                        <th>Route</th>
                                        <th>Idadi</th>
                                        <th>Jumla</th>
                                        <th>Imepokelewa</th>
                                        <th>Baki</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr class="footer-totals">
                                        <td colspan="8" class="text-right">Jumla:</td>
                                        <td id="total_qty">0</td>
                                        <td id="total_jumla">0</td>
                                        <td id="total_received">0</td>
                                        <td id="total_balance">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('plugin-scripts')
    {!! Html::script('plugins/flatpickr/flatpickr.js') !!}
@endpush

@push('custom-scripts')
<script type="text/javascript">
$(function () {
    $(".flatpickr").flatpickr({ dateFormat: "Y-m-d" });

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('reports.car_parcels.index') }}",
            data: function (d) {
                d.car_id = $('#car_id').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'car_number', name: 'car_number', orderable: false, searchable: false },
            { data: 'delivery', name: 'delivery' },
            { data: 'mteja', name: 'mteja' },
            { data: 'mpokeaji', name: 'mpokeaji' },
            { data: 'name', name: 'name' },
            { data: 'route', name: 'route', orderable: false, searchable: false },
            { data: 'idadi_kupakia', name: 'idadi_kupakia' },
            { data: 'jumla', name: 'jumla' },
            { data: 'ela_iliyopokelewa', name: 'ela_iliyopokelewa' },
            { data: 'balance', name: 'balance', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        footerCallback: function () {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : (typeof i === 'number' ? i : 0);
            };
            var fmt = function (n) { return new Intl.NumberFormat().format(n); };

            $('#total_qty').html(fmt(api.column(8).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#total_jumla').html(fmt(api.column(9).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#total_received').html(fmt(api.column(10).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#total_balance').html(fmt(api.column(11).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
        }
    });

    $('#filter_button').on('click', function () {
        table.draw();
    });

    $('#export_pdf').on('click', function () {
        var params = $.param({
            car_id: $('#car_id').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val()
        });
        window.open("{{ route('reports.car_parcels.pdf') }}?" + params, '_blank');
    });
});
</script>
@endpush
