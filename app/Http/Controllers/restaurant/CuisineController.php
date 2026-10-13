<?php

namespace App\Http\Controllers\restaurant;

use App\Enums\CategoryStatus;
use App\Enums\Status;
use App\Http\Controllers\restaurant\BackendController;
use App\Http\Requests\Restaurant\CuisineRequest;
use App\Models\restaurant\Category;
use App\Models\restaurant\Cuisine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

class CuisineController extends BackendController
{

    /**
     * Category Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['siteTitle'] = 'Cuisines';

        // $this->middleware(['permission:cuisine'])->only('index');
        // $this->middleware(['permission:cuisine_create'])->only('create', 'store');
        // $this->middleware(['permission:cuisine_edit'])->only('edit', 'update');
        // $this->middleware(['permission:cuisine_delete'])->only('destroy');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->getCuisine($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('restaurant.cuisine.create');
    }

    /**
     * @param CuisineRequest $request
     * @return mixed
     */
    public function store(CuisineRequest $request)
    {
        $cuisine              = new Cuisine;
        $cuisine->name        = $request->name;
        $cuisine->description = $request->description;
        $cuisine->parent_id   = 0;
        $cuisine->depth       = 0;
        $cuisine->left        = 0;
        $cuisine->right       = 0;
        $cuisine->status      = $request->status;
        $cuisine->save();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $cuisine->addMediaFromRequest('image')->toMediaCollection('cuisines');
        }

        return redirect(route('cuisine.index'))->withSuccess('The data inserted successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['cuisine'] = Cuisine::owner()->findOrFail($id);
        return view('restaurant.cuisine.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CuisineRequest $request, $id)
    {
        $cuisine              = Cuisine::owner()->findOrFail($id);
        $cuisine->name        = $request->name;
        $cuisine->description = $request->description;
        $cuisine->parent_id   = 0;
        $cuisine->depth       = 0;
        $cuisine->left        = 0;
        $cuisine->right       = 0;
        $cuisine->save();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $cuisine->media()->delete($id);
            $cuisine->addMediaFromRequest('image')->toMediaCollection('cuisines');
        }

        return redirect(route('cuisine.index'))->withSuccess('The data updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Cuisine::owner()->findOrFail($id)->delete();
        return redirect(route('cuisine.index'))->withSuccess('The data deleted successfully.');
    }

    private function getCuisine($request)
    {
        if (request()->ajax()) {
            $queryArray = [];
            $queryArray['status'] = Status::ACTIVE;
            if ((int) $request->status) {
                $queryArray['status'] = $request->status;
            }
            if ((int) $request->requested) {
                $queryArray['requested'] = $request->requested;
            }

            $cuisines = Cuisine::where($queryArray)->descending()->get();

            $i = 0;
            return Datatables::of($cuisines)
                ->addColumn('image', function ($cuisine) {
                    return '<img alt="' . $cuisine->name . '" src="' . $cuisine->image . '" class="img-thumbnail rounded-circle">';
                })
                ->addColumn('action', function ($cuisine) {
                    $retAction = '';

                    if ($cuisine->action_button) {
                        // if (auth()->user()->can('category_edit')) {
                        //     $retAction .= '<a href="' . route('cuisine.edit', $cuisine) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="far fa-edit"></i></a>';
                        // }

                        // if (auth()->user()->can('category_delete')) {
                        //     $retAction .= '<form class="float-left pl-2" action="' . route('cuisine.destroy', $cuisine) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button></form>';
                        // }
                            $retAction .= '<a href="' . route('cuisine.edit', $cuisine) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="lar la-edit"></i></a>';

                            $retAction .= '<form class="float-left pl-2" action="' . route('cuisine.destroy', $cuisine) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="la la-trash"></i></button></form>';
                        
                    }
                    return $retAction;
                })
                ->editColumn('id', function ($cuisine) use (&$i) {
                    return ++$i;
                })
                ->editColumn('status', function ($cuisine) {
                    return trans('statuses.' . $cuisine->status) ?? trans('statuses.' . CategoryStatus::INACTIVE);
                })
                ->editColumn('created_by', function ($cuisine) {
                    return optional($cuisine->creator)->name;
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }
        return view('restaurant.cuisine.index');
    }
}
