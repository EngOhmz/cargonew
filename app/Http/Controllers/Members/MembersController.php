<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member\Business;
use App\Models\Member\Member;
use App\Models\Member\MembershipType;
use App\Models\Member\Sports;
use App\Models\Member\Dependant;
use App\Models\Cards\Cards;
use App\Models\Cards\CardAssignment;
use App\Models\User;
use App\Models\Country;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class MembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return view("members.application_form");
        $country=Country::all(); 
        return view("members.application_form1",compact('country'));
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

        $validated = Validator::make($request->all(), [
            'email' => 'required|unique:users',
            'full_name' => 'required',
            'gender' => 'required',
            'membership_class'=>'required',
            'd_o_birth'=>'required',
            'business_name' => 'nullable',
            'business_address'=>'nullable',
            'employer' => 'nullable',
            'designation' => 'nullable',
           // 'first_proposer'=>'required',
            //'second_proposer'=>'required',
            'picture'=>'required',
        ]);
        $validated->after(function ($validator) use ($request)  {
            $age = Carbon::parse($request->d_o_bith)->age;
           if (($age <18 || $age >18) && $request->membership_class == 4) {
                $validator->errors()->add('age_error', 'For Jounior Membership age should be between 18 and 21 only');
            }
        });
        // $validated->after(function ($validator) use ($request)  {
      
        //    if ($request->first_proposer == $request->second_proposer) {
        //         $validator->errors()->add('age_error', 'First and Second Proposer should be Different People');
        //     }
        // });
        if ($validated->fails()) {
            return redirect('members/non_cooperate')
                        ->withErrors($validated)
                        ->withInput();
        }
        //
        $data['full_name'] =  $request->full_name;
        $data['nationality'] =  $request->nationality;
        $data['address'] =  $request->address;
        $data['phone1'] =  $request->phone1;
        $data['phone2'] =  $request->phone2;
        $data['gender'] =  $request->gender;
        $data['d_o_birth'] =  $request->d_o_birth;
        $data['email'] =  $request->email;
        $data['membership_class'] =  $request->membership_class;
        $data['membership_reason'] =  $request->other_info;
        $data['spouse_name'] =  $request->spouse_name;
        $data['first_proposer'] =  "sam";
        $data['second_proposer'] =  "sam";

        // $data['first_proposer'] =  $request->first_proposer;
        // $data['second_proposer'] =  $request->second_proposer;

        if ($request->hasFile('picture')) {
            $photo=$request->file('picture');
            $fileType=$photo->getClientOriginalExtension();
            $fileName=rand(1,1000).date('dmyhis').".".$fileType;
            $logo=$fileName;
            $photo->move('assets/img/member_pasport_size', $fileName );
             $data['picture'] = $logo;

        }

        $member = Member::create($data);



        



        $business_data['member_id'] = $member->id;
         $business_data['business_name'] = $request->business_name;
         $business_data['business_address'] = $request->business_address;
         $business_data['employer'] = $request->employer;
         $business_data['designation'] = $request->designation;

         $business = Business::create($business_data);


         $sports_name_arr = $request->sport_name;
         $years_played_arr = $request->years_played;
         $level_arr = $request->level;
         if(!empty($sports_name_arr)){
            for($i=0;$i<count($sports_name_arr);$i++){
                $sports_data['member_id'] = $member->id;
                $sports_data['sport_name'] = $sports_name_arr[$i];
                $sports_data['years_played'] = $years_played_arr[$i];
                $sports_data['level'] = $level_arr[$i];

if(!empty($years_played_arr[$i]) || !empty($level_arr[$i])){
    $sports = Sports::create($sports_data);
}
                
            }
         }

         $name_arr = $request->name;
         $birth_date_arr = $request->birth_date;

         if(!empty($name_arr)){
            for($i=0;$i<count($name_arr);$i++){
                

            $dependant_data['name'] = $name_arr[$i];
            $dependant_data['member_id'] = $member->id;
            $dependant_data['birth_date'] = $birth_date_arr[$i];
            $dependant = Dependant::create($dependant_data);

            }
         }



         $user_data['email'] = $request->email;
         $user_data['member_id'] = $member->id;
         $user_data['password'] =  Hash::make(strtoupper($request->full_name));
         $user_data['name'] = $request->fname.''.$request->lname;
         $user  = User::create($user_data);

         $role = Role::where('slug','Member')->get()->first();
         $user->roles()->attach($role->id);

         
         return redirect(url('/'))->with(['success'=>'Congraturation Your Registration Request Received successfull. Your Login credential is Email and Your Last name in Capital Letter']);








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

    public function reg_type(Request $request)
    {
        //
        $proposer = Member::all();
       $membership_type = MembershipType::all();
       $country=Country::all(); 
        if($request->type == 1)
        return view("members.application_form",compact('membership_type','proposer','country'));
        else{
            return view("members.application_form2");
        }
    }

    public function non_cooperate(){
        $proposer = Member::all();
        $membership_type = MembershipType::all();
         $country=Country::all(); 
        return view("members.application_form",compact('membership_type','proposer','country'));
    }

    public function member_class(Request $request){
       
        
        return response()->json(['html' => view('members.family')->render()]);

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

    public function image_update(Request $request,$id){
        $id = $request->id;

        if ($request->hasFile('picture')) {
            $photo=$request->file('picture');
            $fileType=$photo->getClientOriginalExtension();
            $fileName=rand(1,1000).date('dmyhis').".".$fileType;
            $logo=$fileName;
            $photo->move('assets/img/member_pasport_size', $fileName );
             $data['picture'] = $logo;

        }

        $member = Member::find($id)->update($data);
                    
        //$data = MembershipPayment::find($id);
       // $filename =  $data->attachment;
        return redirect()->back()->with(['success'=>'Image Uploaaded successfully']);


    }

    public function image_update_model(Request $request){
        $id = $request->id;
                    
        //$data = MembershipPayment::find($id);
       // $filename =  $data->attachment;
        return view('members.update_image',compact('id'));


    }

    public function findEmail(Request $request)
    {
 
  $member_info  = Member::where('email', $request->id)->first();
 if (!empty($member_info)) {
$price="Email is already registered. Enter another email" ;
}
else{
$price='' ;
 }


                return response()->json($price);	                  
 
    }

}
