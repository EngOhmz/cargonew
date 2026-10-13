<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;

use App\Models\Accounting\GroupAccount;
use App\Models\Accounting\ClassAccount;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;


class GroupAccountController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
        $group = GroupAccount::orderBy('group_id','asc')->get();
           $class_account = ClassAccount::all();
        return view('accounting.group_account.data', compact('group','class_account'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
       $class_account = ClassAccount::all();
        return view('accounting.group_account.create', compact('class_account'));
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
            'name' => 'required',
            'class' => 'required'
        ]);
     
      
            $group_account = new GroupAccount();
             $group_account->name = $request->name;
        $group_account->class = $request->class;
        $group_account->added_by = auth()->user()->id;;

       $class=ClassAccount::where('class_name', $request->class)->first();                     
          $group_account->type= $class->class_type;

     
         $before=GroupAccount::where('class',$request->class)->latest('id')->first();
          if(!empty($before)){
            $count=GroupAccount::where('class',$request->class)->count();
            if($count == '9'){

        Toastr::error('You have exceeded the limit for the group.','Error');
  return redirect(route('group_account.index'))->with(['error'=>'']);

}
            else{
         $group_account->group_id =    $before->group_id +100;
         $group_account->order_no = $before->order_no +1;
}
}
 else{
           $group_account->group_id=    $class->class_id +100;
             $group_account->order_no = '0';

}
           
            $group_account->save();
            
            Toastr::success('Group Created Successfully','Success');
            return redirect('group_account');
        
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
        $data=  GroupAccount::find($id);
           $class_account = ClassAccount::all();
        return View::make('accounting.group_account.data', compact('data','class_account','id'))->render();
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
     
        $group_account = GroupAccount::find($id);
       $group_account->name = $request->name;

        $group_account->class = $request->class;

           $class=ClassAccount::where('class_name', $request->class)->first();                     
          $group_account->type= $class->class_type;

              $old = GroupAccount::find($id);

          if($old->class != $request->class){
     
         $before=GroupAccount::where('class',$request->class)->latest('id')->first();
          if(!empty($before)){
            $count=GroupAccount::where('class',$request->class)->count();
            if($count == '9'){

            Toastr::error('You have exceeded the limit for the group.','Error');   
          return redirect(route('group_account.index'));

}
            else{
         $group_account->group_id =    $before->group_id +100;
         $group_account->order_no = $before->order_no +1;
}
}
 else{
           $group_account->group_id=    $class->class_id +100;
             $group_account->order_no = '0';

}
}

else{
 $group_account->group_id =   $old->group_id;
}
        
        $group_account->save();

        Toastr::success('Group Updated Successfully','Success');
        return redirect('group_account');

 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
        GroupAccount::destroy($id);
        Toastr::success('Group Deleted Successfully','Success');
        return redirect('group_account');
    }
}
