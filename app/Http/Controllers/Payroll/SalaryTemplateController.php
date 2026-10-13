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
use App\Models\Payroll\Overtime;
use App\Models\Payroll\EmployeeLoan;
use App\Models\Payroll\EmployeeLoanReturn;
use App\Models\Payroll\PayrollActivity;
use App\Models\Payroll\AdvanceSalary;
use App\Models\Payroll\EmployeeAward;
use App\Models\UserDetails\BasicDetails;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use  DateTime;


class SalaryTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
            $salary = SalaryTemplate::all();

         
        return view('payroll/salary_template',compact('salary'));
        //
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
       
        $template_data['salary_grade'] = $request->salary_grade;
        $template_data['basic_salary'] = $request->basic_salary;
        $template_data['user_id'] = auth()->user()->id;

// ************* Save into tbl_salary_template *************
           if(!empty($id))
           $salary_template_id1 = SalaryTemplate::where('salary_template_id',$id)->update($template_data);
           else
           $salary_template_id1 = SalaryTemplate::create($template_data);
           $salary_template_id = $salary_template_id1->salary_template_id;

           

            // inout data salary_allowance information
            // Input data defualt salary_allowance
            $house_rent_allowance = $request->house_rent_allowance;
            $medical_allowance = $request->medical_allowance;
            // check defualt salary_allowance empty or not
            if (!empty($house_rent_allowance)) {
                $asalary_allowance_data['allowance_label'][] = 'House Rent Allowance';
                $asalary_allowance_data['allowance_value'][] = $house_rent_allowance;
            }
            if (!empty($medical_allowance)) {
                $asalary_allowance_data['allowance_label'][] = 'Medical Allowance';
                $asalary_allowance_data['allowance_value'][] = $medical_allowance;
            }
// check salary_allowance data empty or not 
// if not empty then save into table
            if (!empty($asalary_allowance_data['allowance_label'])) {
                foreach ($asalary_allowance_data['allowance_label'] as $hkey => $h_salary_allowance_label) {
                    $alsalary_allowance_data['salary_template_id'] = $salary_template_id;
                    $alsalary_allowance_data['allowance_label'] = $h_salary_allowance_label;
                    $alsalary_allowance_data['allowance_value'] = $asalary_allowance_data['allowance_value'][$hkey];
                    $alsalary_allowance_data['user_id'] = auth()->user()->id;
// *********** save defualt value into tbl_salary_allowance    *******************
                $salary_allowance = SalaryAllowance::create($alsalary_allowance_data);
                }
            }
            // save add more value into tbl_salary_allowance
            $salary_allowance_label = $request->allowance_label;
            $salary_allowance_value = $request->allowance_value;
            // input id for update
            $salary_allowance_id = $request->salary_allowance_id;
            
            //$salary_allowance = get_any_field('tbl_salary_allowance', array('salary_template_id' => $salary_template_id), 'salary_allowance_id', true);
            $salary_allowance1 = SalaryAllowance::all()->where('salary_template_id',$salary_template_id)->last();
            
            
            // if (!empty($salary_allowance1)) {
            //     $salary_allowance = $salary_allowance1->salary_allowance_id;
            //     $salary_allowance = array_column($salary_allowance, 'salary_allowance_id');
            //     if (!empty($salary_allowance)) {
            //         $delete_salary_allowance_id = array_diff($salary_allowance, $salary_allowance_id);
            //         if (!empty($delete_salary_allowance_id)) {
            //             foreach ($delete_salary_allowance_id as $deleted_id) {
            //                 // $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
            //                 // $this->payroll_model->_primary_key = "salary_allowance_id"; // $id
            //                 // $this->payroll_model->delete($deleted_id);
            //                 $salaryAllowance = SalaryAllowance::find($deleted_id);
            //                 $salaryAllowance->delete();

            //             }
            //         }

            //     }
            // }
            if (!empty($salary_allowance_label)) {
                foreach ($salary_allowance_label as $key => $v_salary_allowance_label) {
                    if (!empty($salary_allowance_value[$key])) {
                        $salary_allowance_data['salary_template_id'] = $salary_template_id;
                        $salary_allowance_data['allowance_label'] = $v_salary_allowance_label;
                        $salary_allowance_data['allowance_value'] = $salary_allowance_value[$key];
// *********** save add more value into tbl_salary_allowance    *******************
                        
                        // $this->payroll_model->_table_name = "tbl_salary_allowance"; // table name
                        // $this->payroll_model->_primary_key = "salary_allowance_id"; // $id
                        if (!empty($salary_allowance_id[$key])) {
                            
                            $allowance_id = $salary_allowance_id[$key];
                            SalaryAllowance::where('salary_allowance_id',$allowance_id)->update($salary_allowance_data);
                            //$this->payroll_model->save($salary_allowance_data, $allowance_id);
                        } else {
                            SalaryAllowance::create($salary_allowance_data);
                            //$this->payroll_model->save($salary_allowance_data);
                        }
                    }
                }
            }
