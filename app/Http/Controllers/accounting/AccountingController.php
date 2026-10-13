<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;

use App\Models\Accounting\ClassAccount;
use App\Models\Accounting\JournalEntry;
use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Helpers\GeneralHelper;
use App\Models\Borrower;
use App\Traits\Calculate_netProfitTrait2;
use App\Traits\Calculate_netProfitTrait5;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\OtherIncome;
use App\Models\Payroll;
use App\Models\SavingTransaction;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Clickatell\Api\ClickatellHttp;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Accounting\AccountCodes;
use App\Models\Accounting\BankReconciliation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class AccountingController extends Controller
{
  


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trial_balance(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('accounting.accounting.trial_balance',
            compact('start_date',
                'end_date'));
    }
    public function journal(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $account_id=$request->account_id;
        $chart_of_accounts = [];
        foreach (ChartOfAccount::all() as $key) {
            $chart_of_accounts[$key->id] = $key->name;
        }
        if($request->isMethod('post')){
            $data=JournalEntry::where('reversed', 0)->where('account_id', $request->account_id)->whereBetween('date',[$start_date,$end_date])->get();
        }else{
            $data=[];
        }
        return view('accounting.accounting.journal',
            compact('start_date',
                'end_date','chart_of_accounts','data','account_id'));
    }

      use Calculate_netProfitTrait2;
     use Calculate_netProfitTrait5;
    public function ledger(Request $request)
    {
       
        $start_date = $request->start_date;
        $second_date = $request->second_date;
        $end_date = $request->end_date;

  $income = ClassAccount::where('class_type','Income')->get();
           $cost = ClassAccount::where('class_type','Expense')->get();
           $expense= ClassAccount::where('class_type','Expense')->get();


        
              if(!empty($start_date) || !empty($end_date)){
          $net_profit = $this->get_netProfit5($start_date, $second_date,$end_date);
          $net_tax= $this->get_netProfit5($start_date, $second_date,$end_date);
        }
        
else{
     $net_profit ='';    
  $net_tax ='';       
}

        
         $net_p = $this->get_netProfit2();
         $net_t = $this->get_netProfit2();

        return view('accounting.accounting.ledger',
            compact('start_date','second_date','income','expense','end_date',
                'cost' ,'net_profit','net_p' ,'net_tax','net_t'));
    }
    public function create_manual_entry()
    {
       
         $journal =  JournalEntry::all();
        $chart_of_accounts = [];
        foreach (ChartOfAccount::all() as $key) {
            $chart_of_accounts[$key->id] = $key->name;
        }
        return view('accounting.accounting.create_manual_entry',
            compact('chart_of_accounts','journal'));
    }
    public function store_manual_entry(Request $request)
    {
       

        $journal = new JournalEntry();
        $journal->account_id = $request->credit_account_id;
        $date = explode('-', $request->date);
        $journal->date = $request->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'manual_entry';
        $journal->name = $request->name;
        $journal->credit = $request->amount;
        $journal->reference = $request->reference;
        $journal->branch_id =session('branch_id');
        $journal->save();

        $journal = new JournalEntry();
        $journal->account_id = $request->debit_account_id;
        $date = explode('-', $request->date);
        $journal->date = $request->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'manual_entry';
        $journal->name = $request->name;
        $journal->reference = $request->reference;
        $journal->debit = $request->amount;
       $journal->branch_id =session('branch_id');
        $journal->save();
        //Flash::success(trans('general.successfully_saved'));
        //GeneralHelper::audit_trail("Added Journal Manual Entry  with id:" . $journal->id);
    
        return redirect('accounting/manual_entry');
    }


    public function bank_statement(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $account_id=$request->account_id;
        $chart_of_accounts = [];
        foreach (AccountCodes::where('account_group','Cash and Cash Equivalent')->get() as $key) {
            $chart_of_accounts[$key->id] = $key->account_name;
        }
        if($request->isMethod('post')){
            $data=JournalEntry::where('reconcile', 0)->where('account_id', $request->account_id)->whereBetween('date',[$start_date,$end_date])->get();
        }else{
            $data=[];
        }

        if($request->isMethod('post')){
            $open_debit=JournalEntry::where('reconcile', 0)->where('account_id', $request->account_id)->where('date','<', $start_date)->sum('debit');
        }else{
            $open_debit=[];
        }

        if($request->isMethod('post')){
            $open_credit=JournalEntry::where('reconcile', 0)->where('account_id', $request->account_id)->where('date','<', $start_date)->sum('credit');
        }else{
            $open_credit=[];
        }
        return view('accounting.accounting.bank_statement',
            compact('start_date',
                'end_date','chart_of_accounts','data','account_id','open_debit','open_credit'));
    }

    public function bank_reconciliation(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $account_id=$request->account_id;
        $chart_of_accounts = [];
        foreach (AccountCodes::where('account_group','Cash and Cash Equivalent')->get() as $key) {
            $chart_of_accounts[$key->id] = $key->account_name;
        }
        if($request->isMethod('post')){
            $data=JournalEntry::where('reconcile', 0)->where('account_id', $request->account_id)->whereBetween('date',[$start_date,$end_date])->get();
        }else{
            $data=[];
        }

       

        return view('accounting.accounting.bank_reconciliation',
            compact('start_date',
                'end_date','chart_of_accounts','data','account_id'));
    }


    public function save_reconcile(Request $request)
    {

$trans_id= $request->trans_id;

  if(!empty($trans_id)){
    for($i = 0; $i < count($trans_id); $i++){
   if(!empty($trans_id[$i])){
    
             $acc = JournalEntry::find($trans_id[$i]);       
                  $items = array(
                    'name' =>  $acc->name,
                    'account_id' => $acc->account_id,
                     'transaction_type' => $acc->transaction_type,
                    'date' => date('Y-m-d'),
                    'payment_id' => $acc->payment_id,
                    'debit' =>$acc->debit,
                    'credit' =>   $acc->credit,
                    'currency_code' => $acc->currency_code,
                    'notes' => $acc->notes,
                    'added_by' =>  auth()->user()->id);

                    BankReconciliation::create($items);  ;

                    JournalEntry::where('id',$trans_id[$i])->update(['reconcile' => '1']);

                  }
                  }
                }

                Toastr::success('Reconciled Successfully','Success');
                return redirect(route('reconciliation.report'));
}

    public function reconciliation_report()
    {
        //
        $data= BankReconciliation::all();
       return view('accounting.accounting.reconciliation_report',compact('data'));
    }
}
