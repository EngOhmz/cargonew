<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member\Charge;
use App\Models\Member\ChargeType;
use App\Models\Member\MembershipType;

class ChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $charge = Charge::all();

        $charge_type = ChargeType::all();
        $membership_type = MembershipType::all();

        return view('members.charge',compact('charge','charge_type','membership_type'));
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
        $data  = $request->all();

        $charges = Charge::create($data);

        return redirect(route('manage_charge.index'))->with(['success'=>'charges created successfully']);
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
        $data = Charge::find($id);

        $charge_type = ChargeType::all();
        $membership_type = MembershipType::all();

        return view('members.charge',compact('data','id','charge_type','membership_type'));
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
        $data  = $request->all();

        $charges = Charge::find($id)->update($data);

        return redirect(route('manage_charge.index'))->with(['success'=>'charges updated successfully']);
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


        $charges = Charge::find($id)->delete($data);

        return redirect(route('manage_charge.index'))->with(['success'=>'charges created successfully']);
        //
    }
}
