<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll\SalaryAllowance;
use App\Models\Departments;
use App\Models\Payroll\SalaryDeduction;
use App\Models\Payroll\SalaryTemplate;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\SalaryPayment;
use App\Models\Payroll\SalaryPaymentDetails;
use App\Models\Payroll\SalaryPaymentAllowance;
use App\Models\Payroll\SalaryPaymentDeduction;
use App\Models\UserDetails\BasicDetails;
use App\Models\Payroll\Accounts;
use App\Models\Payroll\Overtime;
use App\Models\Payroll\AdvanceSalary;
use App\Models\Payroll\PayrollActivity;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use DateTime;

class AdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
  $user=auth()->user()->id;
// active check with current month
        $current_month = date('m');
     
            $year= date('Y'); // get current year

            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $month = $year . "-" . '0' . $i;
                } else {
                    $month = $year . "-" . $i;
                }
                $advance_salary_info[$i] = AdvanceSalary::where('deduct_month',$month)->get();
                 $user_advance_salary_info[$i] = AdvanceSalary::where('deduct_month',$month)->where('user_id',$user)->get();
            }
       
      

 return view('payroll.advance_salary',compact('current_month','year','advance_salary_info','user_advance_salary_info'));
       
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

   if(!empty($request->type)){

         $data['user_id']=$request->user_id;
        $data['advance_amount']=$request->advance_amount;
        $data['deduct_month']=$request->deduct_month;
        $data['reason']=$request->reason;
       if(!empty($request->approve)){
        $data['status']='1';
    $data['approve_by']=auth()->user()->id;
$st="Approved";
}
       else{
        $data['status']='0';
$st="Created";
}
        $data['added_by']=auth()->user()->id;
        $advance=AdvanceSalary::create($data);

 $emp_info = User::find($request->user_id);
$month= date('F Y', strtotime($request->deduct_month)) ;

if(!empty($advance)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $advance->id,
                            'module'=>'Advance Salary',
                            'activity'=>"Advance Salary to " .$emp_info->name. "  for the month ".  $month. " is ".$st,
                        ]
                        );                      
       }
    

 return redirect(route('advance_salary.index'))->with(['success'=>'Advance Salary Created Successfully']);
}

else{
// active check with current month
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
                $advance_salary_info[$i] = AdvanceSalary::where('deduct_month',$month)->get();
             $user_advance_salary_info[$i] = AdvanceSalary::where('deduct_month',$month)->where('user_id',$user)->get();
            }
       
  }    


 return view('payroll.advance_salary',compact('current_month','year','advance_salary_info','user_advance_salary_info'));
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
 $advance=AdvanceSalary::find($id);

   $data['user_id']=$request->user_id;
        $data['advance_amount']=$request->advance_amount;
        $data['deduct_month']=$request->deduct_month;
        $data['reason']=$request->reason;
       if(!empty($request->approve)){
        $data['status']='1';
    $data['approve_by']=auth()->user()->id;
$st="Approved";
}
       else{
        $data['status']='0';
$st="Updated";
}
        $advance->update($data);

 $emp_info = User::find($request->user_id);
$month= date('F Y', strtotime($request->deduct_month)) ;

if(!empty($advance)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $advance->id,
                            'module'=>'Advance Salary',
                            'activity'=>"Advance Salary to " .$emp_info->name. "  for the month ".  $month. " is " .$st,
                        ]
                        );                      
       }

 return redirect(route('advance_salary.index'))->with(['success'=>'Advance Salary Updated Successfully']);
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


 public function findAmount(Request $request)
    {
 
$user_id=$request->user;



  $employee_info  = EmployeePayroll::where('user_id', $user_id)->first();
 if (!empty( $employee_info)) {
$total_deduction=0;

$deduction_info = SalaryDeduction::where('salary_template_id', $employee_info->salary_template_id)->get();

                    if (!empty($deduction_info[0])) {
                    foreach ($deduction_info as $value) {
                     $total_deduction+=$value->deduction_value;
}
}

$template=SalaryTemplate::where('salary_template_id', $employee_info->salary_template_id)->first();
$salary=$template->basic_salary-$total_deduction;

if($request->id > $salary){
$price="You have exceeded your Net Salary. Choose amount less than ".  number_format($salary,2) ;

}
else{
$price='' ;
 }


}
else{
$price="You can not apply for Advance Amount . Please set your Salary Grade  " ;
}



                return response()->json($price);	                  
 
    }

 public function findMonth(Request $request)
    {
 
$user_id=$request->user;



  $employee_info  = EmployeePayroll::where('user_id', $user_id)->first();
 if (!empty( $employee_info)) {

$advance_salary= AdvanceSalary::where('user_id',$user_id)->where('deduct_month', $request->id)->first();

                    if (!empty($advance_salary)) {
              
$price="You have already applied for this month . Please choose a different month " ;

}
else{
$price='' ;
 }


}
else{
$price="You can not apply for Advance Amount . Please set your Salary Grade . " ;
}



                return response()->json($price);	                  
 
    }


public function reject($id)
   {
       //
       $advance=AdvanceSalary::find($id);
       $data['status'] = 2;
       $advance->update($data);

 $emp_info = User::find($advance->user_id);
$month= date('F Y', strtotime($advance->deduct_month)) ;

if(!empty($advance)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $advance->id,
                            'module'=>'Advance Salary',
                            'activity'=>"Advance Salary to " .$emp_info->name. "  for the month ".  $month. " is rejected",
                        ]
                        );                      
       }
       return redirect(route('advance_salary.index'))->with(['success'=>'Rejected Successfully']);
   }

public function approve($id)
   {
       //
       $advance=AdvanceSalary::find($id);
       $data['status'] = 1;
        $data['approve_by']=auth()->user()->id;
       $advance->update($data);

 $emp_info = User::find($advance->user_id);
$month= date('F Y', strtotime($advance->deduct_month)) ;

if(!empty($advance)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $advance->id,
                            'module'=>'Advance Salary',
                            'activity'=>"Advance Salary to " .$emp_info->name. "  for the month ".  $month. " is approved",
                        ]
                        );                      
       }
       return redirect(route('advance_salary.index'))->with(['success'=>'Approved Successfully']);
   }
   
}
