<table id="basic-dt" class="table table-hover dataTable" style="width: 100%;" role="grid" aria-describedby="basic-dt_info">
    <thead>
    <tr role="row">
        <th class="sorting_asc" tabindex="0" aria-controls="basic-dt" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 98px;">{{ __('levels.image') }}</th>
        <th class="sorting" tabindex="0" aria-controls="basic-dt" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 134px;">{{ __('levels.name') }}</th>
        <th class="sorting" tabindex="0" aria-controls="basic-dt" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 80px;">{{ __('levels.price') }}</th>
        <th class="sorting" tabindex="0" aria-controls="basic-dt" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 30px;">{{ __('levels.status') }}</th>
        <th class="no-content sorting" tabindex="0" aria-controls="basic-dt" rowspan="1" colspan="1" aria-label=": activate to sort column ascending" style="width: 1px;">{{ __('levels.action') }}</th>
    </tr>
    </thead>
    <tbody>
        
        @foreach($menu_item_list as $menu_item)
            <tr>
            <td><img class="img img-thumbnail rounded-circle" style="width: 50px;border-radius: 50%;height: 50px" src="{{ $menu_item->image }}"  alt=""></td>
            <td>{{$menu_item->name}}</td>
            <td>{{$menu_item->unit_price}}</td>
             <td>{{ trans('menu_item_statuses.' .$menu_item->status) }}</td>
            <td><div>
                <a href="#variation-{{$menu_item->id}}" wire:click.prevent="addToCartModal({{$menu_item->id}})">
                    <i class="las la-plus text-primary font-20 mr-2 btn-edit-contact"></i> </a></div> 
           </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th rowspan="1" colspan="1">{{ __('levels.image') }}</th>
        <th rowspan="1" colspan="1">{{ __('levels.name') }}</th>
        <th rowspan="1" colspan="1">{{ __('levels.price') }}</th>
        <th rowspan="1" colspan="1">{{ __('levels.status') }}</th>
        <th rowspan="1" colspan="1">{{ __('levels.action') }}</th>
    </tr>
    </tfoot>
</table>


       
