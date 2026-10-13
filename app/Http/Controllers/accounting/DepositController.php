<?php
namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountCodes;
use App\Models\Accounting\Deposit;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\Accounts;
use App\Models\Accounting\GroupAccount;
use App\Models\Accounting\Transaction;
use App\Models\Payments\Currency;
use App\Models\Payments\Payment_methodes;
use Illuminate\Support\Facades\View;


class DepositController extends Controller
{
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
      $deposit = Deposit::all();
      $payment_method = Payment_methodes::all();
 $bank_accounts=AccountCodes::where('account_group','Cash and Cash Equivalent')->get() ;
     $chart_of_accounts =AccountCodes::where('account_group','!=','Cash and Cash Equivalent')->get() ;
     $currency = Currency::all();
          $group_account = GroupAccount::all();
        return view('accounting.deposit.data', compact('currency','deposit','group_account','payment_method','chart_of_accounts','bank_accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

            $deposit = new Deposit();
            $deposit->name = $request->name;
       $deposit->amount = $request->amount ;
         $deposit->date  = $request->date  ;
         $deposit->account_id  = $request->account_id  ;
             $deposit->bank_id  = $request->bank_id ;
             $deposit->notes  = $request->notes ;
             $deposit->reference  = $request->reference ;
             $deposit->status  = '0' ;
             $deposit->exchange_code =   $request->exchange_code;
             $deposit->exchange_rate=  $request->exchange_rate;
             $random = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(4/strlen($x)) )),1,4);
             $deposit->trans_id = "TRANS_DEP_".$random;
             $deposit->type='Deposit';
             $deposit->added_by = auth()->user()->id;
             $deposit->payment_method =  $request->payment_method;
             $deposit->save();

       
          Toastr::success('Deposit Added Successfully','Success');
            return redirect('deposit');
        }
   

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
       $data= Deposit::find($id);


 $bank_accounts=AccountCodes::where('account_group','Cash and Cash Equivalent')->get() ;
     $chart_of_accounts =AccountCodes::where('account_group','!=','Cash and Cash Equivalent')->get() ;
     $currency = Currency::all();
     $payment_method = Payment_methodes::all();
            $group_account = GroupAccount::all();
        return View::make('accounting.deposit.data', compact('data','currency','group_account','id','payment_method','chart_of_accounts','bank_accounts'))->render();
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
       
          $deposit= Deposit::find($id);
            $deposit->name = $request->name;
       $deposit->amount = $request->amount ;
         $deposit->date  = $request->date  ;
         $deposit->account_id  = $request->account_id  ;
             $deposit->bank_id  = $request->bank_id ;
             $deposit->notes  = $request->notes ;
             $deposit->status  = '0' ;
             $deposit->reference  = $request->reference ;
             $deposit->exchange_code =   $request->exchange_code;
             $deposit->exchange_rate=  $request->exchange_rate;
             $deposit->type='Deposit';
             $deposit->added_by = auth()->user()->id;
             $deposit->payment_method =  $request->payment_method;
            $deposit->save();

            Toastr::success('Deposit Updated Successfully','Success');
        return redirect('deposit');
     
 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        Deposit::destroy($id);
        Toastr::success('Deposit Deleted Successfully','Success');
        return redirect('deposit');
    }

    public function approve($id)
    {
        //
        $deposit= Deposit::find($id);
        $data['status'] = 1;
        $deposit->update($data);

        $journal = new JournalEntry();
        $journal->account_id = $deposit->bank_id;
        $date = explode('-',  $deposit->date);
        $journal->date = $deposit->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'deposit';
        $journal->name = 'Deposit Payment';
        $journal->payment_id =    $deposit->id;
        $journal->notes= 'Deposit Payment with reference ' .$deposit->reference;
        $journal->debit=    $deposit->amount;
        $journal->added_by=auth()->user()->id;
        $journal->save();

        $journal = new JournalEntry();
        $journal->account_id =    $deposit->account_id;
         $date = explode('-',  $deposit->date);
        $journal->date = $deposit->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'deposit';
        $journal->name = 'Deposit Payment';
        $journal->payment_id =    $deposit->id;
        $journal->notes= 'Deposit Payment with transaction id ' .$deposit->reference;
        $journal->credit=   $deposit->amount ;
        $journal->added_by=auth()->user()->id;
        $journal->save();
    
       
        $account= Accounts::where('account_id',$deposit->bank_id)->first();

        if(!empty($account)){
        $balance=$account->balance + $deposit->amount ;
        $item_to['balance']=$balance;
        $account->update($item_to);
        }
        
        else{
          $cr= AccountCodes::where('id',$deposit->bank_id)->first();
        
             $new['account_id']= $deposit->bank_id;
               $new['account_name']= $cr->account_name;
              $new['balance']= $deposit->amount;
               $new[' exchange_code']='TZS';
                $new['added_by']=auth()->user()->id;
        $balance=$deposit->amount;
             Accounts::create($new);
        }
                
           // save into tbl_transaction
        
                                     $transaction= Transaction::create([
                                        'module' => 'Deposit',
                                         'module_id' => $deposit->id,
                                       'account_id' => $deposit->bank_id,
                                        'code_id' => $deposit->account_id,
                                        'name' => 'Deposit Payment with reference ' .$deposit->reference,
                                         'transaction_prefix' => $deposit->reference,
                                        'type' => 'Income',                               
                                        'amount' =>$deposit->amount ,
                                        'credit' => $deposit->amount,
                                         'total_balance' =>$balance,
                                         'date' => $deposit->date,
                                         'payment_methods_id'=>$deposit->payment_method,
                                           'status' => 'paid' ,
                                        'notes' => 'Deposit Payment with reference ' .$deposit->reference ,
                                        'added_by' =>auth()->user()->id,
                                    ]);
                                    
        
        

      
        Toastr::success('Deposit Approved Successfully','Success');
        return redirect(route('deposit.index'));
    }

   
}
