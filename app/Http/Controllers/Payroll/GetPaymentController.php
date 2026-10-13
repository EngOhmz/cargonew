<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use App\Models\Payroll\Payslip;
use App\Models\Payment_methodes;
use App\Models\AccountCodes;
use App\Models\JournalEntry;
use App\Models\UserDetails\BasicDetails;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use  DateTime;


class GetPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


       public function nssf(Request $request)
    {
        //

    $user=auth()->user()->id;
        $current_month = date('m');
         if(!empty($request->year)){
         $year=$request->year;
}
else{
            $year= date('Y'); // get current year
}
            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $month = $year . "-" . '0' . $i;
                } else {
                    $month = $year . "-" . $i;
                }

                $nssf_salary_info[$i] =  JournalEntry::where('payment_month', $month)->where('transaction_type','salary')->where('name','NSSF Payment')->whereNotNull('debit')->get();
             $user_nssf_salary_info[$i] =JournalEntry::where('payment_month', $month)->where('user_id',$user)->where('transaction_type','salary')->where('name','NSSF Payment')->whereNotNull('debit')->get();
            }

 return view('payroll.provident_fund_info',compact('current_month','year','nssf_salary_info','user_nssf_salary_info'));
    }

  public function tax(Request $request)
    {
        //

    $user=auth()->user()->id;
        $current_month = date('m');
         if(!empty($request->year)){
         $year=$request->year;
}
else{
            $year= date('Y'); // get current year
}
            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $month = $year . "-" . '0' . $i;
                } else {
                    $month = $year . "-" . $i;
                }

                $nssf_salary_info[$i] =  JournalEntry::where('payment_month', $month)->where('transaction_type','salary')->where('name','PAYE Payment')->whereNotNull('debit')->get();
             $user_nssf_salary_info[$i] =JournalEntry::where('payment_month', $month)->where('user_id',$user)->where('transaction_type','salary')->where('name','PAYE Payment')->whereNotNull('debit')->get();
            }

 return view('payroll.tax_deduction_info',compact('current_month','year','nssf_salary_info','user_nssf_salary_info'));
    }

  public function nhif(Request $request)
    {
        //

    $user=auth()->user()->id;
        $current_month = date('m');
         if(!empty($request->year)){
         $year=$request->year;
}
else{
            $year= date('Y'); // get current year
}
            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $month = $year . "-" . '0' . $i;
                } else {
                    $month = $year . "-" . $i;
                }

                $nssf_salary_info[$i] =  JournalEntry::where('payment_month', $month)->where('transaction_type','salary')->where('name','NHIF Payment')->whereNotNull('credit')->get();
             $user_nssf_salary_info[$i] =JournalEntry::where('payment_month', $month)->where('user_id',$user)->where('transaction_type','salary')->where('name','NHIF Payment')->whereNotNull('credit')->get();
            }

 return view('payroll.nhif_contr',compact('current_month','year','nssf_salary_info','user_nssf_salary_info'));
    }

  public function wcf(Request $request)
    {
        //

    $user=auth()->user()->id;
        $current_month = date('m');
         if(!empty($request->year)){
         $year=$request->year;
}
else{
            $year= date('Y'); // get current year
}
            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $month = $year . "-" . '0' . $i;
                } else {
                    $month = $year . "-" . $i;
                }

                $nssf_salary_info[$i] =  JournalEntry::where('payment_month', $month)->where('transaction_type','salary')->where('name','WCF Contribution Payment')->whereNotNull('debit')->get();
             $user_nssf_salary_info[$i] =JournalEntry::where('payment_month', $month)->where('user_id',$user)->where('transaction_type','salary')->where('name','WCF Contribution Payment')->whereNotNull('debit')->get();
            }

 return view('payroll.wcf_info',compact('current_month','year','nssf_salary_info','user_nssf_salary_info'));
    }


 public function payroll_summary(Request $request)
    {
        //

    $all_employee=User::where('id','!=',1)->get();

 $search_type = $request->search_type;
 $check_existing_payment='';
$start_month='';
$end_month='';
$by_month='';
$user_id='';
$flag = $request->flag;

 

if (!empty($flag)) {
            if ($search_type == 'employee') {
             $user_id = $request->user_id;
             $check_existing_payment = SalaryPayment::where('user_id', $user_id)->get();
            }
            else if ($search_type == 'month') {
            $by_month = $request->by_month;
             $check_existing_payment = SalaryPayment::all()->where('payment_month', $by_month);
            }
            else if ($search_type == 'period') {
              $start_month = $request->start_month;
              $end_month = $request->end_month;
             $check_existing_payment = SalaryPayment::all()->where('payment_month','>=', $start_month)->where('payment_month','<=', $end_month);
            }
           elseif ($search_type == 'activities') {
             $check_existing_payment =PayrollActivity::all();
            }
}
else{
 $check_existing_payment='';
$start_month='';
$end_month='';
$search_type='';
$by_month='';
$user_id='';
        }

 

 return view('payroll.payroll_summary',compact('all_employee','check_existing_payment','start_month','end_month','search_type','by_month','user_id','flag'));
    }

 public function generate_payslip (Request $request)
    {
        //
     
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
 $start_date ='';
  $loan_info='';
 $end_date='';

$all_department_info=Department::all();
if (!empty($flag) || !empty($departments_id)) {
    $payment_month = $request->payment_month;    
     $date = new DateTime($payment_month . '-01');
        $start_date = $date->modify('first day of this month')->format('Y-m-d');
        $end_date = $date->modify('last day of this month')->format('Y-m-d');
  
                          $employee_info  = EmployeePayroll::where('department_id',$departments_id)->get();



}

return view('payroll.generate_payslip',compact('employee_info','flag','payment_month','departments_id','all_department_info','start_date','end_date'));
 
    }

 public function received_payslip ($id)
    {
        //
    $check_existing_payment = SalaryPayment::find($id);
   $employee_info = User::find($check_existing_payment->user_id);
$month= date('F Y', strtotime($check_existing_payment->payment_month)) ;
$salary_payment_details_info=SalaryPaymentDetails::where('salary_payment_id', $id)->get();
$allowance_info=SalaryPaymentAllowance::where('salary_payment_id', $id)->get();
$deduction_info=SalaryPaymentDeduction::where('salary_payment_id', $id)->get();

       $payslip=Payslip::where('salary_payment_id',$id)->first();
        if(!empty($payslip)){
          $payslip_number = $payslip->payslip_number;
          }
        else{
         $data['salary_payment_id']=$id;
         $data['added_by']=auth()->user()->id;
         $slip= Payslip::create($data);

          $pdata['payslip_number'] = date('Ym') . $slip->id;
          $p=date('Ym') . $slip->id;
           Payslip::where('id', $slip->id)->update($pdata);
       


         if(!empty($slip)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $slip->id,
                            'module'=>'Payslip',
                            'activity'=>"Payslip ". $p. " has been generated for  " .$employee_info->name. "  for the month ".  $month ,
                        ]
                        );                      
       }

   }                
     $pay=Payslip::where('salary_payment_id',$id)->first();
return view('payroll.payslip_info',compact('pay','check_existing_payment','id','employee_info','month','salary_payment_details_info','allowance_info','deduction_info'));
 
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
