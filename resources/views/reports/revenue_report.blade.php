@extends('layout.master')

@push('plugin-styles')
    {!! Html::style('plugins/flatpickr/flatpickr.css') !!}
    {!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
    {!! Html::style('assets/css/elements/tooltip.css') !!}
    <style>
        .footer-totals {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Revenue Report</h4>
                            <div>
                                <button id="export_excel" class="btn btn-success btn-sm">
                                    <i class="icon-file-excel"></i> Export Excel
                                </button>
                                <button id="export_pdf" class="btn btn-danger btn-sm">
                                    <i class="icon-file-pdf"></i> Export PDF
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="start_date">Tarehe ya Kuanzia</label>
                                    <input type="text" id="start_date" class="form-control flatpickr" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date">Tarehe ya Kuishia</label>
                                    <input type="text" id="end_date" class="form-control flatpickr" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button id="filter_button" class="btn btn-primary w-100">Chuja</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th>Tarehe</th>
                                            <th>Mteja</th>
                                            <th>Mpokeaji (Ofisi)</th>
                                            <th>Jina la Mzigo</th>
                                            <th>Route</th>
                                            <th>Bei ya Mzigo</th>
                                            <th>Hela Iliyopokelewa</th>
                                            <th>Jumla</th>
                                            <th>Hela Iliyobaki</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr class="footer-totals">
                                            <td colspan="5" class="text-right">Jumla Kuu:</td>
                                            <td></td>
                                            <td id="total_received">0</td>
                                            <td id="total_jumla">0</td>
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
$(function() {
    $(".flatpickr").flatpickr({
        dateFormat: "Y-m-d",
    });

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('reports.revenue.index') }}",
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'mteja', name: 'mteja' },
            { data: 'mpokeaji', name: 'mpokeaji' },
            { data: 'name', name: 'name' },
            { data: 'route', name: 'route', orderable: false, searchable: false },
            { data: 'bei', name: 'bei' },
            { data: 'ela_iliyopokelewa', name: 'ela_iliyopokelewa' },
            { data: 'jumla', name: 'jumla' },
            { data: 'balance', name: 'balance', orderable: false, searchable: false }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            var total_received = api.column(6).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            var total_jumla = api.column(7).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            var total_balance = api.column(8).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

            $('#total_received').html(new Intl.NumberFormat().format(total_received));
            $('#total_jumla').html(new Intl.NumberFormat().format(total_jumla));
            $('#total_balance').html(new Intl.NumberFormat().format(total_balance));
        }
    });

    $('#filter_button').on('click', function() {
        table.draw();
    });

    $('#export_excel').on('click', function() {
        var start = $('#start_date').val();
        var end = $('#end_date').val();
        window.location.href = "{{ route('reports.revenue.excel') }}?start_date=" + start + "&end_date=" + end;
    });

    $('#export_pdf').on('click', function() {
        var start = $('#start_date').val();
        var end = $('#end_date').val();
        window.location.href = "{{ route('reports.revenue.pdf') }}?start_date=" + start + "&end_date=" + end;
    });
});
</script>
@endpush
