<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Member\Member;
use App\Models\Member\MembershipFee;
use App\Models\Member\MembershipPayment;
use App\Models\Cards\Cards;
use App\Models\Cards\CardAssignment;
use App\Models\Member\Charge;
use Illuminate\Http\Request;

class CardPrintingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $member = Member::all()->where('card_id','!=',null);

        return view('members.card_printing',compact('member'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $member = Member::find(14);
        $card = Cards::find($member->card_id)->reference_no;

        return view('members.card_preview',compact('member','card'));
    
    
    }

    public function print(Request $request)
    {
        //
        $member = Member::find(14);
        $card = Cards::find($member->card_id)->reference_no;

        return view('members.card_preview',compact('member','card'));
    
    
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
        $member = Member::find($id);
        $card = Cards::find($member->card_id)->reference_no;

        return view('members.card_preview',compact('member','card','id'));
    }

    public function print_front($id){
        $member = Member::find($id);
        $card = Cards::find($member->card_id)->reference_no;

        return view('members.print_front',compact('member','card'));

    }

    public function print_back($id){
        $member = Member::find($id);
        $card = Cards::find($member->card_id)->reference_no;

        return view('members.print_back',compact('member','card'));

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
        $payment = MembershipPayment::find($id);

        $invoice = MembershipFee::where('reference_no',$payment->reference_no)->get()->first();

        $invoice = MembershipFee::where('reference_no',$payment->reference_no)->update(['due_amount'=>$invoice->due_amount-$payment->amount,'status'=>1]);

        //start catd asignment

        $last_card_id = Cards::all()->last();
        if(!empty($last_card_id)){
            $reference_no = $last_card_id->id + 1;
        }else{
            $reference_no = 0;
        }


        $card_data['reference_no'] = "DCG-M-".sprintf('%04d',$reference_no);
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
