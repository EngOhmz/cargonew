<script type="text/javascript">
$(function() {
    var selectedMpokeaji = '';
    var isDebtPage = {{ !empty($isDebtPage) && $isDebtPage ? 'true' : 'false' }};

    var storeColumns = [
            { data: 'delivery', name: 'delivery' },
            { data: 'mpokeaji', name: 'mpokeaji' },
            { data: 'created_at', name: 'created_at' },
            { data: 'name', name: 'name' },
            { data: 'stoo_display', name: 'idadi_stoo', orderable: false, searchable: false },
            { data: 'bei', name: 'bei' },
            { data: 'jumla', name: 'jumla', orderable: false, searchable: false },
            { data: 'mzigo_unapotoka', name: 'mzigo_unapotoka' },
            { data: 'mzigo_unapokwenda', name: 'mzigo_unapokwenda' },
            { data: 'ela_iliyopokelewa', name: 'ela_iliyopokelewa' },
            { data: 'receipt', name: 'receipt' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ];

    var debtColumns = [
        { data: 'delivery', name: 'delivery' },
        { data: 'mpokeaji', name: 'mpokeaji' },
        { data: 'created_at', name: 'created_at' },
        { data: 'name', name: 'name' },
        { data: 'idadi', name: 'idadi' },
        { data: 'bei', name: 'bei' },
        { data: 'jumla', name: 'jumla', orderable: false, searchable: false },
        { data: 'ela_iliyopokelewa', name: 'ela_iliyopokelewa', orderable: false, searchable: false },
        { data: 'hela_iliyobaki', name: 'hela_iliyobaki', orderable: false, searchable: false },
        { data: 'mzigo_unapotoka', name: 'mzigo_unapotoka' },
        { data: 'mzigo_unapokwenda', name: 'mzigo_unapokwenda' },
        { data: 'receipt', name: 'receipt' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ];

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        autoWidth: false,
        ajax: {
            url: "{{ $dataRoute }}",
            data: function (d) {
                if (isDebtPage) {
                    d.customer_mpokeaji = selectedMpokeaji;
                }
            }
        },
        columns: isDebtPage ? debtColumns : storeColumns
    });

    if (isDebtPage) {
        $(document).on('click', '.sidebar-filter', function(e) {
            e.preventDefault();
            $('.sidebar-filter').removeClass('active');
            $(this).addClass('active');
            selectedMpokeaji = $(this).data('mpokeaji') || '';
            table.ajax.reload(null, false);
        });
    }

    $.fn.dataTable.ext.errMode = 'none';

    function positionStoreDropdown($toggle, $menu) {
        var offset = $toggle.offset();
        var top = offset.top + $toggle.outerHeight();
        var left = offset.left + $toggle.outerWidth() - $menu.outerWidth();
        if (left < 8) left = 8;
        $menu.css({ top: top + 'px', left: left + 'px', display: 'block' });
    }

    $(document).on('click', '.store-action-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $toggle = $(this);
        var $menu = $toggle.next('.dropdown-menu');
        var isOpen = $menu.hasClass('show');
        $('.store-row-actions .dropdown-menu').removeClass('show').hide();
        if (!isOpen) {
            $menu.addClass('show');
            positionStoreDropdown($toggle, $menu);
        }
    });

    $(document).on('click', function() {
        $('.store-row-actions .dropdown-menu').removeClass('show').hide();
    });

    $(document).on('click', '.store-row-actions .dropdown-menu', function(e) {
        e.stopPropagation();
    });

    $(window).on('scroll', function() {
        $('.store-row-actions .dropdown-menu.show').removeClass('show').hide();
    });

    $('.table-responsive').on('scroll', function() {
        $('.store-row-actions .dropdown-menu.show').removeClass('show').hide();
    });

    $(document).on('click', '.js-close-store-modal', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).closest('.modal').modal('hide');
    });

    $('#pacelEditModal, #pacelMoneyModal').on('hidden.bs.modal', function() {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });

    function loadPacelForEdit(id) {
        var url = '{{ route("pacel.edit", ":id") }}'.replace(':id', id);
        $.get(url, function(response) {
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
            if (response.paceldata.receipt === 'HR') {
                $('#cr_l_i_u').prop('checked', true);
            } else {
                $('#cr_l_i_s').prop('checked', true);
            }
            $('#pacelEditModal').modal('show');
        });
    }

    function loadPacelForMoney(id) {
        var url = '{{ route("money.edit", ":id") }}'.replace(':id', id);
        $.get(url, function(response) {
            $('#pacel_id2').val(response.paceldata2.id);
            var total = parseFloat(String(response.paceldata2.jumla).replace(/,/g, '')) || 0;
            var received = parseFloat(String(response.paceldata2.ela_iliyopokelewa).replace(/,/g, '')) || 0;
            var remaining = Math.max(0, total - received);
            $('#pacelRemainingDue2').text(remaining.toFixed(0));
            $('#pacelAmount_received2').val(remaining);
            $('#pacelMoneyModal').modal('show');
        });
    }

    $(document).on('click', '.store-edit-trigger', function(e) {
        e.preventDefault();
        $('.store-row-actions .dropdown-menu').removeClass('show').hide();
        loadPacelForEdit($(this).data('id'));
    });

    $(document).on('click', '.store-money-trigger', function(e) {
        e.preventDefault();
        $('.store-row-actions .dropdown-menu').removeClass('show').hide();
        loadPacelForMoney($(this).data('id'));
    });

    $(document).on('input', '#pacelIdadi, #pacelBei', function() {
        var qty = parseFloat($('#pacelIdadi').val()) || 0;
        var price = parseFloat($('#pacelBei').val()) || 0;
        $('#pacelJumla').val((qty * price).toFixed(2));
    });
});
</script>
