<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member\Member;
use App\Models\Member\MembershipFee;
use App\Models\Member\MembershipPayments;
use App\Models\Member\Charge;
use App\Models\User;
use App\Models\Company;
use App\Models\Mandotory;
use Carbon\Carbon;

class CooperateMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $members = Company::all()->where('status',0);

        return view('members.manage_cooperate',compact('members'));
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

        $dataCooperate = Company::find($id);

        $mandatory_id = Company::where('id', $id)->value('mandatory_id');

        $dataAttachement  = Mandotory::find($mandatory_id);

        $members = Member::all()->where('cooperate_id', $id);

        

        return view('members.cooperate_details',compact('dataCooperate', 'dataAttachement', 'members'));
    }

    public function mandatory_preview(Request $request){
        $id = $request->id;

        if($request->type == "incorporationCertificate"){

            $data = Mandotory::find($id);
            $filename =  $data->incorporationCertificate;
            return view('members.mandatory_preview',compact('filename'));
        }
        elseif($request->type == "tinCertificate"){

            $data = Mandotory::find($id);
            $filename =  $data->tinCertificate;
            return view('members.mandatory_preview',compact('filename'));
        }
        elseif($request->type == "businessLicense"){

            $data = Mandotory::find($id);
            $filename =  $data->businessLicense;
            return view('members.mandatory_preview',compact('filename'));
        }
        elseif($request->type == "organizationProfile"){

            $data = Mandotory::find($id);
            $filename =  $data->organizationProfile;
            return view('members.mandatory_preview',compact('filename'));
        }
        elseif($request->type == "membership"){

            $data = Mandotory::find($id);
            $filename =  $data->membership;
            return view('members.mandatory_preview',compact('filename'));
        }
        // else{
        //     return 0;
        // }
                      
        
                  
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

        $company = Company::find($id);

        

        // for cooperate members membership type = single (id=2), fee_type(development_fee= id= 1, subscription_fee= id= 2)

        $membership_fee = Charge::where('membership_type', 2)->get()->first();
        $no_member = Member::where('cooperate_id', $company->id)->count();

         $total_development_fee = $membership_fee->development_fee * $no_member;
         $total_subscription_fee = $membership_fee->subscription_fee * $no_member;

         


         $data['company_id'] = $company->id;
         $data['reference_no'] = 1;
         $data['fee_type'] = 1;
         $data['amount'] = $total_development_fee;
         $data['due_amount'] = $total_development_fee;
         $data['status'] = 0;
         $data['date'] = Carbon::now()->format('Y-m-d');
         $data['due_date'] = Carbon::now()->addDays(10)->format('Y-m-d');

         $result = MembershipFee::create($data);
         MembershipFee::find($result->id)->update(['reference_no'=>'INV-'.$result->id]);
         $data['fee_type'] = 2;
         $data['amount'] = $total_subscription_fee;
         $data['due_amount'] = $total_subscription_fee;
         $result1 = MembershipFee::create($data);
         MembershipFee::find($result1->id)->update(['reference_no'=>'INV-'.$result1->id]);

         User::whereNull('member_id')->where('company_id',$id)->update(['is_active'=>1]);

         $company->status = 1;
         $company->save();


        // foreach($members as $member){

        //     // $member = Member::find($id);
        //     $id2 = $member->id;
        // $membership_fee = Charge::where('membership_type',$member->membership_type)->get()->first();
        //  $development_fee = $membership_fee->development_fee;
        //  $subscription_fee = $membership_fee->subscription_fee;

        //  $data['member_id'] = $id2;
        //  $data['reference_no'] = 1;
        //  $data['fee_type'] = 1;
        //  $data['amount'] = $development_fee;
        //  $data['due_amount'] = $development_fee;
        //  $data['status'] = 0;
        //  $data['date'] = Carbon::now()->format('Y-m-d');

        //  $result = MembershipFee::create($data);
        //  MembershipFee::find($result->id)->update(['reference_no'=>'INV'.$result->id]);
        //  $data['fee_type'] = 2;
        //  $data['amount'] = $subscription_fee;
        //  $data['due_amount'] = $subscription_fee;
        //  $result1 = MembershipFee::create($data);
        //  MembershipFee::find($result->id)->update(['reference_no'=>'INV'.$result1->id]);

        //  User::where('member_id',$id2)->update(['is_active'=>1]);

        //  $member->status = 1;
        //  $member->save();

        // }
        
        return redirect()->back();
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
