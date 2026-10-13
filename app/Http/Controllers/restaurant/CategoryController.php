<?php

namespace App\Http\Controllers\restaurant;

use App\Enums\CategoryStatus;
use App\Enums\Status;
use App\Http\Controllers\restaurant\BackendController;
use App\Http\Requests\Restaurant\CategoryRequest;
use App\Models\restaurant\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

class CategoryController extends BackendController
{

    /**
     * Category Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['siteTitle'] = 'Categories';

        // $this->middleware(['permission:category'])->only('index');
        // $this->middleware(['permission:category_create'])->only('create', 'store');
        // $this->middleware(['permission:category_edit'])->only('edit', 'update');
        // $this->middleware(['permission:category_delete'])->only('destroy');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->getCategory($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('restaurant.category.create');
    }

    /**
     * @param CategoryRequest $request
     * @return mixed
     */
    public function store(CategoryRequest $request)
    {
        $category              = new Category;
        $category->name        = $request->name;
        $category->description = $request->description;
        $category->parent_id   = 0;
        $category->depth       = 0;
        $category->left        = 0;
        $category->right       = 0;
        $category->status      = $request->status;
        $category->save();

        //Store Image Media Libraty Spati
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $category->addMediaFromRequest('image')->toMediaCollection('categories');
        }

        return redirect(route('category.index'))->withSuccess('The data inserted successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['category'] = Category::owner()->findOrFail($id);
        return view('restaurant.category.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $category              = Category::owner()->findOrFail($id);
        $category->name        = $request->name;
        $category->description = $request->description;
        $category->parent_id   = 0;
        $category->depth       = 0;
        $category->left        = 0;
        $category->right       = 0;
        $category->save();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $category->media()->delete($id);
            $category->addMediaFromRequest('image')->toMediaCollection('categories');
        }

        return redirect(route('category.index'))->withSuccess('The data updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::owner()->findOrFail($id)->delete();
        return redirect(route('category.index'))->withSuccess('The data deleted successfully.');
    }

    private function getCategory($request)
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

            $categories = Category::where($queryArray)->descending()->get();

            $i = 0;
            return Datatables::of($categories)
                ->addColumn('image', function ($category) {
                    return '<img alt="' . $category->name . '" src="' . $category->image . '" class="img-thumbnail rounded-circle">';
                })
                ->addColumn('action', function ($category) {
                    $retAction = '';

                    if ($category->action_button) {
                        
                        // if (auth()->user()->can('category_edit')) {
                        //     $retAction .= '<a href="' . route('admin.category.edit', $category) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="far fa-edit"></i></a>';
                        // }

                        // if (auth()->user()->can('category_delete')) {
                        //     $retAction .= '<form class="float-left pl-2" action="' . route('admin.category.destroy', $category) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button></form>';
                        // }
                       
                            $retAction .= '<a href="' . route('category.edit', $category) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="lar la-edit"></i></a>';
                        

                       
                            $retAction .= '<form class="float-left pl-2" action="' . route('category.destroy', $category) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="la la-trash"></i></button></form>';
                        
                    }
                    return $retAction;
                })
                ->editColumn('id', function ($category) use (&$i) {
                    return ++$i;
                })
                ->editColumn('status', function ($category) {
                    return trans('statuses.' . $category->status) ?? trans('statuses.' . CategoryStatus::INACTIVE);
                })
                ->editColumn('created_by', function ($category) {
                    return optional($category->creator)->name;
                })
                ->rawColumns(['image', 'action'])

                ->escapeColumns([])
                ->make(true);

        }
        return view('restaurant.category.index');
    }
}
