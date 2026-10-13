<?php

namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\Tyre\TyreActivity;
use App\Models\Tyre\TyreBrand;
use Illuminate\Http\Request;

class TyreBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $tyre= TyreBrand::all();
      
        return view('tyre.tyre_brand',compact('tyre'));
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

        $data=$request->post();
        $data['added_by']=auth()->user()->id;
        $tyre = TyreBrand::create($data);

        if(!empty($tyre)){
        $activity = TyreActivity::create(
            [ 
                'added_by'=>auth()->user()->id,
                'module_id'=>$tyre->id,
                'module'=>'Tyre Brand',
                'activity'=>"Tyre Items Created",
                'date'=>date('Y-m-d'),
            ]
            );                      
}
 
        return redirect(route('tyre_brand.index'))->with(['success'=>'Tyre Created Successfully']);
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
        $data =  TyreBrand::find($id);
        return view('tyre.tyre_brand',compact('data','id'));
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
        $tyre =  TyreBrand::find($id);
        $data=$request->post();
        $data['added_by']=auth()->user()->id;
        $tyre->update($data);

        if(!empty($tyre)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$tyre->id,
                    'module'=>'Tyre Brand',
                    'activity'=>"Tyre Items Updated",
                    'date'=>date('Y-m-d'),
                ]
                );                      
    }
 
        return redirect(route('tyre_brand.index'))->with(['success'=>'Tyre Updated Successfully']);
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
        $tyre = TyreBrand::find($id);
        $tyre->delete();

        if(!empty($tyre)){
            $activity = TyreActivity::create(
                [ 
                    'added_by'=>auth()->user()->id,
                    'module_id'=>$tyre->id,
                    'module'=>'Tyre Brand',
                    'activity'=>"Tyre Items Deleted",
                    'date'=>date('Y-m-d'),
                ]
                );                      
    }
 
 
        return redirect(route('tyre_brand.index'))->with(['success'=>'Tyre Deleted Successfully']);
    }
}
