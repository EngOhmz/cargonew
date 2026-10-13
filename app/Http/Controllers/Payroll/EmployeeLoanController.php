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
use App\Models\Payroll\EmployeeLoan;
use App\Models\Payroll\EmployeeLoanReturn;
use App\Models\Payroll\PayrollActivity;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use DateTime;

class EmployeeLoanController extends Controller
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
            $employee_loan=EmployeeLoan::all();
             $user_employee_loan=EmployeeLoan::where('user_id',$user)->get();
             $all_employee=User::where('id','!=',1)->get();

 return view('payroll.employee_loan',compact('all_employee','employee_loan','user_employee_loan'));
       
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

  

         $data['user_id']=$request->user_id;
        $data['loan_amount']=$request->loan_amount;
      $data['paid_amount']=$request->paid_amount;
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


$loan_amount=$request->loan_amount;
$paid=$request->paid_amount;
        $num=$loan_amount/$paid;

        if(is_float($num)){
        $intpart=floor($num);
         $data['returns']= $intpart + 1;
       }
      else{
         $data['returns']=$num;
}


        $loan=EmployeeLoan::create($data);


     $date = new DateTime($request->deduct_month . '-01');
       $loan_due_date = $date->format('Y-m-d');
      $b=0;

  if(is_float($num)){
        $intpart=floor($num);
        for($i = 0; $i < $intpart; $i++){
            if(!empty($intpart)){
                   $b++;
                $items = array(
                    'loan_amount' =>$request->paid_amount,
                    'loan_id' =>   $loan->id,
                     'status' =>   $loan->status,
                    'user_id' =>$request->user_id,
                     'deduct_month' => date('Y-m', strtotime("+$i months", strtotime($loan_due_date))) 
                             );
                     EmployeeLoanReturn::create($items);  

                
            }
        }

$rem=$loan_amount - ($intpart * $paid);
$m= $intpart;
          if($rem > 0){     
                $gl['loan_amount'] =  $rem;
                 $gl['loan_id'] = $loan->id;
                $gl['status'] =  $loan->status;
                 $gl['user_id'] = $request->user_id;
                 $gl['deduct_month'] =date('Y-m', strtotime("+$m months", strtotime($loan_due_date)));
          EmployeeLoanReturn::create($gl);  
    }             
       }


      else{

   for($i = 0; $i < $num; $i++){
            if(!empty($num)){
                  $b++;
                $items = array(
                    'loan_amount' =>$request->paid_amount,
                    'loan_id' =>   $loan->id,
                     'status' =>   $loan->status,
                    'user_id' =>$request->user_id,
                     'deduct_month' => date('Y-m', strtotime("+$i months", strtotime($loan_due_date))) 
                             );
                     EmployeeLoanReturn::create($items);  
                
            }
        }
        
}
     

 $emp_info = User::find($request->user_id);


if(!empty($loan)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $loan->id,
                            'module'=>'Employee Loan',
                            'activity'=>"Employee Loan to " .$emp_info->name. " is ".$st,
                        ]
                        );                      
       }

 return redirect(route('employee_loan.index'))->with(['success'=>'Employee Loan Created Successfully']);

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
      $data=EmployeeLoan::find($id);
             $all_employee=User::where('id','!=',1)->get();

 return view('payroll.employee_loan',compact('all_employee','data','id'));
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
          $loan=EmployeeLoan::find($id);

     $data['user_id']=$request->user_id;
        $data['loan_amount']=$request->loan_amount;
      $data['paid_amount']=$request->paid_amount;
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
     


$loan_amount=$request->loan_amount;
$paid=$request->paid_amount;
        $num=$loan_amount/$paid;

        if(is_float($num)){
        $intpart=floor($num);
         $data['returns']= $intpart + 1;
       }
      else{
         $data['returns']=$num;
}


        $loan->update($data);


     $date = new DateTime($request->deduct_month . '-01');
       $loan_due_date = $date->format('Y-m-d');
      $b=0;

     EmployeeLoanReturn::where('loan_id',$id)->delete();

  if(is_float($num)){
        $intpart=floor($num);
        for($i = 0; $i < $intpart; $i++){
            if(!empty($intpart)){
                   $b++;
                $items = array(
                    'loan_amount' =>$request->paid_amount,
                    'loan_id' =>   $loan->id,
                     'status' =>   $loan->status,
                    'user_id' =>$request->user_id,
                     'deduct_month' => date('Y-m', strtotime("+$i months", strtotime($loan_due_date))) 
                             );
                     EmployeeLoanReturn::create($items);  

                
            }
        }

$rem=$loan_amount - ($intpart * $paid);
$m= $intpart;
          if($rem > 0){     
                $gl['loan_amount'] =  $rem;
                 $gl['loan_id'] = $loan->id;
                $gl['status'] = $loan->status;;
                 $gl['user_id'] = $request->user_id;
                 $gl['deduct_month'] =date('Y-m', strtotime("+$m months", strtotime($loan_due_date)));
          EmployeeLoanReturn::create($gl);  
    }             
       }


      else{

   for($i = 0; $i < $num; $i++){
            if(!empty($num)){
                  $b++;
                $items = array(
                    'loan_amount' =>$request->paid_amount,
                    'loan_id' =>   $loan->id,
                     'status' =>   $loan->status,
                    'user_id' =>$request->user_id,
                     'deduct_month' => date('Y-m', strtotime("+$i months", strtotime($loan_due_date))) 
                             );
                     EmployeeLoanReturn::create($items);  
                
            }
        }
        
}
     
 $emp_info = User::find($request->user_id);


if(!empty($loan)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $loan->id,
                            'module'=>'Employee Loan',
                            'activity'=>"Employee Loan to " .$emp_info->name. " is " .$st,
                        ]
                        );                      
       }

 return redirect(route('employee_loan.index'))->with(['success'=>'Employee Loan Updated Successfully']);
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



 public function findLoan(Request $request)
    {
 
$user_id=$request->user;



  $employee_info  = EmployeePayroll::where('user_id', $user_id)->first();
 if (!empty( $employee_info)) {

$loan= EmployeeLoan::where('user_id',$user_id)->where('status', '!=', '4')->first();

                    if (!empty($loan)) {
              
$price="You have already applied for loan . Please pay loan before you apply for another loan. " ;

}
else{
$price='' ;
 }


}
else{
$price="You can not apply for Loan . Please set your Salary Grade . " ;
}



                return response()->json($price);	                  
 
    }


public function reject($id)
   {
       //
      $loan= EmployeeLoan::find($id);
       $data['status'] = 2;
       $loan->update($data);

     $emp_info = User::find($loan->user_id);


if(!empty($loan)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $loan->id,
                            'module'=>'Employee Loan',
                            'activity'=>"Employee Loan to " .$emp_info->name. " is rejected",
                        ]
                        );                      
       }

       return redirect(route('employee_loan.index'))->with(['success'=>'Rejected Successfully']);
   }

public function approve($id)
   {
       //
       $loan= EmployeeLoan::find($id);
       $data['status'] = 1;
        $data['approve_by']=auth()->user()->id;
       $loan->update($data);

 $emp_info = User::find($loan->user_id);


if(!empty($loan)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=> $loan->id,
                            'module'=>'Employee Loan',
                            'activity'=>"Employee Loan to " .$emp_info->name. " is approved",
                        ]
                        );                      
       }
       return redirect(route('employee_loan.index'))->with(['success'=>'Approved Successfully']);
   }
   
}