// inout data Deduction information
// Input data defualt salary_allowance
            $provident_fund = $request->provident_fund;
            $tax_deduction = $request->tax_deduction;
            $heslb= $request->heslb;

// check defualt Deduction empty or not
            if (!empty($provident_fund)) {
                $ddeduction_data['deduction_label'][] = 'NSSF';
                $ddeduction_data['deduction_value'][] = $provident_fund;
            }
            if (!empty($tax_deduction)) {
                $ddeduction_data['deduction_label'][] = 'PAYE';
                $ddeduction_data['deduction_value'][] = $tax_deduction;
            }
           if (!empty($heslb)) {
                $ddeduction_data['deduction_label'][] = 'HESLB';
                $ddeduction_data['deduction_value'][] = $heslb;
            }
            if (!empty($ddeduction_data['deduction_label'])) {
                foreach ($ddeduction_data['deduction_label'] as $dkey => $d_deduction_label) {
                    $adeduction_data['salary_template_id'] = $salary_template_id;
                    $adeduction_data['deduction_label'] = $d_deduction_label;
                    $adeduction_data['deduction_value'] = $ddeduction_data['deduction_value'][$dkey];
                    $adeduction_data['user_id'] = auth()->user()->id;

// *********** save defualt value into tbl_salary_allowance    *******************
                    //$this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
                    //$this->payroll_model->_primary_key = "salary_deduction_id"; // $id
                    //$this->payroll_model->save($adeduction_data);
                    SalaryDeduction::create($adeduction_data);
                }
            }
// check Deduction data empty or not
// if not empty then save into table

// input salary deduction id for update
            $salary_deduction_id = $request->salary_deduction_id;
