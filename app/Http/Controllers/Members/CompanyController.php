<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Cards\CardAssignment;
use App\Models\Cards\Cards;
use App\Models\Company;
use App\Models\CompanyDeclaration;
use App\Models\Mandotory;
use App\Models\Member\Business;
use App\Models\Member\Dependant;
use App\Models\Member\Member;
use App\Models\Member\Sports;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view("members.application_form2");
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        //
        $validated = Validator::make($request->all(), [

            'cname'  => 'required',
            'organization'  => 'required',
            'regDate'  => 'required',
            'regNo'  => 'required',
            'employeeNo'  => 'required',
            'regHead'  => 'required',
            'employeeBox'  => 'required',
            'tinNo'  => 'required',
            'phone'  => 'required',
            'contactName'  => 'required',
            'email'  => 'required|unique:users',
            'personalPhone'  => 'required',

            'application' => 'required',
            'applicationDate' => 'required',
            'authorizedName' => 'required',
            'agree' => 'required',


            'incorporationCertificate' => 'required',
            'tinCertificate' => 'required',
            'businessLicense' => 'required',
            'organizationProfile' => 'required',
            'membership' => 'required',
            'infoRelevant' => 'required',
            'reasons' => 'required',

            'employeeFirstName' => 'required',
            'employeeLastName' => 'required',
            'employeeEmail' => 'required',
            'employeePosition' => 'required',
            'employeePhone' => 'required',
            'picture' => 'required'


        ]);



        if ($validated->fails()) {
            return redirect('members/member_cooperate')
                ->withErrors($validated)
                ->withInput();
        }

        if ($request->hasFile('incorporationCertificate') && $request->hasFile('tinCertificate') && $request->hasFile('businessLicense') && $request->hasFile('membership')  && $request->hasFile('organizationProfile')) {

            $incorporationCertificate1 = $request->file('incorporationCertificate');
            $incorporationCertificate2 = $request->file('tinCertificate');
            $incorporationCertificate3 = $request->file('businessLicense');
            $incorporationCertificate4 = $request->file('organizationProfile');
            $incorporationCertificate5 = $request->file('membership');

            $fileType1 = $incorporationCertificate1->getClientOriginalExtension();
            $fileType2 = $incorporationCertificate2->getClientOriginalExtension();
            $fileType3 = $incorporationCertificate3->getClientOriginalExtension();
            $fileType4 = $incorporationCertificate4->getClientOriginalExtension();
            $fileType5 = $incorporationCertificate5->getClientOriginalExtension();


            $fileName1=rand(1,1000).date('dmyhis').".".$fileType1;
            $fileName2=rand(1,1000).date('dmyhis').".".$fileType2;
            $fileName3=rand(1,1000).date('dmyhis').".".$fileType3;
            $fileName4=rand(1,1000).date('dmyhis').".".$fileType4;
            $fileName5=rand(1,1000).date('dmyhis').".".$fileType5;

            $incorporationCertificate11=$fileName1;
            $incorporationCertificate21=$fileName2;
            $incorporationCertificate31=$fileName3;
            $incorporationCertificate41=$fileName4;
            $incorporationCertificate51=$fileName5;




            $incorporationCertificate1->move('assets/img/logo', $fileName1 );

            $incorporationCertificate2->move('assets/img/logo', $fileName2 );

            $incorporationCertificate3->move('assets/img/logo', $fileName3 );

            $incorporationCertificate4->move('assets/img/logo', $fileName4 );

            $incorporationCertificate5->move('assets/img/logo', $fileName5 );




     $mandatory =   Mandotory::create([
            'incorporationCertificate' => $incorporationCertificate11,
            'tinCertificate' => $incorporationCertificate21,
            'businessLicense' => $incorporationCertificate31,
            'organizationProfile' => $incorporationCertificate41,
            'membership' => $incorporationCertificate51,
            'infoRelevant' => $request->infoRelevant,
            'reasons' => $request->reasons,
        ]);

        $mandatory_id = $mandatory->id;

        }

       $companyDeclarartion = CompanyDeclaration::create([

            'companyName2' => $request->cname,
            'application' => $request->application,
            'applicationDate' => $request->applicationDate,
            'authorizedName' => $request->authorizedName,
            'agree' => $request->agree,
        ]);

        $companyDeclarartion_id = $companyDeclarartion->id;

       $company = Company::create([

            'cname'  =>  $request->cname,
            'mandatory_id' => $mandatory_id,
            'companyDeclarartion_id' => $companyDeclarartion_id,
            'organization'  =>  $request->organization,
            'regDate'  =>  $request->regDate,
            'regNo'  =>  $request->regNo,
            'employeeNo'  =>  $request->employeeNo,

            'regHead'  =>  $request->regHead,
            'employeeBox'  =>  $request->employeeBox,
            'tinNo'  =>  $request->tinNo,
            'phone'  =>  $request->phone,


            'contactName'  =>  $request->contactName,
            'email'  =>  $request->email,
            'personalPhone'  =>  $request->personalPhone,

        ]);

                 $company_id = $company->id;

                $user_data['email'] = $company->email;
                $user_data['company_id'] = $company->id;
                $user_data['password'] =  Hash::make($company->email);
                $user_data['name'] = $company->cname;
                $user  = User::create($user_data);

                $role = Role::where('slug','Member')->get()->first();
                $user->roles()->attach($role->id);

        

       

        $employeeFirstName_array = $request->employeeFirstName;

        $employeeLastName_array = $request->employeeLastName;

        $employeeEmail_array = $request->employeeEmail;

        $employeePhone_array = $request->employeePhone;

        $picture_array = $request->picture;

        


