<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\FieldStaff;
use App\Models\Inventory\GoodDisposal;
use App\Models\Inventory\Inventory;
use Illuminate\Http\Request;

class GoodDisposalController extends Controller
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
        $disposal= GoodDisposal::all();
       return view('inventory.good_disposal',compact('disposal','inventory','staff'));
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
        $disposal= GoodDisposal::create($data);
  
        $q=$inv->quantity -$request->quantity;
        Inventory::where('id',$request->item_id)->update(['quantity' => $q]);
        return redirect(route('good_disposal.index'))->with(['success'=>'Good Disposal Created Successfully']);
        }

        else{
            return redirect(route('good_disposal.index'))->with(['error'=>'You have exceeded the Quantity']);
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
        $data= GoodDisposal::find($id);
       return view('inventory.good_disposal',compact('data','inventory','staff','id'));
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
        $disposal=GoodDisposal::find($id);

        $data = $request->all();
        $data['added_by']=auth()->user()->id;

        $inv=Inventory::where('id',$request->item_id)->first();

        if(($request->quantity <= $inv->quantity)){  
       

        if($disposal->quantity <= $request->quantity){
            $diff=$request->quantity-$disposal->quantity;
            $q=$inv->quantity -$diff;
            Inventory::where('id',$request->item_id)->update(['quantity' => $q]);
        }

        if($disposal->quantity > $request->quantity){
            $diff=$disposal->quantity - $request->quantity;
            $q=$inv->quantity + $diff;
            Inventory::where('id',$request->item_id)->update(['quantity' => $q]);
        }
  
        $disposal->update($data);
       
        return redirect(route('good_disposal.index'))->with(['success'=>'Good Disposal Updated Successfully']);
        }

        else{
            return redirect(route('good_disposal.index'))->with(['error'=>'You have exceeded the Quantity']);
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
        $disposal=GoodDisposal::find($id);
        $disposal->delete();

        return redirect(route('good_disposal.index'))->with(['success'=>'Good Disposal Deleted Successfully']);
    }
}
