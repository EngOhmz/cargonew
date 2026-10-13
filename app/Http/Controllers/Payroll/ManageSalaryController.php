<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll\SalaryAllowance;
use App\Models\Payroll\SalaryDeduction;
use App\Models\Payroll\SalaryTemplate;
use App\Models\Payroll\EmployeePayroll;
use App\Models\UserDetails\BasicDetails;
use App\Models\Payroll\PayrollActivity;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use Yajra\DataTables\DataTables;

class ManageSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
      $all_department_info=Department::all();
        return view('payroll.manage_salary_details',compact('all_department_info'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //

    }

    public function getDetails(Request $request){
        // retrive all data from department table
        $all_department_info=Department::all();
      $salary_grade=SalaryTemplate::all();
      $departments_id=$request->departments_id;
       $flag = $request->flag;
  $salary_grade_info ='';
$designation_info='';
$employee_info='';
     if (!empty($flag) || !empty($departments_id)) {

$designation_info = Designation::where('department_id',$departments_id)->get();
          
                    $employee_info =User::where('department_id',$departments_id)->get();
                    $emp_info =User::where('department_id',$departments_id)->get();
                    if (!empty($emp_info)) {
                    foreach ($emp_info as $value) {
                        // get all salary Template info
                        $salary_grade_info = EmployeePayroll::where('user_id', $value->id)->get();
                          
}
   
            }


}
       return view('payroll.manage_salary_details',compact('all_department_info','departments_id','salary_grade_info','designation_info','employee_info','flag','salary_grade'));
       
    }
    public function save_salary_details(Request $request)
    {

         $nameArr =$request->user_id ;
        $qtyArr = $request->monthly_status  ;
        $priceArr = $request->salary_template_id;
        $rateArr =  $request->payroll_id ;
     
       
      

           if(!empty($qtyArr)){
               for($i = 0; $i < count($qtyArr); $i++){
                   if(!empty($qtyArr[$i])){
                    $dep=User::where('id',$nameArr[$i])->first();
                       $items = array(
                           'user_id' => $nameArr[$i],
                           'salary_template_id' =>$priceArr[$i],                        
                              'added_by'=>auth()->user()->id,
                             'department_id'=>$dep->department_id,
                                       );
                        
                           if(!empty($rateArr[$i])){
                           $salary=EmployeePayroll::where('payroll_id',$rateArr[$i])->update($items);  
      
      }
                          else{
                           $salary=EmployeePayroll::create($items);  
      
      }

                          
       
       
                   }
               }
           }  

  $dep_name=Department::where('id',$salary->department_id)->first();
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

     
        return redirect(route('employee_salary_list'));
    }

    public function employee_salary_list()
    {
            
            $data = EmployeePayroll::all();

         

        return view('payroll.employee_salary_list',compact('data'));
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
         // retrive all data from department table
        $all_department_info=Department::all();
  
      $departments_id=$id;
       $flag = '1';

     if (!empty($flag) || !empty($departments_id)) {

$designation_info = Designation::where('department_id',$departments_id)->get();
          $edit='1';
                    $employee_info =User::where('department_id',$departments_id)->get();
                    $emp_info =User::where('department_id',$departments_id)->get();
                    if (!empty($emp_info)) {
                    foreach ($emp_info as $value) {
                        // get all salary Template info
                        $salary_grade_info = EmployeePayroll::where('user_id', $value->id)->get();
                          $salary_grade=SalaryTemplate::all();
}
       
            }

else{
  $salary_grade_info ='';
}
}
       return view('payroll.manage_salary_details',compact('all_department_info','departments_id','salary_grade_info','designation_info','employee_info','flag','salary_grade','edit'));
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
        $emp =  EmployeePayroll::find($id);    
        $emp->delete();
        return redirect(route('employee_salary_list'))->with(['success'=>'Deleted Successfully']);
    }

 public function addTemplate(Request $request){
       
    
            $template_data['salary_grade'] = $request['salary_grade'];
        $template_data['basic_salary'] = $request['basic_salary'];
        $template_data['user_id'] = auth()->user()->id;

// ************* Save into tbl_salary_template *************
        
           $salary_template_id1 = SalaryTemplate::create($template_data);
           $salary_template_id = $salary_template_id1->salary_template_id;

           

            // inout data salary_allowance information
            // Input data defualt salary_allowance
            $house_rent_allowance = $request['house_rent_allowance'];
            $medical_allowance = $request['medical_allowance'];
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
            $salary_allowance_label = $request['allowance_label'];
            $salary_allowance_value = $request['allowance_value'];
            // input id for update
            $salary_allowance_id = $request->salary_allowance_id;
            
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

                        } else {
                            SalaryAllowance::create($salary_allowance_data);
                            //$this->payroll_model->save($salary_allowance_data);
                        }
                    }
                }
            }
// inout data Deduction information
// Input data defualt salary_allowance
            $provident_fund = $request['provident_fund'];
            $tax_deduction = $request['tax_deduction'];
            $heslb= $request['heslb'];

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
        
      



        if (!empty($salary_template_id1)) {           
            return response()->json($salary_template_id1);
         }

       
   }
}
