<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\FieldStaff;
use App\Models\Inventory\GoodReallocation;
use App\Models\Inventory\Inventory;
use App\Models\Logistic\Truck;
use Illuminate\Http\Request;

class GoodReallocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $inventory= Inventory::all();
        $staff=FieldStaff::all();
        $truck=Truck::all();
        $reallocation= GoodReallocation::all();
       return view('inventory.good_reallocation',compact('reallocation','inventory','staff','truck'));
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

        $data = $request->all();
        $data['added_by']=auth()->user()->id;

        $inv=Inventory::where('id',$request->item_id)->first();

        if(($request->quantity <= $inv->quantity)){  
  
        $reallocation=GoodReallocation::create($data);
       
        return redirect(route('good_reallocation.index'))->with(['success'=>'Good Reallocation  Created Successfully']);
        }

        else{
            return redirect(route('good_reallocation.index'))->with(['error'=>'You have exceeded the Quantity']);
        }
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
        $inventory= Inventory::all();
        $staff=FieldStaff::all();
        $truck=Truck::all();
        $data= GoodReallocation::find($id);
       return view('inventory.good_reallocation',compact('data','inventory','staff','truck','id'));
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
        $reallocation =GoodReallocation::find($id);

        $data = $request->all();
        $data['added_by']=auth()->user()->id;

        $inv=Inventory::where('id',$request->item_id)->first();

        if(($request->quantity <= $inv->quantity)){  
  
        $reallocation->update($data);
       
        return redirect(route('good_reallocation.index'))->with(['success'=>'Good Reallocation  Updated Successfully']);
        }

        else{
            return redirect(route('good_reallocation.index'))->with(['error'=>'You have exceeded the Quantity']);
        }
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
        $reallocation =  GoodReallocation::find($id);
        $reallocation->delete();

        return redirect(route('good_reallocation.index'))->with(['success'=>'Good Reallocation  Deleted Successfully']);
    }
}
