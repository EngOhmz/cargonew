<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll\SalaryAllowance;
use App\Models\Department;
use App\Models\Payroll\SalaryDeduction;
use App\Models\Payroll\SalaryTemplate;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\SalaryPayment;
use App\Models\Payroll\SalaryPaymentDetails;
use App\Models\Payroll\SalaryPaymentAllowance;
use App\Models\Payroll\SalaryPaymentDeduction;
use App\Models\Payroll\Overtime;
use App\Models\Payroll\AdvanceSalary;
use App\Models\Payroll\EmployeeAward;
use App\Models\Payroll\EmployeeLoan;
use App\Models\Payroll\EmployeeLoanReturn;
use App\Models\Payroll\PayrollActivity;
use App\Models\Payments\Payment_methodes;
use App\Models\Accounting\AccountCodes;
use App\Models\Accounting\Accounts;
use App\Models\Accounting\Transaction;
use App\Models\JournalEntry;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;

use  DateTime;

class MakePaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
       $all_department_info = Department::all();
        return view('payroll.make_payment',compact('all_department_info'));

        
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

        $flag = $request->flag;
        $departments_id = $request->departments_id;
         $payment_month ='';
        $employee_info='';
         $allowance_info='';
        $deduction_info='';
        $overtime_info='';
        $award_info='';
       $advance_salary='';
       $total_hours='';
      $salary_info='';
  $loan_info='';
$salary_loan_info='';

$all_department_info=Department::all();
if (!empty($flag) || !empty($departments_id)) {
    $payment_month = $request->payment_month;    
     $date = new DateTime($payment_month . '-01');
        $start_date = $date->modify('first day of this month')->format('Y-m-d');
        $end_date = $date->modify('last day of this month')->format('Y-m-d');
  
                          $employee_info  = EmployeePayroll::where('department_id',$departments_id)->get();



}