// save add more value into tbl_deduction
            $deduction_label = $request->deduction_label;
            $deduction_value = $request->deduction_value;

            //$salary_deduction = get_any_field('tbl_salary_deduction', array('salary_template_id' => $salary_template_id), 'salary_deduction_id', true);
            $salary_deduction1 = SalaryDeduction::all()->where('salary_template_id',$salary_template_id)->last();
            // if (!empty($salary_deduction)) {
            //     $salary_deduction = $salary_deduction1->salary_deduction_id;
            //     $salary_deduction = array_column($salary_deduction, 'salary_deduction_id');
            //     if (!empty($salary_deduction)) {

            //         $delete_salary_deduction_id = array_diff($salary_deduction, $salary_deduction_id);
            //         if (!empty($delete_salary_deduction_id)) {
            //             foreach ($delete_salary_deduction_id as $deleted_id) {
            //                 // $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
            //                 // $this->payroll_model->_primary_key = "salary_deduction_id"; // $id
            //                 // $this->payroll_model->delete($deleted_id);
            //                 $salaryDeduction = SalaryDeduction::find($deleted_id);
            //                 $salaryDeducyion->delete();
            //             }
            //         }

            //     }
            // }

            if (!empty($deduction_label)) {
                foreach ($deduction_label as $key => $v_deduction_label) {
                    if (!empty($deduction_value[$key])) {

                        $deduction_data['salary_template_id'] = $salary_template_id;
                        $deduction_data['deduction_label'] = $v_deduction_label;
                        $deduction_data['deduction_value'] = $deduction_value[$key];


                        if (!empty($salary_deduction_id[$key])) {
                            $deduction_id = $salary_deduction_id[$key];
                            SalaryDeduction::where('salary_deduction_id',$deduction_id)->update($deduction_data);
                           
                        } else {
                            //$this->payroll_model->save($deduction_data);
                            SalaryDeduction::create($deduction_data);
                        }
                    }
                }
            }


      if(!empty($salary_template_id1)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=>$salary_template_id,
                             'module'=>'Salary Template',
                            'activity'=>"Salary Template  " .  $salary_template_id1->salary_grade. "  Created",
                        ]
                        );                      
       }
         
        return redirect(route('salary_template.index'))->with(['success'=>'Template Created Successfully']);
      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        //
 switch ($request->type) {
            case 'template':
          $salary_template_info = SalaryTemplate::find($id);
          $salary_allowance_info=SalaryAllowance::where('salary_template_id',$id)->get();
          $salary_deduction_info=SalaryDeduction::where('salary_template_id',$id)->get();
                    return view('payroll.salary_template_details',compact('salary_template_info','salary_deduction_info','salary_allowance_info','id'));
                    break;

            case 'employee':
           $salary_grade_info = EmployeePayroll::where('payroll_id',$id)->first();
          $salary_allowance_info=SalaryAllowance::where('salary_template_id', $salary_grade_info->salary_template_id)->get();
          $salary_deduction_info=SalaryDeduction::where('salary_template_id',$salary_grade_info->salary_template_id)->get();
         $employee_info =User::find($salary_grade_info->user_id);
                    return view('payroll.employee_salary_details',compact('salary_grade_info','salary_deduction_info','salary_allowance_info','id','employee_info'));
                        break;

            case 'salary':

         $date = new DateTime($request->month . '-01');
        $start_date = $date->modify('first day of this month')->format('Y-m-d');
        $end_date = $date->modify('last day of this month')->format('Y-m-d');


           $salary_grade_info = EmployeePayroll::where('payroll_id',$id)->first();
          $salary_allowance_info=SalaryAllowance::where('salary_template_id', $salary_grade_info->salary_template_id)->get();
          $salary_deduction_info=SalaryDeduction::where('salary_template_id',$salary_grade_info->salary_template_id)->get();
         $employee_info =User::find($salary_grade_info->user_id);
        $salary_info =SalaryPayment::where('user_id',  $salary_grade_info->user_id)->where('payment_month', $request->month)->first();
       $overtime_info =Overtime::where('user_id',$salary_grade_info->user_id)->where('overtime_date','>=', $start_date)->where('overtime_date','<=', $end_date)->where('status', '1')->get();    
        $advance_salary= AdvanceSalary::where('user_id',$salary_grade_info->user_id)->where('deduct_month', $request->month)->where('status', '1')->get();
          $loan_info= EmployeeLoanReturn::where('user_id',$salary_grade_info->user_id)->where('deduct_month', $request->month)->where('status', '1')->get();
        
        $award_info= EmployeeAward::where('user_id',$salary_grade_info->user_id)->where('award_date', $request->month)->where('status', '1')->get();;
      $payment_month=$request->month;

                    return view('payroll.salary_payment_details',compact('salary_grade_info','salary_deduction_info','salary_allowance_info','id','employee_info','overtime_info','advance_salary','award_info','payment_month','salary_info','loan_info'));
                            break;

            case 'payment':
          

          $salary_info =SalaryPayment::find($id);

          $date = new DateTime($salary_info->payment_month . '-01');
        $start_date = $date->modify('first day of this month')->format('Y-m-d');
        $end_date = $date->modify('last day of this month')->format('Y-m-d');

           $salary_grade_info = EmployeePayroll::where('user_id' ,$salary_info->user_id)->first();
          $salary_allowance_info=SalaryAllowance::where('salary_template_id', $salary_grade_info->salary_template_id)->get();
          $salary_deduction_info=SalaryDeduction::where('salary_template_id',$salary_grade_info->salary_template_id)->get();
         $employee_info =User::find($salary_info->user_id);
       
       $overtime_info =Overtime::where('user_id',$salary_info->user_id)->where('overtime_date','>=', $start_date)->where('overtime_date','<=', $end_date)->where('status', '3')->get();    
        $advance_salary= AdvanceSalary::where('user_id',$salary_info->user_id)->where('deduct_month', $salary_info->payment_month)->where('status', '3')->get();
           $loan_info= EmployeeLoanReturn::where('user_id',$salary_info->user_id)->where('deduct_month', $salary_info->payment_month)->where('status', '3')->get();
        $award_info= EmployeeAward::where('user_id',$salary_info->user_id)->where('award_date', $salary_info->payment_month)->get();;
      $payment_month=$salary_info->payment_month;

                    return view('payroll.monthly_payment_details',compact('salary_grade_info','salary_deduction_info','salary_allowance_info','id','employee_info','overtime_info','advance_salary','award_info','payment_month','salary_info','loan_info'));
                                break;
              case 'advance':
                      if($id > 0){
                        $advance_salary=AdvanceSalary::find($id);
                         $all_employee=User::where('id','!=',1)->get();
                       return view('payroll.add_advance_salary',compact('id','advance_salary','all_employee'));
                    }
                     else{
                         $all_employee=User::where('id','!=',1)->get();
                        return view('payroll.add_advance_salary',compact('all_employee','id'));
                  }
                      
                        break;

                    case 'loan':
                    
                        $loan_details=EmployeeLoanReturn::where('loan_id',$id)->get();
                        
                       return view('payroll.employee_loan_details',compact('id','loan_details'));
                   
                        break;
                   case 'overtime':
                      if($id > 0){
                        $overtime=Overtime::find($id);
                         $all_employee=User::where('id','!=',1)->get();
                       return view('payroll.add_overtime',compact('id','overtime','all_employee'));
                    }
                     else{
                         $all_employee=User::where('id','!=',1)->get();
                        return view('payroll.add_overtime',compact('all_employee','id'));
                  }
                      
                        break;

                     case 'addtemplate':
                        return view('payroll.add_salary_template',compact('id'));
                      
                        break;
             
             default:
             return abort(404);
             
            }
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
 $salary_template_info = SalaryTemplate::find($id);
