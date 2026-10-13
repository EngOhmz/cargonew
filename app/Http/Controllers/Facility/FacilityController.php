<?php

namespace App\Http\Controllers\Facility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Facility\Facility;
use App\Models\Logistic\Maintainance;
use App\Models\Logistic\Service;
use App\Models\Inventory\FieldStaff;
use App\Models\Inventory\Inventory;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $facility = Facility::all();

        return view('facility.index',compact('facility'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $data['name'] = $request->name;
        $data['location'] = $request->location;
        $data['personel'] = $request->personel;
        $data['added_by'] = auth()->user()->id;

        Facility::create($data);

        return redirect(route('facility.index'));
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
        $data = Facility::find($id);
        $maintain=Maintainance::where('facility',$id)->get();
        $staff = FieldStaff::all();
        $facility=$id;
        $name = Inventory::all();
        $service=Service::where('facility',$id)->get();
        return view('facility.details',compact('data','maintain','staff','facility','name','service'));
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
        $data = Facility::find($id);

        return view('facility.index',compact('data','id'));
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
        $data['name'] = $request->name;
        $data['location'] = $request->location;
        $data['personel'] = $request->personel;
        $data['added_by'] = auth()->user()->id;

        Facility::where('id',$id)->update($data);

        return redirect(route('facility.index'));
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
        $data = Facility::find($id);
        $data->delete();

        return redirect(route('facility.index'));
    }

    public function getMaintainance(Request $request,$id){

        if ($request->ajax()) {
           // $data = Maintainance::all()->where('id',$id);

            $data = Maintainance::all();

            return Datatables::of($data)
                    ->addIndexColumn()
                     ->addColumn('action', function ($row) {
                    //     return   '<a class="btn btn-xs btn-outline-info text-uppercase px-2 rounded"
                    //     href="{{ route("maintainance.edit", '.$row->id.')}}">
                    //     <i class="fa fa-edit"></i>
                    // </a>
                    // {!! Form::open(["route" =>
                    //     ["maintainance.destroy",'.$row->id.'],
                    //     "method" => "delete"]) !!}
                    //     {{ Form::button("<i class="fa fa-trash"></i>", ["type" => "submit", "class" => "btn btn-xs btn-outline-danger text-uppercase px-2 rounded demo4", "title" => "Delete", "onclick" => "return confirm("Are you sure?")"]) }}
                    //     {{ Form::close() }}
                    
                  //  ';
                        return '<button type="button" class="btn btn-xs btn-outline-info"
                        data-toggle="modal" data-target="#appFormModal" onclick="model("'.$row->id.'","edit")"
                        data-id="' . $row->id . '" data-type="edit">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="deleteContract(this)"
                        data-url="' . $row->id. '">
                        <i class="fa fa-trash"></i>
                    </button>';
                    })
        
                    ->editColumn('personel', function ($row) {
                        $data = Facility::find($row->facility);
                        return $data->personel;
                   })
                     ->editColumn('facility', function ($row) {
                        $data = Facility::find($row->facility);
                        return $data->name;
                   })
                     ->editColumn('mechanical', function ($row) {
                        $st=FieldStaff::find($row->mechanical);
                        return $st->name;
                   })
                    ->editColumn('date', function ($row) {

                     return Carbon::parse($row->date)->format('M d, Y');
                   })
                   ->editColumn('status', function ($row) {
                    if($row->status == 0){
                        return '<div class="badge badge-danger badge-shadow">
                        Incomplete</div>';
                    }
                    
                    elseif($row->status == 1){
                      
                      return   '<div class="badge badge-success badge-shadow">
                        Complete</div>';
                    
                    }
                   
                    return Carbon::parse($row->date)->format('M d, Y');
                  })
                 
                   ->rawColumns(['action','status'])
                    ->make(true);
        }
    }

    
    public function getService(Request $request,$id){

        if ($request->ajax()) {
           // $data = Maintainance::all()->where('id',$id);
           $data = Service::all();
           // 

            return Datatables::of($data)
                    ->addIndexColumn()
                     ->addColumn('action', function ($row) {
                    //     return   '<a class="btn btn-xs btn-outline-info text-uppercase px-2 rounded"
                    //     href="{{ route("maintainance.edit", '.$row->id.')}}">
                    //     <i class="fa fa-edit"></i>
                    // </a>
                    // {!! Form::open(["route" =>
                    //     ["maintainance.destroy",'.$row->id.'],
                    //     "method" => "delete"]) !!}
                    //     {{ Form::button("<i class="fa fa-trash"></i>", ["type" => "submit", "class" => "btn btn-xs btn-outline-danger text-uppercase px-2 rounded demo4", "title" => "Delete", "onclick" => "return confirm("Are you sure?")"]) }}
                    //     {{ Form::close() }}
                    
                  //  ';
                        return '<button type="button" class="btn btn-xs btn-outline-info"
                        data-toggle="modal" data-target="#appFormModal" onclick="model("'.$row->id.'","edit")"
                        data-id="' . $row->id . '" data-type="edit">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="deleteContract(this)"
                        data-url="' . $row->id. '">
                        <i class="fa fa-trash"></i>
                    </button>';
                    })
        
                    ->editColumn('personel', function ($row) {
                        $data = Facility::find($row->facility);
                        return $data->personel;
                   })
                     ->editColumn('facility', function ($row) {
                        $data = Facility::find($row->facility);
                        return $data->name;
                   })
                     ->editColumn('mechanical', function ($row) {
                        $st=FieldStaff::find($row->mechanical);
                        return $st->name;
                   })
                    ->editColumn('date', function ($row) {

                     return Carbon::parse($row->date)->format('M d, Y');
                   })
                   ->editColumn('status', function ($row) {
                    if($row->status == 0){
                        return '<div class="badge badge-danger badge-shadow">
                        Incomplete</div>';
                    }
                    
                    elseif($row->status == 1){
                      
                      return   '<div class="badge badge-success badge-shadow">
                        Complete</div>';
                    
                    }
                   
                    return Carbon::parse($row->date)->format('M d, Y');
                  })
                 
                   ->rawColumns(['action','status'])
                    ->make(true);
        }
    }
}
