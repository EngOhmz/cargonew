@extends('layout.master')

@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/apex/apexcharts.css') !!}
{!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
{!! Html::style('plugins/flatpickr/flatpickr.css') !!}
{!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
{!! Html::style('assets/css/elements/tooltip.css') !!}
<style>
    .dn-hint {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
        font-size: 14px;
        color: #334155;
    }
    .dn-hint strong { color: #0f172a; }
    .dataTables_filter input {
        min-width: 220px;
    }
</style>
@endpush

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Delivery Note - Fuatilia mzigo</h4>
                    </div>
                    <div class="card-body">
                        <div class="dn-hint">
                            <strong>Tafuta kwa Reference Number</strong>
                            (REF / Delivery ID / RG-001 / hashtag).
                            Andika namba kwenye sanduku la <em>Filter / Search</em> - matokeo yanaonekana baada ya kutafuta tu.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped data-table w-100">
                                <thead>
                                    <tr>
                                        <th>No#</th>
                                        <th>Mteja</th>
                                        <th>REF No#</th>
                                        <th>Mpokeaji (Ofisi)</th>
                                        <th>Mwandishi</th>
                                        <th>Hali</th>
                                        <th>Tarehe</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
{!! Html::script('assets/js/loader.js') !!}
{!! Html::script('plugins/apex/apexcharts.min.js') !!}
{!! Html::script('plugins/flatpickr/flatpickr.js') !!}
{!! Html::script('assets/js/dashboard/dashboard_1.js') !!}
@endpush

@push('custom-scripts')
<script type="text/javascript">
$(function () {
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('delivery_note') }}",
        pageLength: 10,
        order: [[6, 'desc']],
        language: {
            search: "Filter:",
            searchPlaceholder: "Andika REF / Delivery ID...",
            lengthMenu: "Show: _MENU_",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            zeroRecords: "Hakuna mzigo uliopatikana - jaribu REF nyingine",
            emptyTable: "Andika reference number kutafuta mzigo"
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'mteja', name: 'mteja' },
            { data: 'delivery', name: 'delivery' },
            { data: 'mpokeaji', name: 'mpokeaji' },
            { data: 'added_by', name: 'added_by', orderable: false },
            { data: 'status_label', name: 'status_label', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $.fn.dataTable.ext.errMode = 'none';
});
</script>
@endpush
