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

class OvertimeController extends Controller
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
         $year= date('Y');
        $current_month = date('m');
        


         // get current year


            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $month = $year . "-" . '0' . $i;
                } else {
                    $month = $year . "-" . $i;
                }
              $date = new DateTime($month . '-01');
            $start_date = $date->modify('first day of this month')->format('Y-m-d');
            $end_date = $date->modify('last day of this month')->format('Y-m-d');
                $overtime_salary_info[$i] = Overtime::where('overtime_date','>=', $start_date)->where('overtime_date','<=', $end_date)->get();
                 $user_overtime_salary_info[$i] =Overtime::where('user_id',$user)->where('overtime_date','>=', $start_date)->where('overtime_date','<=', $end_date)->get();
            }
       
      

 return view('payroll.overtime',compact('current_month','year','overtime_salary_info','user_overtime_salary_info'));
       
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
        $data['overtime_amount']=$request->overtime_amount;
        $data['overtime_date']=$request->overtime_date;
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
        $overtime=Overtime::create($data);

 $emp_info = User::find($request->user_id);
$month= date('d F Y', strtotime($request->overtime_date)) ;

if(!empty($overtime)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $overtime->id,
                            'module'=>'Overtime',
                            'activity'=>"Overtime to " .$emp_info->name. "  for the period ".  $month. " is " .$st,
                        ]
                        );                      
       }

 return redirect(route('overtime.index'))->with(['success'=>'Overtime Created Successfully']);
}

else{
  $user=auth()->user()->id;
     
        $current_month = date('m');
         
            if(!empty($request->year)){
         $year=$request->year;
}
else{
            $year= date('Y'); // get current year
}
         // get current year

            for ($i = 1; $i <= 12; $i++) { // query for months
                if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                    $month = $year . "-" . '0' . $i;
                } else {
                    $month = $year . "-" . $i;
                }
                 $date = new DateTime($month . '-01');
            $start_date = $date->modify('first day of this month')->format('Y-m-d');
            $end_date = $date->modify('last day of this month')->format('Y-m-d');

             $date = new DateTime($month . '-01');
            $start_date = $date->modify('first day of this month')->format('Y-m-d');
            $end_date = $date->modify('last day of this month')->format('Y-m-d');

                $overtime_salary_info[$i] = Overtime::where('overtime_date','>=', $start_date)->where('overtime_date','<=', $end_date)->get();
                 $user_overtime_salary_info[$i] =Overtime::where('user_id',$user)->where('overtime_date','>=', $start_date)->where('overtime_date','<=', $end_date)->get();
            }
       }
      

 return view('payroll.overtime',compact('current_month','year','overtime_salary_info','user_overtime_salary_info'));
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
 $overtime=Overtime::find($id);

  $data['user_id']=$request->user_id;
        $data['overtime_amount']=$request->overtime_amount;
        $data['overtime_date']=$request->overtime_date;
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
    
        $overtime->update($data);

 $emp_info = User::find($request->user_id);
$month= date('d F Y', strtotime($request->overtime_date)) ;

if(!empty($overtime)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $overtime->id,
                            'module'=>'Overtime',
                            'activity'=>"Overtime to " .$emp_info->name. "  for the period ".  $month. " is".$st,
                        ]
                        );                      
       }

 return redirect(route('overtime.index'))->with(['success'=>'Overtime Updated Successfully']);


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
$price='' ;
}
else{
$price="You can not apply for Overtime . Please set your Salary Grade  " ;
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
       $overtime=Overtime::find($id);
       $data['status'] = 2;
      $overtime->update($data);


 $emp_info = User::find($overtime->user_id);
$month= date('d F Y', strtotime($overtime->overtime_date)) ;

if(!empty($overtime)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $overtime->id,
                            'module'=>'Overtime',
                            'activity'=>"Overtime to " .$emp_info->name. "  for the period ".  $month. " is rejected",
                        ]
                        );                      
       }
       return redirect(route('overtime.index'))->with(['success'=>'Rejected Successfully']);
   }

public function approve($id)
   {
       //
      $overtime=Overtime::find($id);
       $data['status'] = 1;
        $data['approve_by']=auth()->user()->id;
      $overtime->update($data);

 $emp_info = User::find($overtime->user_id);
$month= date('d F Y', strtotime($overtime->overtime_date)) ;

if(!empty($overtime)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $overtime->id,
                            'module'=>'Overtime',
                            'activity'=>"Overtime to " .$emp_info->name. "  for the period ".  $month. " is approved",
                        ]
                        );                      
       }
       return redirect(route('overtime.index'))->with(['success'=>'Approved Successfully']);
   }
}
