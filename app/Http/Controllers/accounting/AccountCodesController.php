<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;


use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\GroupAccount;
use App\Models\Accounting\ClassAccount;
use App\Models\Accounting\AccountCodes;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;


class AccountCodesController extends Controller
{
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
        $codes = AccountCodes::orderBy('account_codes','asc')->get();
          $group_account = GroupAccount::all();
        return view('accounting.account_codes.data', compact('codes','group_account'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
       $group_account = GroupAccount::all();
        return view('accounting.account_codes.create', compact('group_account'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
      $validatedData = $request->validate([
        'account_name' => 'required',
        'account_group' => 'required',
          'account_status' => 'required',
    ]);
 
  
        $account_codes = new AccountCodes();
        
   $account_codes->account_name = $request->account_name ;
    $account_codes->account_group  = $request->account_group  ;
    $account_codes->account_status  = $request->account_status  ;

  $group=GroupAccount::where('name', $request->account_group)->first();                                 
  $account_codes->account_type= $group->type;
      $account_codes->added_by = auth()->user()->id;;

$before=AccountCodes::where('account_group',$request->account_group)->latest('id')->first();
      if(!empty($before)){
        $count=AccountCodes::where('account_group',$request->account_group)->count();
        if($count == '99'){
  Toastr::error('You have exceeded the limit for the group.','Error');
return redirect(route('account_codes.index'));

}
        else{
      $account_codes->account_codes =    $before->account_codes +1;
     $account_codes->order_no = $before->order_no +1;
}
}
else{
        $account_codes->account_codes = $group->group_id +1;
     $account_codes->order_no = '0';

}
       


        $account_codes->save();

        AccountCodes::where('id',$account_codes->id)->update(['account_id' => $account_codes->id]);

          $chart_of_account = new ChartOfAccount();
          $chart_of_account->id =  $account_codes->id;
         $chart_of_account->account_codes = $account_codes->account_codes;
        $chart_of_account->account_name = $request->account_name ;
           $chart_of_account->name = $request->account_name ;
            $chart_of_account->gl_code = $account_codes->account_codes;
       $chart_of_account->account_type =     $account_codes->account_type ;
          $chart_of_account->active = $request->account_status  ;
            $chart_of_account->added_by = auth()->user()->id;;
        $chart_of_account->save();
        
        
        Toastr::success('Account Code Created Successfully','Success');
        return redirect('account_codes');
        }
   

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function edit($id)
    {
       $data= AccountCodes::find($id);
            $group_account = GroupAccount::all();
        return View::make('accounting.account_codes.data', compact('data','group_account','id'))->render();
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
       
      $account_codes= AccountCodes::find($id);
      $account_codes->account_codes = $request->account_codes;
    $account_codes->account_name = $request->account_name ;
     $account_codes->account_group  = $request->account_group  ;
     $account_codes->account_status  = $request->account_status  ;

  $group=GroupAccount::where('name', $request->account_group)->first();                                 
   $account_codes->account_type= $group->type;

$old = AccountCodes::find($id);

       if($old->account_group != $request->account_group){
$before=AccountCodes::where('account_group',$request->account_group)->latest('id')->first();
       if(!empty($before)){
        $count=AccountCodes::where('account_group',$request->account_group)->count();
        if($count == '99'){
    Toastr::error('You have exceeded the limit for the group.','Error');
return redirect(route('account_codes.index'));

}
         else{
       $account_codes->account_codes =    $before->account_codes +1;
      $account_codes->order_no = $before->order_no +1;
}
}
else{
         $account_codes->account_codes = $group->group_id +1;
      $account_codes->order_no = '0';

}
}

else{
$account_codes->account_codes =   $old->account_codes;
}         
         $account_codes->save();

       ChartOfAccount::where('id',$account_codes->id)->update([
           'account_codes' =>$account_codes->account_codes,
           'account_name' =>$request->account_name,
           'account_type' => $account_codes->account_type,
              'name' =>$request->account_name,
               'gl_code' =>$account_codes->account_codes,
                 'active' =>$request->account_status,
       ]);

       Toastr::success('Account Code Updated Successfully','Success');
     return redirect('account_codes');
 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        AccountCodes::destroy($id);
          ChartOfAccount::where('id',$id)->delete();

          Toastr::success('Account Codes Deleted Successfully','Success');
        return redirect('account_codes');
    }
}
