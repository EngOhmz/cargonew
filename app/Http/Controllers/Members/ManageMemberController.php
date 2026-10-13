<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member\Member;
use App\Models\Member\MembershipFee;
use App\Models\Member\MembershipPayments;
use App\Models\Member\Charge;
use App\Models\User;
use Carbon\Carbon;

class ManageMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $members = Member::all()->where('status',0);

        return view('members.manage_member',compact('members'));
    }

    public function member_list()
    {
        //
        $members = Member::all()->where('status','!=',0);

        return view('members.member_list',compact('members'));
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
        $data = Member::find($id);

        return view('members.member_details',compact('data'));
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
        
        $member = Member::find($id);
        $membership_fee = Charge::where('membership_type',$member->membership_class)->get()->first();
         $development_fee = $membership_fee->development_fee;
         $subscription_fee = $membership_fee->subscription_fee;

         $data['member_id'] = $id;
         $data['reference_no'] = 1;
         $data['fee_type'] = 1;
         $data['amount'] = $development_fee;
         $data['due_amount'] = $development_fee;
         $data['status'] = 0;
         $data['date'] = Carbon::now()->format('Y-m-d');
         $data['due_date'] = Carbon::now()->addDays(10)->format('Y-m-d');

         $result = MembershipFee::create($data);
         MembershipFee::find($result->id)->update(['reference_no'=>'INV-'.$result->id]);
         $data['fee_type'] = 2;
         $data['amount'] = $subscription_fee;
         $data['due_amount'] = $subscription_fee;
         $result1 = MembershipFee::create($data);
         MembershipFee::find($result1->id)->update(['reference_no'=>'INV-'.$result1->id]);

         User::where('member_id',$id)->update(['is_active'=>1]);

         $member->status = 1;
         $member->save();




        return redirect(route('manage_member.index'))->with(['success'=>'Member Aproved Seccessfully']);

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