//        if(!empty($employeeName_array) && count($employeeName_array ) <= 5){
//            return redirect('members/member_cooperate')
//                ->withErrors(['error', 'Membership minimum must be 5']);
//            }

//variable application_type varies from 1 to 2 where 1 for cooperate members while 2 for non-cooperate member
        if(!empty($employeeFirstName_array)){
            for ($i=0; $i<count($employeeFirstName_array); $i++){

                $data['fname'] =  $employeeFirstName_array[$i];
                $data['lname'] =  $employeeLastName_array[$i];
                $data['nationality'] =  'null';
                $data['address'] =  $request->employeeBox;
                $data['gender'] =  'null';
                $data['email'] =  $employeeEmail_array[$i];
                $data['membership_class'] =  2;  //for all  cooperate members assumed singles
                $data['phone1'] =  $employeePhone_array[$i];
                $data['membership_reason'] =  'null';
                $data['cooperate_id'] =  $company_id;
                $data['application_type'] =  1; //for cooperate members
                $data['spouse_name'] =  'null';
                $data['first_proposer'] = 'null';
                $data['second_proposer'] = 'null';

                $data['picture'] = 'null';

                // dd($picture_array[$i]->getClientOriginalName());

                // if ($request->hasFile('picture')) {
                //     $photo=$picture_array[$i]->getClientOriginalExtension();   
                //     $fileName=rand(1,1000).date('dmyhis').".".$photo;
                //     $logo=$fileName;
                //     $photo->move('assets/img/logo', $fileName );
                //     $data['picture'] = $logo;

                // }

                $member = Member::create($data);

               // dd($member);



                // $last_card_id = Cards::all()->last();
                // if(!empty($last_card_id)){
                //     $reference_no = $last_card_id->id + 1;
                // }else{
                //     $reference_no = 0;
                // }


                // $card_data['reference_no'] = "DCG-M-".sprintf('%04d',$reference_no);
                // $data['added_by'] = $member->id;
                // $data['type'] = 1;
                // $cards = Cards::create($data);


                // if(!empty($cards))
                //     $card_id = $cards->id;
                // $member_id = $member->id;

                // if(isset($card_id)){
                //     $data['member_id'] = $member_id;
                //     $data['cards_id'] = $card_id;
                //     $data['added_by'] = $member->id;

                //     $assignment  = CardAssignment::create($data);
                // }else{

                //     return redirect()->back()->with(['error'=>'No Card available']);
                // }
                // if(!empty($assignment->id) && $assignment->id > 0){
                //     Cards::where('id',$card_id)->update(['status'=>2,'owner_id'=>$member_id]);
                //     Member::find($member_id)->update(['status'=>1,'card_id'=>$card_id]);

                // }

                $user_data['email'] = $employeeEmail_array[$i];
                $user_data['company_id'] = $company->id;
                $user_data['member_id'] = $member->id;
                $user_data['password'] =  Hash::make(strtoupper($employeeLastName_array[$i]));
                $user_data['name'] = $employeeFirstName_array[$i].''.$employeeLastName_array[$i];
                $user  = User::create($user_data);

                $role = Role::where('slug','Member')->get()->first();
                $user->roles()->attach($role->id);




            }
        }

        return redirect(url('/'))->with(['success'=>'Cooperate Created Successfully']);



    }

    public function member_cooperate(){

        return view("members.application_form2");
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