$salary_allowance_info=SalaryAllowance::where('salary_template_id',$id)->get();
 $salary_deduction_info=SalaryDeduction::where('salary_template_id',$id)->get();     
        return view('payroll/salary_template',compact('salary_template_info','salary_deduction_info','salary_allowance_info','id'));
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

       
        $template_data['salary_grade'] = $request->salary_grade;
        $template_data['basic_salary'] = $request->basic_salary;
        $template_data['user_id'] = auth()->user()->id;

// ************* Save into tbl_salary_template *************
      
           $salary_template_id1 = SalaryTemplate::where('salary_template_id',$id)->update($template_data);
         
           $salary_template_id = $id;

           

            // inout data salary_allowance information
            // Input data defualt salary_allowance
            $house_rent_allowance = $request->house_rent_allowance;
            $medical_allowance = $request->medical_allowance;
            // check defualt salary_allowance empty or not
            if (!empty($house_rent_allowance)) {
                $asalary_allowance_data['allowance_label'][] = 'House Rent Allowance';
                $asalary_allowance_data['allowance_value'][] = $house_rent_allowance;
            }
            if (!empty($medical_allowance)) {
                $asalary_allowance_data['allowance_label'][] = 'Medical Allowance';
                $asalary_allowance_data['allowance_value'][] = $medical_allowance;
            }
// check salary_allowance data empty or not 
// if not empty then save into table
            if (!empty($asalary_allowance_data['allowance_label'])) {
                foreach ($asalary_allowance_data['allowance_label'] as $hkey => $h_salary_allowance_label) {
                    $alsalary_allowance_data['salary_template_id'] = $salary_template_id;
                    $alsalary_allowance_data['allowance_label'] = $h_salary_allowance_label;
                    $alsalary_allowance_data['allowance_value'] = $asalary_allowance_data['allowance_value'][$hkey];
                    $alsalary_allowance_data['user_id'] = auth()->user()->id;
// *********** save defualt value into tbl_salary_allowance    *******************
                $salary_allowance = SalaryAllowance::create($alsalary_allowance_data);
                }
            }
            // save add more value into tbl_salary_allowance
            $salary_allowance_label = $request->allowance_label;
            $salary_allowance_value = $request->allowance_value;
            // input id for update
            $salary_allowance_id = $request->salary_allowance_id;
            
            //$salary_allowance = get_any_field('tbl_salary_allowance', array('salary_template_id' => $salary_template_id), 'salary_allowance_id', true);
            $salary_allowance1 = SalaryAllowance::all()->where('salary_template_id',$salary_template_id)->last();
            
         
            if (!empty($salary_allowance_label)) {
                foreach ($salary_allowance_label as $key => $v_salary_allowance_label) {
                    if (!empty($salary_allowance_value[$key])) {
                        $salary_allowance_data['salary_template_id'] = $salary_template_id;
                        $salary_allowance_data['allowance_label'] = $v_salary_allowance_label;
                        $salary_allowance_data['allowance_value'] = $salary_allowance_value[$key];
// *********** save add more value into tbl_salary_allowance    *******************
                        
                    
                        if (!empty($salary_allowance_id[$key])) {
                            
                            $allowance_id = $salary_allowance_id[$key];
                            SalaryAllowance::where('salary_allowance_id',$allowance_id)->update($salary_allowance_data);
                            //$this->payroll_model->save($salary_allowance_data, $allowance_id);
                        } else {
                            SalaryAllowance::create($salary_allowance_data);
                            //$this->payroll_model->save($salary_allowance_data);
                        }
                    }
                }
            }
// inout data Deduction information
// Input data defualt salary_allowance
            $provident_fund = $request->provident_fund;
            $tax_deduction = $request->tax_deduction;
            $heslb= $request->heslb;

