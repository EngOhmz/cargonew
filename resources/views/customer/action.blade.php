<div class="dropdown">
    <button class="btn btn-light btn-sm dropdown-toggle" type="button"
        id="customerActions{{ $row->id }}" aria-haspopup="true" aria-expanded="false">
        <i class="icon-cog6"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="customerActions{{ $row->id }}">
        <a href="{{ route('list_ya_wateja.detail', $row->id) }}" class="dropdown-item">
            <i class="icon-eye mr-2"></i> Angalia
        </a>
        <a href="{{ route('list_ya_wateja.editmzigo', $row->id) }}" class="dropdown-item">
            <i class="icon-plus2 mr-2"></i> Ongeza Mzigo
        </a>
        <a href="{{ route('customer.pacel.edit', $row->id) }}" class="dropdown-item">
            <i class="icon-pencil mr-2"></i> Badilisha
        </a>
    </div>
</div>
