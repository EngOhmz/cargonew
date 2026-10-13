<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Member\Member;
use App\Models\Member\MembershipFee;
use App\Models\Member\MembershipPayment;
use App\Models\Cards\Cards;
use App\Models\Cards\CardAssignment;
use App\Models\User;
use App\Models\Member\Charge;
use Illuminate\Http\Request;

class MemberPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $payment = MembershipPayment::all()->where('status',0);

        return view('members.paymentlist',compact('payment'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index1()
    {
        //
        $payment = MembershipPayment::all()->where('status',0);

        return view('members.coopaerate_paymentlist',compact('payment'));
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
        if(!empty(auth()->user()->company_id)){
            $invoice = MembershipFee::find($request->invoice_id);
        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;
        $data['reference_no'] = $invoice->reference_no;
        $data['fee_type'] = $invoice->fee_type;
        $data['amount'] = $request->amount;
        $data['due_amount'] = $invoice->due_amount - $request->amount;
        $data['status'] = 0;
        $data['added_by'] = auth()->user()->id;


        if ($request->hasFile('attachment')) {
            $photo=$request->file('attachment');
            $fileType=$photo->getClientOriginalExtension();
            $fileName=rand(1,1000).date('dmyhis').".".$fileType;
            $attachment=$fileName;
            $photo->move('assets/img/logo', $fileName );
             $data['attachment'] = $attachment;

        }

        $result = MembershipPayment::create($data);
        

        return redirect()->back()->with(['success'=>'Payments for Cooperate Membership Added Successfully Please weit for Apporove']);
    
        }
        else{
            $invoice = MembershipFee::find($request->invoice_id);
        $data = $request->all();
        $data['member_id'] = auth()->user()->member_id;
        $data['reference_no'] = $invoice->reference_no;
        $data['fee_type'] = $invoice->fee_type;
        $data['amount'] = $request->amount;
        $data['due_amount'] = $invoice->due_amount - $request->amount;
        $data['status'] = 0;
        $data['added_by'] = auth()->user()->id;


        if ($request->hasFile('attachment')) {
            $slip=$request->file('attachment');
            $fileType=$slip->getClientOriginalExtension();
            $fileName=rand(1,1000).date('dmyhis').".".$fileType;
           // $attachment=$fileName;
            $slip->move('assets/img/logo',$fileName);
             $data['attachment'] = $fileName;

        }
      

        $result = MembershipPayment::create($data);
        

        return redirect()->back()->with(['success'=>'Payments Added Successfully Please weit for Apporove']);
    
        }

        
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store1(Request $request)
    {
        //

        $invoice = MembershipFee::find($request->invoice_id);
        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;
        $data['reference_no'] = $invoice->reference_no;
        $data['fee_type'] = $invoice->fee_type;
        $data['amount'] = $request->amount;
        $data['due_amount'] = $invoice->due_amount - $request->amount;
        $data['status'] = 0;
        $data['added_by'] = auth()->user()->id;


        if ($request->hasFile('attachment')) {
            $photo=$request->file('attachment');
            $fileType=$photo->getClientOriginalExtension();
            $fileName=rand(1,1000).date('dmyhis').".".$fileType;
            $attachment=$fileName;
            $photo->move('assets/img/logo', $fileName );
             $data['attachment'] = $attachment;

        }

        $result = MembershipPayment::create($data);
        

        return redirect()->back()->with(['success'=>'Payments Added Successfully Please weit for Apporove']);
    
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
       
        $invoice_id = $id;
        $deposits = MembershipPayment::all()->where('member_id',auth()->user()->member_id);

        return view('members.deposit',compact('deposits','invoice_id','id'));
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show1($id)
    {
        //
        $invoice_id = $id;
        $deposits = MembershipPayment::all()->where('member_id',auth()->user()->company_id);

        return view('members.deposit',compact('deposits','invoice_id','id'));
    }

    public function file_preview(Request $request){
      $id = $request->id;
                    
      $data = MembershipPayment::find($id);
      $filename =  $data->attachment;
      return view('members.file_preview',compact('filename'));
                
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
        $development_status = 0;
        $subscription_status = 0;
        $payment = MembershipPayment::find($id);

        $invoice = MembershipFee::where('reference_no',$payment->reference_no)->get()->first();

        if($payment->fee_type == 1){
            if($invoice->amount == $invoice->due_amount){
                if($payment->amount >= $invoice->due_amount/2 ){
                 $development_status = 1;
                 Member::find($payment->member_id)->update(['development_status'=>1]);
                }
             }else{
                 if($payment->amount >= $invoice->due_amount ){
                     $development_status = 1;
                     Member::find($payment->member_id)->update(['development_status'=>1]);
                    }
             }
        }else{
            $invoice_age = \Carbon\Carbon::now()->diffInMonths(\Carbon\Carbon::parse($invoice->date));
            if($invoice->amount == $invoice->due_amount){
                if($payment->amount >= $invoice->due_amount/2 ){
                 $subscription_status = 1;
                 Member::find($payment->member_id)->update(['subscription_status'=>1]);
                }
             }else{
                for($i = 1; $i<13; $i++){
                    if($invoice_age == $i){
                        if($payment->amount >= $invoice->due_amount/$i ){
                            $subscription_status = 1;
                            Member::find($payment->member_id)->update(['subscription_status'=>1]);
                           }
                    }
                  }
             }
 
        }

      

        $invoice = MembershipFee::where('reference_no',$payment->reference_no)->update(['due_amount'=>$invoice->due_amount-$payment->amount,'status'=>1]);

        MembershipPayment::where('reference_no',$payment->reference_no)->update(['status'=>1]);

        
        $member_status = Member::find($payment->member_id);
        //start catd asignment
        
        if($member_status->development_status ==1 && $member_status->subscription_status==1 && $member_status->card_id == null ){

            $last_card_id = Cards::all()->last();
            if(!empty($last_card_id)){
                $reference_no = $last_card_id->id + 1;
            }else{
                $reference_no = 0;
            }
    
    
            $data['reference_no'] = "DCG-M-".sprintf('%04d',$reference_no);
            $data['added_by'] = $payment->member_id;
            $data['type'] = 1;
            $cards = Cards::create($data);
    
    
            if(!empty($cards))
            $card_id = $cards->id;
            $member_id = $payment->member_id;
    
            if(isset($card_id)){
                $data['member_id'] = $member_id;
                $data['cards_id'] = $card_id;
                $data['added_by'] = $payment->member_id;
    
                $assignment  = CardAssignment::create($data);
             }else{
    
                return redirect()->back()->with(['error'=>'No Card available']);
             }
            if(!empty($assignment->id) && $assignment->id > 0){
                Cards::where('id',$card_id)->update(['status'=>2,'owner_id'=>$member_id]);
                Member::find($member_id)->update(['status'=>1,'card_id'=>$card_id]);
    
            }
            return redirect()->back()->with(['success'=>"approved successfully and Card is assigned to member"]);
        }
        else{
            return redirect()->back()->with(['success'=>"approved successfully But Card is not assigned "]);
        }
       
        // card assignment

       
        

        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit1($id)
    {
        //
        $payment = MembershipPayment::find($id);

        $invoice = MembershipFee::where('reference_no',$payment->reference_no)->get()->first();

        $invoice = MembershipFee::where('reference_no',$payment->reference_no)->update(['due_amount'=>$invoice->due_amount-$payment->amount,'status'=>1]);

        $company_id = $payment->company_id;

        $members = Member::all()->where('cooperate_id', $company_id);

        foreach($members as $member){

            //start catd asignment

        $last_card_id = Cards::all()->last();
        if(!empty($last_card_id)){
            $reference_no = $last_card_id->id + 1;
        }else{
            $reference_no = 0;
        }


        $data['reference_no'] = "DCG-M-".sprintf('%04d',$reference_no);
        $data['added_by'] = $member->id;
        $data['type'] = 1;
        $cards = Cards::create($data);

        if(!empty($cards))
        $card_id = $cards->id;
        $member_id = $member->id;

        if(isset($card_id)){
            $data['member_id'] = $member_id;
            $data['cards_id'] = $card_id;
            $data['added_by'] = $member->id;

            $assignment  = CardAssignment::create($data);
         }else{

            return redirect()->back()->with(['error'=>'No Card available']);
         }
        if(!empty($assignment->id) && $assignment->id > 0){
            Cards::where('id',$card_id)->update(['status'=>2,'owner_id'=>$member_id]);
            Member::find($member_id)->update(['status'=>1,'card_id'=>$card_id]);
            User::where('member_id',$member_id)->update(['is_active'=>1]);

        }

        }

        

        // card assignment

        return redirect()->back()->with(['success'=>"approved successfully"]);
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
