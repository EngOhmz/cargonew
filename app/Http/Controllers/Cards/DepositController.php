<?php

namespace App\Http\Controllers\Cards;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\URL;

use App\Libraries\MyString;
use App\Models\Cards\Deposit;
use App\Models\Cards\Cards;
use App\Models\Visitors\Visitor;
use App\Models\Visitors\VisitingDetails;

use App\Models\Cards\TemporaryDeposit;


use Pesapal;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $cards = Cards::all()->where('status',2);

        $visitor = VisitingDetails::where('visitor_id',auth()->user()->visitor_id)->get()->first();

        $deposits = Deposit::all()->where('visitor_id',auth()->user()->visitor_id)->where('card_id',$visitor->card_id);

        return view('cards.deposit',compact('deposits'));


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

        $visitor = VisitingDetails::where('visitor_id',auth()->user()->visitor_id)->get()->first();
        $card = Cards::find($visitor->card_id);

           $card = Cards::find($visitor->card_id);
           $data['visitor_id'] = auth()->user()->visitor_id;
           $data['ref_no'] = $card->reference_no;
           $data['card_id'] =$visitor->card_id;
           $data['debit'] = $request->amount;
           $data['status'] = 1;
           $data['added_by'] = auth()->user()->id;

           $deposit = Deposit::create($data);
           $temp_deposit = TemporaryDeposit::create($data);

/*
        $callbacUrl = URL::to('')."/pesapalResonse";

  
        MyString::setEnv('PESAPAL_CONSUMER_KEY','BQRCwbtaVO7uKdxDPQi/HXQ/Lk9oh3Me');
        MyString::setEnv('PESAPAL_CONSUMER_SECRET',"1+qbNhG7qirv0ElpgQ1d0I0ernw=");
       MyString::setEnv('PESAPAL_API_URL','https://demo.pesapal.com/api/PostPesapalDirectOrderV4');

       MyString::setEnv('PESAPAL_CALLBACK_URL',$callbacUrl);
       



       //Pesapal::make_payment("customerfirstname","customerlastname","customerlastname","amount","transaction_id");
          $res=Pesapal::makepayment("samwel","1000","herman","epmnzava@gmail.com","MERCHANT","453f4f4343" ,"transacto","0715438485");
      
          echo  $res;

          */

          return redirect(route('card_deposit.index'));
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

     

        return view('cards.deposit',compact('id'));
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
        $card = Cards::find($request->card_id);
        $data['ref_no'] = 1;
        $data['visitor_id'] = $card->owner_id;
        $data['card_id'] =$request->card_id;
        $data['debit'] = $request->amount;
        $data['status'] = 1;
        $data['added_by'] = auth()->user()->id;

        $temp_deposit = TemporaryDeposit::create($data);

/*
     $callbacUrl = URL::to('')."/pesapalResonse";


     MyString::setEnv('PESAPAL_CONSUMER_KEY','BQRCwbtaVO7uKdxDPQi/HXQ/Lk9oh3Me');
     MyString::setEnv('PESAPAL_CONSUMER_SECRET',"1+qbNhG7qirv0ElpgQ1d0I0ernw=");
    MyString::setEnv('PESAPAL_API_URL','https://demo.pesapal.com/api/PostPesapalDirectOrderV4');

    MyString::setEnv('PESAPAL_CALLBACK_URL',$callbacUrl);
    



    //Pesapal::make_payment("customerfirstname","customerlastname","customerlastname","amount","transaction_id");
       $res=Pesapal::makepayment("samwel","1000","herman","epmnzava@gmail.com","MERCHANT","453f4f4343" ,"transacto","0715438485");
   
       echo  $res;

       */

       return redirect(route('card_deposit.index'));
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