return view('payroll.make_payment',compact('employee_info','flag','payment_month','departments_id','all_department_info','start_date','end_date'));
    }

  public function getPayment($user_id,$departments_id,$payment_month)
    {
        //

        $employee_info='';
         $allowance_info='';
        $deduction_info='';
        $overtime_info='';
        $award_info='';
       $advance_salary='';
       $total_hours='';
      $salary_info='';
 $total_paid_amount='';
       $ttl_deduction='';
      $ttl_allowance='';
      $loan_info='';


$all_department_info=Department::all();
if (!empty($user_id) || !empty($departments_id)) {
        
     $date = new DateTime($payment_month . '-01');
        $start_date = $date->modify('first day of this month')->format('Y-m-d');
        $end_date = $date->modify('last day of this month')->format('Y-m-d');



                 // check payment history by employee id
                    $check_existing_payment = SalaryPayment::all()->where('user_id', $user_id);
               
  
                        // get all salary Template info
                          $employee_info  = EmployeePayroll::where('user_id', $user_id)->first();

                          // get all allowance info by salary template id
                                          $allowance_info =  SalaryAllowance::where('salary_template_id',$employee_info->salary_template_id)->get();
        // get all deduction info by salary template id
                                         $deduction_info = SalaryDeduction::where('salary_template_id',$employee_info->salary_template_id)->get();
                                           // get all overtime info by month and employee id
                                      $overtime_info =Overtime::where('user_id',$user_id)->where('overtime_date','>=', $start_date)->where('overtime_date','<=', $end_date)->get();                                    
        // get all advance salary info by month and employee id
                                    $advance_salary= AdvanceSalary::where('user_id',$user_id)->where('deduct_month', $payment_month)->where('status', '1')->get();
                              $loan_info= EmployeeLoanReturn::where('user_id',$user_id)->where('deduct_month', $payment_month)->where('status', '1')->get();
        // get award info by employee id and payment month
                                  $award_info= EmployeeAward::where('user_id',$user_id)->where('award_date', $payment_month)->get();;
  
                                  $total_hours = '0';

             $all_payment_method = Payment_methodes::all();
           $account_info=AccountCodes::where('account_group','Cash and Cash Equivalent')->get() ;


}

return view('payroll.employee_payment',compact('employee_info','allowance_info','deduction_info','overtime_info','advance_salary','award_info','total_hours','payment_month','departments_id','all_department_info','salary_info','user_id','all_payment_method','account_info','check_existing_payment','loan_info'));
    }

   public function save_payment(Request $request){

   // input data

     $data['user_id']=$request->user_id ;
     $data['payment_month']=$request->payment_month ;
       $data['fine_deduction']=$request->fine_deduction ;
       $data['payment_type']=$request->payment_type ;
       $data['comments']=$request->comments ;
      $data['account_id']=$request->account_id ;
      $data['added_by']=auth()->user()->id ;
            $salary_payment=SalaryPayment::create($data);  ;

    $date = new DateTime($request->payment_month . '-01');
        $start_date = $date->modify('first day of this month')->format('Y-m-d');
        $end_date = $date->modify('last day of this month')->format('Y-m-d');

 $emp_info = User::find($request->user_id);
 $payroll_info  = EmployeePayroll::where('user_id', $request->user_id)->first();
 $paye_info = SalaryDeduction::where('salary_template_id',$payroll_info->salary_template_id)->where('deduction_label','PAYE')->first();
 $nssf_info = SalaryDeduction::where('salary_template_id',$payroll_info->salary_template_id)->where('deduction_label','NSSF')->first();
$basic=SalaryTemplate::where('salary_template_id', $payroll_info->salary_template_id)->first();
$month= date('F Y', strtotime($request->payment_month)) ;

if(!empty( $salary_payment)){ 
$s=AccountCodes::where('account_name','Salaries And Wages')->first();   

          $journal = new JournalEntry();
        $journal->account_id = $s->account_id;
          $journal->user_id=$request->user_id ;
        $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'Salary Payment';
        $journal->debit= $request->payment_amount;
        $journal->payment_id= $salary_payment->id;
         $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "Net Salary Payment to " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();

        $journal = new JournalEntry();
        $journal->account_id =   $request->account_id;;
          $journal->user_id=$request->user_id ;
        $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'Salary Payment';
        $journal->credit= $request->payment_amount;
        $journal->payment_id= $salary_payment->id;
        $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "Net Salary Payment to " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();

        $account= Accounts::where('account_id',$request->account_id)->first();

        if(!empty($account)){
        $balance=$account->balance - $request->payment_amount ;
        $item_to['balance']=$balance;
        $account->update($item_to);
        }
        
        else{
          $cr= AccountCodes::where('id',$request->account_id)->first();
        
             $new['account_id']= $request->account_id;
               $new['account_name']= $cr->account_name;
              $new['balance']= 0-$request->payment_amount;
               $new[' exchange_code']='TZS';
                $new['added_by']=auth()->user()->id;
        $balance=0-$request->payment_amount;
             Accounts::create($new);
        }
                
           // save into tbl_transaction
        
                                     $transaction= Transaction::create([
                                        'module' => 'Salary Payment',
                                         'module_id' => $salary_payment->id,
                                       'account_id' => $request->account_id,
                                        'code_id' => $s->account_id,
                                        'name' => 'Net Salary Payment to ' .$emp_info->name. '  for the month of '.  $month,
                                        'type' => 'Expense',                               
                                        'amount' =>$request->payment_amount ,
                                        'credit' => $request->payment_amount,
                                         'total_balance' =>$balance,
                                         'date' => date('Y-m-d'),
                                         'payment_methods_id'=>$request->payment_type,
                                           'status' => 'paid' ,
                                        'notes' => 'Net Salary Payment to ' .$emp_info->name. '  for the month of '.  $month,
                                        'user_id' => $request->user_id ,
                                        'added_by' =>auth()->user()->id,
                                    ]);
                                    

   }       

 
            if (!empty($paye_info)) {
$salary=AccountCodes::where('account_name','Salaries And Wages')->first();                 
          $journal = new JournalEntry();
        $journal->account_id = $salary->id;
          $journal->user_id=$request->user_id ;
       $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'PAYE Payment';
        $journal->debit= $paye_info->deduction_value ;
        $journal->payment_id= $salary_payment->id;
        $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "PAYE Payment from " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();

 $paye=AccountCodes::where('account_name','PAYE')->first();;
   
         $journal = new JournalEntry();
        $journal->account_id = $paye->id;
          $journal->user_id=$request->user_id ;
      $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'PAYE Payment';
        $journal->credit= $paye_info->deduction_value ;
        $journal->payment_id= $salary_payment->id;
       $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "PAYE Payment from " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
          
}


 if (!empty($nssf_info)) {
$salary=AccountCodes::where('account_name','Salaries And Wages')->first();                 
          $journal = new JournalEntry();
        $journal->account_id = $salary->id;
          $journal->user_id=$request->user_id ;
         $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'NSSF Payment';
        $journal->debit= $nssf_info->deduction_value ;
        $journal->payment_id= $salary_payment->id;
         $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "NSSF Payment from " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
          
     $nssf=AccountCodes::where('account_name','NSSF')->first();;
   
         $journal = new JournalEntry();
        $journal->account_id = $nssf->id;
          $journal->user_id=$request->user_id ;
     $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'NSSF Payment';
        $journal->credit= $nssf_info->deduction_value ;
        $journal->payment_id= $salary_payment->id;
        $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "NSSF Payment from " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();  


$nssf_e=AccountCodes::where('account_name','NSSF - Employer Contribution')->first();   
                   
          $journal = new JournalEntry();
        $journal->account_id =$nssf_e->id;
          $journal->user_id=$request->user_id ;
       $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'NSSF - Employer Contribution Payment';
        $journal->debit= $nssf_info->deduction_value ;
        $journal->payment_id= $salary_payment->id;
           $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "NSSF - Employer Contribution Payment from " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();

$nssf=AccountCodes::where('account_name','NSSF')->first();;
   
         $journal = new JournalEntry();
        $journal->account_id = $nssf->id;
          $journal->user_id=$request->user_id ;
       $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'NSSF Payment';
        $journal->credit= $nssf_info->deduction_value ;
        $journal->payment_id= $salary_payment->id;
          $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "NSSF Payment from " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();  

          
    }   

$wcf_e=AccountCodes::where('account_name','WCF contribution')->first();;
 
           $journal = new JournalEntry();
        $journal->account_id =$wcf_e->id;
          $journal->user_id=$request->user_id ;
      $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'WCF Contribution Payment';
        $journal->debit=  0.006 * $basic->basic_salary ;
        $journal->payment_id= $salary_payment->id;
           $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "WCF Contribution Payment from " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();


 $wcf=AccountCodes::where('account_name','WCF')->first();;
   
                 $journal = new JournalEntry();
        $journal->account_id =$wcf->id;
          $journal->user_id=$request->user_id ;
       $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'WCF Payment';
        $journal->credit=  0.006 * $basic->basic_salary ;
        $journal->payment_id= $salary_payment->id;
             $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "WCF  Payment from " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
          
$nhif_e=AccountCodes::where('account_name','NHIF - Heath Insurance Expense')->first();;

        $journal = new JournalEntry();
        $journal->account_id =$nhif_e->id;
          $journal->user_id=$request->user_id ;
      $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'NHIF - Heath Insurance Expense Payment';
        $journal->debit=  0.03 * $basic->basic_salary ;
        $journal->payment_id= $salary_payment->id;
             $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "NHIF - Heath Insurance Expense Payment from " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
         

 $nhif=AccountCodes::where('account_name','NHIF')->first();;

        $journal = new JournalEntry();
        $journal->account_id =$nhif->id;
          $journal->user_id=$request->user_id ;
      $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'NHIF Payment';
        $journal->credit=  0.03 * $basic->basic_salary ;
        $journal->payment_id= $salary_payment->id;
          $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "NHIF  Payment from " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
   


// get all allowance info by salary template id
        if (!empty($basic->salary_template_id)) {
            $salary_payment_details_label[] = 'Salary Grade ';
            $salary_payment_details_value[] = $basic->salary_grade;

            //$salary_payment_details_label[] = 'Basic Salary - ' .$basic->salary_grade;
            $salary_payment_details_label[] = 'Basic Salary ';
            $salary_payment_details_value[] = $basic->basic_salary;

          
// ************ Save all allwance info **********
         $allowance_info = SalaryAllowance::where('salary_template_id', $payroll_info->salary_template_id)->get();
            if (!empty($allowance_info)) {
                foreach ($allowance_info as $v_allowance_info) {
                    $aldata['salary_payment_id'] = $salary_payment->id;
                    $aldata['salary_payment_allowance_label'] = $v_allowance_info->allowance_label;
                    $aldata['salary_payment_allowance_value'] = $v_allowance_info->allowance_value;

                     $salary_allowance = SalaryPaymentAllowance::create($aldata);
                }
            }
// get all deduction info by salary template id
// ************ Save all deduction info **********
            $deduction_info = SalaryDeduction::where('salary_template_id', $payroll_info->salary_template_id)->get();
            if (!empty($deduction_info)) {
                foreach ($deduction_info as $v_deduction_info) {
                    $salary_payment_deduction_label[] = $v_deduction_info->deduction_label;
                    $salary_payment_deduction_value[] = $v_deduction_info->deduction_value;
                }
            }


// ************ Save all Overtime info **********
// get all overtime info by month and employee id
            $overtime_info =Overtime::where('user_id',$request->user_id)->where('overtime_date','>=', $start_date)->where('overtime_date','<=', $end_date)->where('status', '1')->get();
                $overtime_ttl=0;
               if (!empty($overtime_info[0])) {
                foreach ($overtime_info as $v_overtime_info) {
                    $overtime_ttl +=  $v_overtime_info->overtime_amount;
                       Overtime::where('id', $v_overtime_info->id)->update(['status' => '3']); 
                }
              $salary_payment_details_label[] = 'Overtime Amount';
            $salary_payment_details_value[] = $overtime_ttl;



            $over=AccountCodes::where('account_name','Overtime')->first();   
         if (!empty($over)) {
          $journal = new JournalEntry();
        $journal->account_id = $over->id;
          $journal->user_id=$request->user_id ;
        $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'Overtime Salary Payment';
        $journal->debit= $overtime_ttl;
        $journal->payment_id= $salary_payment->id;
             $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "Overtime Salary Payment to " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
     }     
                 
if (!empty($request->account_id)) {
       $journal = new JournalEntry();
        $journal->account_id =   $request->account_id;;
          $journal->user_id=$request->user_id ;
        $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'Overtime  Salary Payment';
        $journal->credit=$overtime_ttl;
        $journal->payment_id= $salary_payment->id;
            $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "Overtime  Salary Payment to " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
 }         
            }
}       
// ************ Save all Advance Salary info **********
// get all advance salary info by month and employee id
        $advance_salary= AdvanceSalary::where('user_id',$request->user_id)->where('deduct_month', $request->payment_month)->where('status', '1')->first();
         $total_advance=0;
             if (!empty($advance_salary)) {
                    $total_advance = $advance_salary->advance_amount;
                AdvanceSalary::where('id', $advance_salary->id)->update(['status' => '3']); 
               
              $salary_payment_deduction_label[] = 'Advance Amount';
            $salary_payment_deduction_value[] =   $total_advance;
          
            $adv=AccountCodes::where('account_name','Advance Salary')->first();   
          $journal = new JournalEntry();
        $journal->account_id = $adv->id;
          $journal->user_id=$request->user_id ;
        $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'Advance Salary Payment';
        $journal->debit= $total_advance;
        $journal->payment_id= $salary_payment->id;
            $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "Advance Salary Payment to " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
          
         $journal = new JournalEntry();
        $journal->account_id =   $request->account_id;;
          $journal->user_id=$request->user_id ;
        $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'Advance Salary Payment';
        $journal->credit= $total_advance;
        $journal->payment_id= $salary_payment->id;
            $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "Net Salary Payment to " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
          

        
            }
     

// ************ Save all Advance Salary info **********
// get all advance salary info by month and employee id
        $loan_info= EmployeeLoanReturn::where('user_id',$request->user_id)->where('deduct_month', $request->payment_month)->where('status', '1')->first();
         $total_loan=0;
             if (!empty($loan_info)) {
                    $total_loan =$loan_info->loan_amount;
               EmployeeLoanReturn::where('id', $loan_info->id)->update(['status' => '3']); 
               
              $salary_payment_deduction_label[] = 'Employee Loan';
            $salary_payment_deduction_value[] =   $total_loan;


$trans_info=EmployeeLoanReturn::where('id','!=',$loan_info->loan_id)->get();
                 if (!empty($trans_info)) {
                foreach ($trans_info as $it) {
              if ($it->status == '3') {
           EmployeeLoan::where('id', $loan_info->loan_id)->update(['status' => '4']);   
           
}
        else {
            EmployeeLoan::where('id', $loan_info->loan_id)->update(['status' => '3']);   
           
}
}
}
          
            $loan=AccountCodes::where('account_name','Employee Loan')->first();   
          $journal = new JournalEntry();
        $journal->account_id = $loan->id;
          $journal->user_id=$request->user_id ;
        $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'Employee Loan Payment';
        $journal->debit= $total_loan;
        $journal->payment_id= $salary_payment->id;
        $journal->payment_month=$request->payment_month;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "Employee Loan Payment to " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
          
         $journal = new JournalEntry();
        $journal->account_id =   $request->account_id;;
          $journal->user_id=$request->user_id ;
        $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'Employee Loan Payment';
        $journal->credit= $total_loan;
        $journal->payment_id= $salary_payment->id;
        
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "Employee Loan Payment to " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
          

        
            }
     

// get award info by employee id and payment date
        $award_info = EmployeeAward::where('user_id',$request->user_id)->where('award_date', $request->payment_month)->where('status', '1')->get();;
 $total_award=0;
        if (!empty($award_info[0])) {
            foreach ($award_info as $v_award_info) {             
                  $total_award += $v_award_info->award_amount;
                   EmployeeAward::where('id', $v_award_info->id)->update(['status' => '3']); 
                }
                $salary_payment_details_label[] ='Award Name '  ;
                $salary_payment_details_value[] = $total_award;

          
          $award=AccountCodes::where('account_name','Employee Award')->first();   
          $journal = new JournalEntry();
        $journal->account_id = $award->id;
          $journal->user_id=$request->user_id ;
        $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'Employee Award Payment';
        $journal->debit=$total_award;
        $journal->payment_id= $salary_payment->id;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "Employee Award Payment to " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
          
         $journal = new JournalEntry();
        $journal->account_id =   $request->account_id;;
          $journal->user_id=$request->user_id ;
        $date = explode('-', date('Y-m-d'));
        $journal->date =   date('Y-m-d') ;
        $journal->year = $date[0];
        $journal->month = $date[1];
       $journal->transaction_type = 'salary';
        $journal->name = 'Employee Award  Payment';
        $journal->credit=$total_award;
        $journal->payment_id= $salary_payment->id;
         $journal->currency_code =  'TZS';
        $journal->exchange_rate= '1';
        $journal->notes= "Employee Award Payment to " .$emp_info->name. "  for the month ".  $month ;
        $journal->save();
        }

        if (!empty($salary_payment_details_label)) {
            foreach ($salary_payment_details_label as $key => $payment_label) {
                $details_data['salary_payment_details_label'] = $payment_label;
                $details_data['salary_payment_details_value'] = $salary_payment_details_value[$key];
                 $details_data['salary_payment_id'] = $salary_payment->id;
                SalaryPaymentDetails::create($details_data);

            }
        }
        if (!empty($salary_payment_deduction_label)) {
            foreach ($salary_payment_deduction_label as $dkey => $deduction_label) {
                $ddetails_data['salary_payment_id'] = $salary_payment->id;
                $ddetails_data['salary_payment_deduction_label'] = $deduction_label;
                $ddetails_data['salary_payment_deduction_value'] = $salary_payment_deduction_value[$dkey];
 
              SalaryPaymentDeduction::create($ddetails_data);
            }
        }

if(!empty($salary_payment)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $salary_payment->id,
                            'module'=>'Salary Payment',
                            'activity'=>"Salary Payment to " .$emp_info->name. "  for the month ".  $month ,
                        ]
                        );                      
       }

    Toastr::success('Payment Made Successfully','Success');
return redirect(route('view.payment',['departments_id'=>$emp_info->department_id,'payment_month'=>$request->payment_month]));
}

    public function viewPayment($departments_id,$payment_month)
    {  

        $flag = '1';
        $employee_info='';
         $allowance_info='';
        $deduction_info='';
        $overtime_info='';
        $award_info='';
       $advance_salary='';
       $total_hours='';
      $salary_info='';
         $loan_info='';
     $salary_loan_info='';

     $dep_name=Department::where('id',$departments_id)->first();
      if(!empty($salary)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=>$salary->id,
                             'module'=>'Salary Details',
                            'activity'=>"Salary Details for  " . $dep_name->name. "  Department Updated",
                        ]
                        );                      
       }
$all_department_info=Department::all();
if (!empty($flag) || !empty($departments_id)) { 
     $date = new DateTime($payment_month . '-01');
        $start_date = $date->modify('first day of this month')->format('Y-m-d');
        $end_date = $date->modify('last day of this month')->format('Y-m-d');
  

                          $employee_info  = EmployeePayroll::where('department_id',$departments_id)->get();



}

return view('payroll.make_payment',compact('employee_info','flag','payment_month','departments_id','all_department_info','start_date','end_date'));
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
