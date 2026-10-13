<?php

namespace App\Http\Controllers\restaurant;

use App\Enums\CategoryStatus;
use App\Enums\MenuItemStatus;
use App\Enums\Status;
use App\Http\Controllers\restaurant\BackendController;
use Illuminate\Http\Request;
use App\Models\restaurant\MenuItem;
use App\Http\Requests\Restaurant\MenuItemRequest;
use App\Models\restaurant\Category;
use App\Models\restaurant\Restaurant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;


class OrdersMenuItemController extends BackendController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->getMenuItem($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['categories'] = Category::where(['status' => CategoryStatus::ACTIVE])->get();
        $this->data['restaurants'] = Restaurant::where(['status' => Status::ACTIVE])->get();
        return view('restaurant.menu-item.create', $this->data);
    }

     /**
     * @param MenuItemRequest $request
     * @return mixed
     */
   public function store(MenuItemRequest $request)
    {
        $menus = MenuItem::where('restaurant_id', $request->get('restaurant_id'))->get();
        $menu_array = [];
        if (isset($menus)) {
            foreach ($menus as $menu) {
                $menu_array[] = $menu->menu_number;
            }
        }
        $menuNumber = $this->checkMenuNumber($menu_array);
        $menuItem                 = new MenuItem;
        $menuItem->restaurant_id  = $request->get('restaurant_id');
        $menuItem->name           = $request->get('name');
        $menuItem->description    = $request->get('description');
        $menuItem->unit_price     = $request->get('unit_price');
        $menuItem->discount_price = $request->get('discount_price') ?? 0;
        $menuItem->status         = $request->get('status');
        $menuItem->menu_number         = $menuNumber;
        $menuItem->save();

        $menuItem->categories()->sync($request->get('categories'));

        //Store Image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $menuItem->addMediaFromRequest('image')->toMediaCollection('menu-items');
        }

        return redirect()->route('menu-items.index')->withSuccess('The data inserted successfully!');
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function modify($id)
    {
        $menuItem = MenuItem::owner()->findOrFail($id);

        $this->data['menuItem']             = $menuItem;
        $this->data['menu_item_variations'] = $menuItem->variations;
        $this->data['menu_item_options']    = $menuItem->options;

        return view('restaurant.menu-item.modify', $this->data);
    }


    private function getMenuItem($request)
    {
        if (request()->ajax()) {
            $queryArray = [];
            if (!empty($request->status) && (int) $request->status) {
                $queryArray['status'] = $request->status;
            }
            // if (auth()->user()->myrole != 1 && auth()->user()->restaurant){
            //     $queryArray['restaurant_id'] =auth()->user()->restaurant->id;
            // }

            if (!blank($queryArray)) {
                $menuItems = MenuItem::with('categories')->where($queryArray)->descending()->get();
            } else {
                $menuItems = MenuItem::with('categories')->descending()->get();
            }

            $i = 0;
            return Datatables::of($menuItems)
                ->addColumn('action', function ($menuItem) {
                    $retAction = '';
                    // if (auth()->user()->can('menu-items_edit')) {
                    //     $retAction .= '<a href="' . route('admin.menu-items.modify', $menuItem) . '" class="btn btn-sm btn-icon float-left btn-success mr-2" data-toggle="tooltip" data-placement="top" title="Add Variation/Option"> <i class="far fa-list-alt"></i></a>';
                    // }
                    // if (auth()->user()->can('menu-items_show')) {
                    //     $retAction .= '<a href="' . route('admin.menu-items.show', $menuItem) . '" class="btn btn-sm btn-icon mr-2  float-left btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></a>';
                    // }
                    // if (auth()->user()->can('menu-items_edit')) {
                    //     $retAction .= '<a href="' . route('admin.menu-items.edit', $menuItem) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="far fa-edit"></i></a>';
                    // }
                    // if (auth()->user()->can('menu-items_delete')) {
                    //     $retAction .= '<form class="float-left pl-2" action="' . route('admin.menu-items.destroy', $menuItem) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"> <i class="fa fa-trash"></i></button></form>';
                    // }
                        // $retAction .= '<a href="' . route('menu-items.modify', $menuItem) . '" class="btn btn-sm btn-icon float-left btn-success mr-2" data-toggle="tooltip" data-placement="top" title="Add Variation/Option"> <i class="far fa-list-alt"></i></a>';
                       
                        $retAction .= '<a href="' . route('menu-items.show', $menuItem) . '" class="btn btn-sm btn-icon mr-2  float-left btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="las la-eye"></i></a>';
                        $retAction .= '<a href="' . route('menu-items.edit', $menuItem) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="las la-edit"></i></a>';
                        $retAction .= '<form class="float-left pl-2" action="' . route('menu-items.destroy', $menuItem) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"> <i class="las la-trash"></i></button></form>';
                    return $retAction;
                })
                ->editColumn('id', function ($menuItem) use (&$i) {
                    return ++$i;
                })
                ->editColumn('categories', function ($menuItem) {
                    $categories = implode(', ', $menuItem->categories()->pluck('name')->toArray());
                    return Str::limit($categories, 30);
                })
                ->editColumn('name', function ($menuItem) {

                    $col ='<a href="#variation-'.$menuItem->id.'" wire:click.prevent="addToCartModal('.$menuItem->id.')">';
                    $col .=' <p class="product-name-style">'.Str::limit($menuItem->name,20).'</p>';
                    // $col1 =' <p class="food-price-p with-color">'. Str::limit(strip_tags($menuItem->description),44).'</p>';
                                                                    
                    // $col .=' <p class="food-price-p custom-design">'.$menuItem->unit_price - $menuItem->discount_price.'</p> </a>';
                                                                   
                                                               
                    // return  $col1;
                    $col .= '<p class="p-0 m-0">' . Str::limit($menuItem->name, 20) . '</p>';
                    $col .= '<small class="text-muted">' . Str::limit($menuItem->description, 20) . '</small>';
                    return $col;
                })
                ->editColumn('status', function ($menuItem) {
                    return trans('menu_item_statuses.' . $menuItem->status) ?? trans('menu_item_statuses.' . MenuItemStatus::INACTIVE);
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }
        return view('restaurant.menu-item.index', $this->data);
    }



    function checkMenuNumber($menu_array)
    {
        $menuNumber = rand(1000, 9999);
        $menuNumber = in_array($menuNumber, $menu_array) ? $this->checkMenuNumber($menu_array) : $menuNumber;
        return $menuNumber;
    }
}
