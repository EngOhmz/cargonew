<div class="dropdown store-row-actions">
    <a href="#" class="btn btn-light btn-sm store-action-toggle" title="Actions" aria-label="Actions">
        <i class="icon-cog6"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <a href="{{ route('pacel_show', $row->id) }}" class="dropdown-item">
            <i class="icon-eye mr-2"></i> View
        </a>
        <a href="{{ route('car.show', $row->id) }}" class="dropdown-item" target="_blank">
            <i class="icon-printer mr-2"></i> Print Delivery
        </a>

        @if($isDebtPage)
        <a href="#" class="dropdown-item store-money-trigger" data-id="{{ $row->id }}">
            <i class="icon-coin-dollar mr-2"></i> Receive Money
        </a>
        @endif

        <div class="dropdown-divider"></div>
        <a href="{{ route('pacel_delete', $row->id) }}" class="dropdown-item text-danger"
           onclick="return confirm('Are you sure you want to delete this parcel?')">
            <i class="icon-trash mr-2"></i> Delete
        </a>
    </div>
</div>