// check defualt Deduction empty or not
            if (!empty($provident_fund)) {
                $ddeduction_data['deduction_label'][] = 'NSSF';
                $ddeduction_data['deduction_value'][] = $provident_fund;
            }
            if (!empty($tax_deduction)) {
                $ddeduction_data['deduction_label'][] = 'PAYE';
                $ddeduction_data['deduction_value'][] = $tax_deduction;
            }
           if (!empty($heslb)) {
                $ddeduction_data['deduction_label'][] = 'HESLB';
                $ddeduction_data['deduction_value'][] = $heslb;
            }
            if (!empty($ddeduction_data['deduction_label'])) {
                foreach ($ddeduction_data['deduction_label'] as $dkey => $d_deduction_label) {
                    $adeduction_data['salary_template_id'] = $salary_template_id;
                    $adeduction_data['deduction_label'] = $d_deduction_label;
                    $adeduction_data['deduction_value'] = $ddeduction_data['deduction_value'][$dkey];
                    $adeduction_data['user_id'] = auth()->user()->id;

// *********** save defualt value into tbl_salary_allowance    *******************
                    //$this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
                    //$this->payroll_model->_primary_key = "salary_deduction_id"; // $id
                    //$this->payroll_model->save($adeduction_data);
                    SalaryDeduction::create($adeduction_data);
                }
            }
// check Deduction data empty or not
// if not empty then save into table

// input salary deduction id for update
            $salary_deduction_id = $request->salary_deduction_id;
// save add more value into tbl_deduction
            $deduction_label = $request->deduction_label;
            $deduction_value = $request->deduction_value;

            //$salary_deduction = get_any_field('tbl_salary_deduction', array('salary_template_id' => $salary_template_id), 'salary_deduction_id', true);
            $salary_deduction1 = SalaryDeduction::all()->where('salary_template_id',$salary_template_id)->last();
            // if (!empty($salary_deduction)) {
            //     $salary_deduction = $salary_deduction1->salary_deduction_id;
            //     $salary_deduction = array_column($salary_deduction, 'salary_deduction_id');
            //     if (!empty($salary_deduction)) {

            //         $delete_salary_deduction_id = array_diff($salary_deduction, $salary_deduction_id);
            //         if (!empty($delete_salary_deduction_id)) {
            //             foreach ($delete_salary_deduction_id as $deleted_id) {
            //                 // $this->payroll_model->_table_name = "tbl_salary_deduction"; // table name
            //                 // $this->payroll_model->_primary_key = "salary_deduction_id"; // $id
            //                 // $this->payroll_model->delete($deleted_id);
            //                 $salaryDeduction = SalaryDeduction::find($deleted_id);
            //                 $salaryDeducyion->delete();
            //             }
            //         }

            //     }
            // }

            if (!empty($deduction_label)) {
                foreach ($deduction_label as $key => $v_deduction_label) {
                    if (!empty($deduction_value[$key])) {

                        $deduction_data['salary_template_id'] = $salary_template_id;
                        $deduction_data['deduction_label'] = $v_deduction_label;
                        $deduction_data['deduction_value'] = $deduction_value[$key];


                        if (!empty($salary_deduction_id[$key])) {
                            $deduction_id = $salary_deduction_id[$key];
                            SalaryDeduction::where('salary_deduction_id',$deduction_id)->update($deduction_data);
                           
                        } else {
                            //$this->payroll_model->save($deduction_data);
                            SalaryDeduction::create($deduction_data);
                        }
                    }
                }
            }
           $salary_template_id = SalaryTemplate::find($id);
                 if(!empty($salary_template_id)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=>$id,
                             'module'=>'Salary Template',
                            'activity'=>"Salary Template " .  $salary_template_id->salary_grade. "  Updated ",
                        ]
                        );                      
       }
        return redirect(route('salary_template.index'))->with(['success'=>'Template Updated Successfully']);
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
     SalaryDeduction::where('salary_template_id', $id)->delete();
         SalaryAllowance::where('salary_template_id', $id)->delete();
      $salary_template_id = SalaryTemplate::find($id);

            if(!empty($salary_template_id)){
                    $activity =PayrollActivity::create(
                        [ 
                            'added_by'=>auth()->user()->id,
                            'module_id'=>$id,
                             'module'=>'Salary Template',
                            'activity'=>"Salary Template  " .  $salary_template_id->salary_grade. "  Deleted ",
                        ]
                        );                      
       }


       $salary_template_id->delete();
        return redirect(route('salary_template.index'))->with(['success'=>'Deleted Successfully']);
    }
}
