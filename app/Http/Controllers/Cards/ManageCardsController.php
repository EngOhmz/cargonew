<?php
namespace App\Http\Controllers\Cards;

use App\Http\Controllers\Controller;

use App\Models\Cards\Cards;
use App\Models\Accounting\GroupAccount;
use App\Models\Accounting\ClassAccount;
use App\Models\Accounting\AccountCodes;
use App\Models\Accounting\Expenses;
use App\Models\Accounting\Deposit;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Models\Accounting\JournalEntry;
use App\Http\Requests;
use App\Models\Payments\Currency;
use App\Models\Payments\Payment_methodes;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use QrCode;

class ManageCardsController extends Controller
{
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
     $cards = Cards::all();

    //  QrCode::size(20)
    //         ->format('png')
    //         ->generate('ItSolutionStuff.com', public_path('images/qrcode.png'));
     
     return view('cards.manage_cards',compact('cards'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
       $group_account = GroupAccount::all();
        return view('accounting.account_codes.create', compact('group_account'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //$data = $request->all();
        $card_numbers = $request->card_number;
        if(!empty($card_numbers)){
            for($i = 0;$i<$card_numbers; $i++){
                $last_card_id = Cards::all()->last();
                if(!empty($last_card_id)){
                    $reference_no = $last_card_id->id + 1;
                }else{
                    $reference_no = 0;
                }
                
                $data['reference_no'] = "DCG-V-".sprintf('%04d',$reference_no);
                
                $data['type'] = 2;
                $data['added_by'] = auth()->user()->id;
                Cards::create($data);
            }
        }
       

        return redirect(route('manage_cards.index'));

           
          
        }
   

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
       $data= Cards::find($id);



       return view('cards.manage_cards',compact('data','id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $data['added_by'] = auth()->user()->id;
         $cards = Cards::find($id);
         $cards->update($data);

        return redirect(route('manage_cards.index'));

     
 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        $data= Cards::destroy($id);
        
        //Flash::success(trans('general.successfully_deleted'));
        return redirect(route('manage_cards.index'));
    }



   
}
