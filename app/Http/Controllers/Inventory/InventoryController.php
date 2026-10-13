<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Inventory;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
class InventoryController extends Controller
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
      
        return view('inventory.inventory',compact('inventory'));
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
        $inventory = Inventory::create($data);
        Toastr::success('Inventory Created Successfully','Success');      
        return redirect(route('inventory.index'));
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
        $data =  Inventory::find($id);
        return view('inventory.inventory',compact('data','id'));
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
        $inventory =  Inventory::find($id);
        $data=$request->post();
        $data['added_by']=auth()->user()->id;
        $inventory->update($data);

        Toastr::success('Inventory Updated Successfully','Success');
        return redirect(route('inventory.index'));
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
        $inventory =  Inventory::find($id);
        $inventory->delete();
 
        Toastr::success('Inventory Deleted Successfully','Success');
        return redirect(route('inventory.index'));
    }
}
