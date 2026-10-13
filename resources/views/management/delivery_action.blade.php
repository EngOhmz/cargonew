<div class="btn-group">
    <a href="{{ route('delivery_show', $row->id) }}" class="btn btn-outline-success btn-sm">
        View
    </a>
    <a href="{{ route('print_delivery_show', $row->id) }}" target="_blank" rel="opener" class="btn btn-outline-primary btn-sm">
        Print Delivery
    </a>
    <a href="{{ route('list_ya_mizigo.show', $row->id) }}" class="btn btn-outline-info btn-sm">
        Fuatilia
    </a>
</div>
