<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Member\Member;
use App\Models\Member\MembershipFee;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if(!empty(auth()->user()->company_id) ){
            $data = Company::find(auth()->user()->company_id);
        $invoices = MembershipFee::where('company_id',auth()->user()->company_id)->where('due_amount','!=',0)->get();
        return view('dashboard.dashboardCooperate',compact('data','invoices'));
        }
        else{

        $data = Member::find(auth()->user()->member_id);
        $invoices = MembershipFee::where('member_id',auth()->user()->member_id)->where('due_amount','!=',0)->get();
        return view('dashboard.dashboard2',compact('data','invoices'));
        
    }
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index1() {

        $data = Company::find(auth()->user()->company_id);
        $invoices = MembershipFee::where('company_id',auth()->user()->company_id)->where('due_amount','!=',0)->get();
        return view('dashboard.dashboardCooperate',compact('data','invoices'));
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
}
