<?php

namespace App\Http\Controllers\restaurant;

use App\Models\User;
use Carbon\Carbon;
use App\Enums\Status;
use App\Models\restaurant\Order;
use App\Models\restaurant\Cuisine;
use App\Models\restaurant\MenuItem;
use App\Enums\UserStatus;
use App\Enums\OrderStatus;
use App\Models\restaurant\Restaurant;
use App\Enums\WaiterStatus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Enums\RestaurantStatus;
use App\Helpers\Support;
use Yajra\Datatables\Datatables;
use App\Imports\RestaurantImport;
// use Spatie\Permission\Models\Role;
use App\Http\Services\restaurant\DepositService;
use Spatie\MediaLibrary\Models\Media;
use App\Http\Requests\Restaurant\RestaurantRequest;
use App\Http\Controllers\restaurant\BackendController;
use App\Http\Requests\RestaurantStoreRequest;
use App\Models\Inventory\Location;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use File;
use Response;
use DB;



class RestaurantBackendController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['siteTitle'] = 'Restaurants';
        //  $this->middleware('license-activate');
        // $this->middleware(['permission:restaurants'])->only('index');
        // $this->middleware(['permission:restaurants_create'])->only('create', 'store');
        // $this->middleware(['permission:restaurants_edit'])->only('edit', 'update');
        // $this->middleware(['permission:restaurants_delete'])->only('destroy');
        // $this->middleware(['permission:restaurants_show'])->only('show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
           $index=Restaurant::all();
           $location=Location::all();
            return view('restaurant.restaurant.index', compact('index','location'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id']=auth()->user()->id;
        $restaurant= Restaurant::create($data);
        Toastr::success('New Restaurant Created Successfully','Success');
        return redirect(route('restaurants.index'));
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

        $data=Restaurant::find($id);
        $location=Location::all();
         return view('restaurant.restaurant.index', compact('data','location','id'));

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

        $restaurant=Restaurant::find($id);

        $data = $request->all();
        $restaurant->update($data);
        Toastr::success('Restaurant Updated Successfully','Success');
        return redirect(route('restaurants.index'));
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Restaurant::find($id)->delete();
        Toastr::success('Restaurant Deleted Successfully','Success');
        return redirect(route('restaurants.index'));
    }

    public function getRestaurant(Request $request)
    {
        if (request()->ajax()) {
            $queryArray = [];
            if (!empty($request->status) && (int) $request->status) {
                $queryArray['status'] = $request->status;
            }

            if (!empty($request->applied)) {
                $queryArray['applied'] = $request->applied;
            }

            if (!blank($queryArray)) {
                $restaurants = Restaurant::where($queryArray)->restaurantowner()->descending()->select();
                
            } else {
                $restaurants = Restaurant::restaurantowner()->descending()->select();
            }

            $i = 0;
            return Datatables::of($restaurants)
                ->addColumn('action', function ($restaurant) {
                    $retAction = '';

                    // if (auth()->user()->can('restaurants_show')) {
                    //     $retAction .= '<a href="' . route('restaurants.show', $restaurant) . '" class="btn btn-sm btn-icon float-left btn-info mr-2" data-toggle="tooltip" data-placement="top" title="View"> <i class="far fa-eye"></i></a>';
                    // }

                    // if (auth()->user()->can('restaurants_edit')) {
                    //     $retAction .= '<a href="' . route('restaurants.edit', $restaurant) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="far fa-edit"></i></a>';
                    // }

                    // if (auth()->user()->can('restaurants_delete')) {
                    //     $retAction .= '<form class="float-left pl-2" action="' . route('restaurants.destroy', $restaurant) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button></form>';
                    // }

                 
                        $retAction .= '<a href="' . route('restaurants.show', $restaurant->id) . '" class="btn btn-sm btn-icon float-left btn-info mr-2" data-toggle="tooltip" data-placement="top" title="View"> <i class="lar la-eye"></i></a>';
                        $retAction .= '<a href="' . route('restaurants.edit', $restaurant->id) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="lar la-edit"></i></a>';
                        $retAction .= '<form class="float-left pl-2" action="' . route('restaurants.destroy', $restaurant->id) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="la la-trash"></i></button></form>';
                 

                    return $retAction;
                })
                ->editColumn('user_id', function ($restaurant) {
                    return Str::limit($restaurant->user->name ?? null, 20);
                })
                ->editColumn('status', function ($restaurant) {
                    return ($restaurant->status == 5 ? trans('statuses.' . Status::ACTIVE) : trans('statuses.' . Status::INACTIVE));
                })
                ->editColumn('waiter_status', function ($restaurant) {
                    return ($restaurant->waiter_status == 5 ? trans('waiter_statuses.' . WaiterStatus::ACTIVE) : trans('waiter_statuses.' . WaiterStatus::INACTIVE));
                })
                ->editColumn('id', function ($restaurant) use (&$i) {
                    $i++;
                    return $i;
                })
                ->make(true);
        }
    }

    public function getMenuItem(Request $request)
    {
        if (request()->ajax()) {
            $queryArray = [];
            if (!empty($request->restaurant_id) && (int) $request->restaurant_id) {
                $queryArray['restaurant_id'] = $request->restaurant_id;
            }

            if (!blank($queryArray)) {
                $menuItems = MenuItem::owner()->with('categories')->where($queryArray)->descending()->select();
            } else {
                $menuItems = MenuItem::owner()->with('categories')->descending()->select();
            }

            $i = 0;
            return Datatables::of($menuItems)
                ->addColumn('action', function ($menuItem) {
                    $retAction = '';

                    if (auth()->user()->can('menu-items_show')) {

                        $retAction .= '<a href="' . route('menu-items.modify', $menuItem) . '" class="btn btn-sm btn-icon float-left btn-success mr-2" data-toggle="tooltip" data-placement="top" title="Add Variation/Option"> <i class="far fa-list-alt"></i></a>';

                        $retAction .= '<a href="' . route('menu-items.show', $menuItem) . '" class="btn btn-sm btn-icon mr-2  float-left btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></a>';
                    }

                    if (auth()->user()->can('menu-items_edit')) {
                        $retAction .= '<a href="' . route('menu-items.edit', $menuItem) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="far fa-edit"></i></a>';
                    }

                    if (auth()->user()->can('menu-items_delete')) {
                        $retAction .= '<form class="float-left pl-2" action="' . route('menu-items.destroy', $menuItem) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"> <i class="fa fa-trash"></i></button></form>';
                    }

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
                    $col = '<p class="p-0 m-0">' . Str::limit($menuItem->name, 20) . '</p>';
                    $col .= '<small class="text-muted">' . Str::limit($menuItem->description, 20) . '</small>';
                    return $col;
                })
                ->editColumn('status', function ($menuItem) {
                    return trans('menu_item_statuses.' . $menuItem->status) ?? trans('menu_item_statuses.' . MenuItemStatus::INACTIVE);
                })
                ->editColumn('created_at', function ($menuItem) {
                    return $menuItem->created_at->diffForHumans();
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }
    }

    public function restaurantStore(RestaurantStoreRequest $request)
    {

        $restaurant                  = new Restaurant;
        $restaurant->user_id         = auth()->id();
        $restaurant->name            = $request->name;
        $restaurant->description     = $request->description;
        $restaurant->delivery_charge = empty($request->delivery_charge) ? 0 : $request->delivery_charge;
        $restaurant->lat             = $request->lat;
        $restaurant->long            = $request->long;
        $restaurant->opening_time    = date('H:i:s', strtotime($request->opening_time));
        $restaurant->closing_time    = date('H:i:s', strtotime($request->closing_time));
        $restaurant->address         = $request->address;
        $restaurant->current_status  = $request->current_status;
        $restaurant->waiter_status   = $request->waiter_status;
        $restaurant->delivery_status = $request->delivery_status;
        $restaurant->pickup_status   = $request->pickup_status;
        $restaurant->table_status    = $request->table_status;
        $restaurant->status          = RestaurantStatus::INACTIVE;
        $restaurant->applied         = true;
        $restaurant->save();
        $restaurant->cuisines()->sync($request->get('cuisines'));

        //Store Image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $restaurant->addMediaFromRequest('image')->toMediaCollection('restaurant');
        }
        if ($request->hasFile('restaurant_logo') && $request->file('restaurant_logo')->isValid()) {
            $restaurant->addMediaFromRequest('restaurant_logo')->toMediaCollection('restaurant_logo');
        }
        return redirect(route('restaurants.index'))->withSuccess('The data inserted successfully.');
    }

    public function restaurantEdit($id)
    {
        $this->data['restaurant'] = Restaurant::restaurantowner()->findOrFail($id);
        $this->data['cuisines']         = Cuisine::where(['status' => Status::ACTIVE])->get();
        $this->data['restaurant_cuisines'] =  $this->data['restaurant']->cuisines()->pluck('id')->toArray();
        return view('restaurant.restaurantEdit', $this->data);
    }

    public function restaurantUpdate(RestaurantStoreRequest $request, $id)
    {
        $restaurant = Restaurant::restaurantowner()->findOrFail($id);

        $restaurant->user_id         = auth()->id();
        $restaurant->name            = $request->name;
        $restaurant->description     = $request->description;
        $restaurant->delivery_charge = empty($request->delivery_charge) ? 0 : $request->delivery_charge;
        $restaurant->lat             = $request->lat;
        $restaurant->long            = $request->long;
        $restaurant->opening_time    = date('H:i:s', strtotime($request->opening_time));
        $restaurant->closing_time    = date('H:i:s', strtotime($request->closing_time));
        $restaurant->address         = $request->address;
        $restaurant->current_status  = $request->current_status;
        $restaurant->waiter_status   = $request->waiter_status;
        $restaurant->delivery_status = $request->delivery_status;
        $restaurant->pickup_status   = $request->pickup_status;
        $restaurant->table_status    = $request->table_status;
        $restaurant->applied         = true;
        $restaurant->save();
        $restaurant->cuisines()->sync($request->get('cuisines'));

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $this->deleteMedia('image', $restaurant->id);
            $restaurant->addMediaFromRequest('image')->toMediaCollection('restaurant');
        }
        if ($request->hasFile('restaurant_logo') && $request->file('restaurant_logo')->isValid()) {
            $this->deleteMedia('restaurant_logo', $restaurant->id);
            $restaurant->addMediaFromRequest('restaurant_logo')->toMediaCollection('restaurant_logo');
        }
        return redirect(route('restaurants.index'))->withSuccess('The data updated successfully.');
    }

    public function deleteMedia($mediaName, $mediaId)
    {
        $media = Media::where([
            'collection_name' => $mediaName,
            'model_id' => $mediaId,
            'model_type' => Restaurant::class,
        ])->first();

        if ($media) {
            $media->delete();
        }
    }

    public function fileImportExport()
    {
        if (auth()->user()->myrole == 1) {
            return view('restaurant.import.restaurantImport');
        }
        return view('errors.403');
    }

    public function fileExport()
    {
        $filepath = public_path('assets\sample\restaurantImportSample.xlsx');
        return Response::download($filepath); 
    }




    public function fileImport(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);
        try {
            $import = new RestaurantImport();
            $import->import($request->file('file'));
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $importErrors = [];
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
                $importErrors[$failure->row()][] = $failure->errors()[0];
            }
            return back()->with('importErrors', $importErrors);
        }

        return back()->withSuccess('The Data Inserted Successfully');
    }
}
