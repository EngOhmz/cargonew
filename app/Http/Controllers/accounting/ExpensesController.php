<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;


use App\Models\Accounting\GroupAccount;
use App\Models\Accounting\AccountCodes;
use App\Models\Accounting\Expenses;
use Illuminate\Http\Request;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\Accounts;
use App\Models\Accounting\Transaction;
use App\Models\Payments\Currency;
use App\Models\Payments\Payment_methodes;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\View;


class ExpensesController extends Controller
{
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payment_method = Payment_methodes::all();
      $expense = Expenses::all();
      $currency = Currency::all();
 $bank_accounts=AccountCodes::where('account_group','Cash and Cash Equivalent')->orwhere('account_name','Payables')->get() ;
     $chart_of_accounts =AccountCodes::where('account_group','!=','Cash and Cash Equivalent')->get() ;
       
          $group_account = GroupAccount::all();
        return view('accounting.expenses.data', compact('expense','group_account','chart_of_accounts','payment_method','bank_accounts','currency'));
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

            $expenses = new Expenses();
            $expenses->name = $request->name;
             $expenses->type='Expenses';
       $expenses->amount = $request->amount ;
         $expenses->date  = $request->date  ;
         $expenses->account_id  = $request->account_id  ;
             $expenses->bank_id  = $request->bank_id ;
             $expenses->notes  = $request->notes ;
             $expenses->reference  = $request->reference ;
             $expenses->status  = '0' ;
             $expenses->exchange_code =   $request->exchange_code;
             $expenses->exchange_rate=  $request->exchange_rate;
             $random = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(4/strlen($x)) )),1,4);
             $expenses->trans_id = "TRANS_EXP_".$random;
             $expenses->added_by = auth()->user()->id;
             $expenses->payment_method =  $request->payment_method;
             $expenses->save();

        
          Toastr::success('Expenses Added Successfully','Success');
            return redirect('expenses');
        }
   

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
       $data= Expenses::find($id);


 $bank_accounts=AccountCodes::where('account_group','Cash and Cash Equivalent')->orwhere('account_name','Payables')->get() ;
     $chart_of_accounts =AccountCodes::where('account_group','!=','Cash and Cash Equivalent')->get() ;
     $currency = Currency::all();
     $payment_method = Payment_methodes::all();
            $group_account = GroupAccount::all();
        return View::make('accounting.expenses.data', compact('data','currency','group_account','payment_method','id','chart_of_accounts','bank_accounts'))->render();
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
       
          $expenses= Expenses::find($id);
            $expenses->name = $request->name;
             $expenses->type='Expenses';
       $expenses->amount = $request->amount ;
         $expenses->date  = $request->date  ;
         $expenses->account_id  = $request->account_id  ;
             $expenses->bank_id  = $request->bank_id ;
             $expenses->notes  = $request->notes ;
             $expenses->reference  = $request->reference ;
             $expenses->exchange_code =   $request->exchange_code;
             $expenses->exchange_rate=  $request->exchange_rate;
             $expenses->added_by = auth()->user()->id;
             $expenses->payment_method =  $request->payment_method;
            $expenses->save();

            Toastr::success('Expenses Updated Successfully','Success');
        return redirect('expenses');
     
 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        Expenses::destroy($id);
        Toastr::success('Expenses Deleted Successfully','Success');
        return redirect('expenses');
    }

    public function approve($id)
    {
        //
        $expenses= Expenses::find($id);
        $data['status'] = 1;
        $expenses->update($data);
   
   if( $expenses->refill_id == NULL){
        $journal = new JournalEntry();
        $journal->account_id =    $expenses->account_id;
       $date = explode('-',  $expenses->date);
        $journal->date = $expenses->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'expense_payment';
        $journal->name = 'Expense Payment';
        $journal->payment_id=    $expenses->id;
        $journal->notes= 'Expense Payment with reference ' .$expenses->reference;
        $journal->debit =   $expenses->amount ;
        $journal->added_by=auth()->user()->id;
        $journal->save();

         $journal = new JournalEntry();
        $journal->account_id = $expenses->bank_id;
        $date = explode('-',  $expenses->date);
        $journal->date = $expenses->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'expense_payment';
        $journal->name = 'Expense Payment';
        $journal->credit =    $expenses->amount;
        $journal->payment_id=    $expenses->id;
        $journal->notes= 'Expense Payment with reference ' .$expenses->reference;
        $journal->added_by=auth()->user()->id;
        $journal->save();



}

 else {
        $journal = new JournalEntry();
        $journal->account_id =    $expenses->account_id;
      $date = explode('-',  $expenses->date);
        $journal->date = $expenses->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
         $journal->transaction_type = 'fuel';
              $journal->name = 'Fuel Refill';
             $journal->payment_id=    $expenses->refill_id;
             $journal->notes= 'Fuel Refill Payment with reference ' .$expenses->reference;
        $journal->debit =   $expenses->amount ;
        $journal->save();

         $journal = new JournalEntry();
        $journal->account_id = $expenses->bank_id;
        $date = explode('-',  $expenses->date);
        $journal->date = $expenses->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'fuel';
              $journal->name = 'Fuel Refill';
             $journal->payment_id=    $expenses->refill_id;
        $journal->credit =    $expenses->amount;
      $journal->notes= 'Fuel Refill Payment with reference ' .$expenses->reference;
        $journal->save();

}


   
$account= Accounts::where('account_id',$expenses->bank_id)->first();

if(!empty($account)){
$balance=$account->balance - $expenses->amount ;
$item_to['balance']=$balance;
$account->update($item_to);
}

else{
  $cr= AccountCodes::where('id',$expenses->bank_id)->first();

     $new['account_id']= $expenses->bank_id;
       $new['account_name']= $cr->account_name;
      $new['balance']= 0-$expenses->amount;
       $new[' exchange_code']='TZS';
        $new['added_by']=auth()->user()->id;
$balance=0-$expenses->amount;
     Accounts::create($new);
}
        
   // save into tbl_transaction

                             $transaction= Transaction::create([
                                'module' => 'Expenses',
                                 'module_id' => $expenses->id,
                               'account_id' => $expenses->bank_id,
                                'code_id' => $expenses->account_id,
                                'name' => 'Expenses Payment with reference ' .$expenses->reference,
                                 'transaction_prefix' => $expenses->reference,
                                'type' => 'Expense',                               
                                'amount' =>$expenses->amount ,
                                'credit' => $expenses->amount,
                                 'total_balance' =>$balance,
                                 'date' => $expenses->date,
                                 'payment_methods_id'=>$expenses->payment_method,
                                   'status' => 'paid' ,
                                'notes' => 'Expenses Payment with reference ' .$expenses->reference ,
                                'added_by' =>auth()->user()->id,
                            ]);
                            




Toastr::success('Expenses Approved Successfully','Success');
return redirect(route('expenses.index'));


        
    }

}
