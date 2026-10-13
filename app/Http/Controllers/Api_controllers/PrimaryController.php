<?php

namespace App\Http\Controllers\Api_controllers\Api_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farmer;
use App\Models\User;
use App\Models\TechTransfer\Institution;
use App\Models\Region;
use App\Models\District;
use App\Models\FarmLand;
use App\Models\Land_properties;
use App\Models\Product_tools;
use App\Models\TechTransfer\Category;
use App\Models\TechTransfer\Challenge;
use App\Models\TechTransfer\Expertise;
use App\Models\TechTransfer\Explanation;
use App\Models\TechTransfer\Infrastructure;
use App\Models\TechTransfer\Office;
use App\Models\TechTransfer\Personal;
use App\Models\TechTransfer\Policies;
use App\Models\TechTransfer\Technology;
use App\Models\Ward;
use PhpParser\Node\Expr\Cast\String_;

class PrimaryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $id)
    {
        
        $institution = Institution::where('respondant_id', $id)->get();

       
        
        if($institution->isNotEmpty()){

            foreach($institution as $row){

                $data = $row;
                $farmers = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','institution'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No institutions found'];
            return response()->json($response,200);
        } 
    }

    public function institution(int $id)
    {
        
        $institution = Institution::where('respondant_id', $id)->get();

       
        
        if($institution->isNotEmpty()){

            foreach($institution as $row){

                $data = $row;
                $farmers = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','institution'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No institutions found'];
            return response()->json($response,200);
        } 
    }

    public function category(int $id)
    {
        
        $category = Category::where('institution_id', $id)->get();

       
        
        if($category->isNotEmpty()){

            foreach($category as $row){

                $data = $row;
                $farmers[] = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','category'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success' => false, 'error' => true, 'message' => 'No technology category found'];
            return response()->json($response,200);
        } 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function technology(int $id)
    {
        
        $technology = Technology::where('category', $id)->get();

       
        
        if($technology->isNotEmpty()){

            foreach($technology as $row){

                $data = $row;
                $farmers[] = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','technology'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No category found'];
            return response()->json($response,200);
        } 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function explanation(int $id, String $type)
    {
        
        $explanation = Explanation::where('institution_id', $id)->where('type', $type)->get();

       
        
        if($explanation->isNotEmpty()){

            foreach($explanation as $row){

                $data = $row;
                $farmers[] = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','explanation'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No Type Technology explanation found'];
            return response()->json($response,200);
        } 
    }

    public function challenge(int $id)
    {
        
        $challenge = Challenge::where('institution_id', $id)->get();

       
        
        if($challenge->isNotEmpty()){

            foreach($challenge as $row){

                $data = $row;
                $farmers[] = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','challenge'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No institutions found'];
            return response()->json($response,200);
        } 
    }

    public function office(int $id)
    {
        
        $office = Office::where('institution_id', $id)->get();

       
        
        if($office->isNotEmpty()){

            foreach($office as $row){

                $data = $row;
                $farmers = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','office'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No office found'];
            return response()->json($response,200);
        } 
    }

    public function infrastructure(int $id)
    {
        
        $infrastructure = Infrastructure::where('institution_id', $id)->get();

       
        
        if($infrastructure->isNotEmpty()){

            foreach($infrastructure as $row){

                $data = $row;
                $farmers[] = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','infrastructure'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No infrastructure found'];
            return response()->json($response,200);
        } 
    }

    public function policies(int $id)
    {
        
        $policies = Policies::where('institution_id', $id)->get();

       
        
        if($policies->isNotEmpty()){

            foreach($policies as $row){

                $data = $row;
                $farmers = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','policies'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No policies found'];
            return response()->json($response,200);
        } 
    }

    public function expertise(String  $type, int $id)
    {
        
        $expertise = Expertise::where('type', $type)->where('institution_id', $id)->get();

       
        
        if($expertise->isNotEmpty()){

            foreach($expertise as $row){

                $data = $row;
                $farmers = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','expertise'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No Expertise found'];
            return response()->json($response,200);
        } 
    }

    public function personal(String $type,  int  $id)
    {
        
        $personal = Personal::where('type', $type)->where('institution_id', $id)->get();

       
        
        if($personal->isNotEmpty()){

            foreach($personal as $row){

                $data = $row;
                $farmers[] = $data;
     
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','expertise'=>$farmers];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No Personal found'];
            return response()->json($response,200);
        } 
    }

    public function get_regions(){

        $region=Region::all();
        return response()->json($region,200);

    }

    public function get_Alldistricts(){

        $district= District::all();
        return response()->json($district,200);

    }

    public function get_Allwards(){

        $ward = Ward::all();
        return response()->json($ward,200);

    }

    public function get_districts(int $id){

        $district= District::where('region_id', $id)->get();
        return response()->json($district,200);

    }

    public function get_wards(int $id){

        $ward = Ward::where('district_id', $id)->get();
        return response()->json($ward,200);

    }

    


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $this->validate($request,[
            'phone'=>'required',
            'name'=>'required',
            'address'=>'required',
            'location'=>'required',
            'category'=>'required',
            'email'=>'required',
            'type'=>'required',
            'id'=>'required',


        ]); 
        
      
        $farmer= new Institution();

        $farmer->phone=$request->input('phone');
        $farmer->name=$request->input('name');
        $farmer->address=$request->input('address');
        $farmer->location=$request->input('location');
        $farmer->category=$request->input('category');
        $farmer->email=$request->input('email');
        $farmer->type=$request->input('type');
        $farmer->respondant_id=$request->input('id');

        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Institution registered successful','institution'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Institution'];
            return response()->json($response,200);
        }

        
    }

    public function category_store(Request $request)
    {
        
        $this->validate($request,[
            'category'=>'required',
            'id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);

        foreach(explode(',' ,$request->input('category')) as $row){

            $farmer= new Category();

            $farmer->category=$row;

            $farmer->institution_id=$request->input('id');

            $farmer->save();

        }
        

        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Tech Category registered successful'];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Explanation'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function institution_store(Request $request)
    {
        
        $this->validate($request,[
            'phone'=>'required',
            'name'=>'required',
            'address'=>'required',
            'location'=>'required',
            'category'=>'required',
            'email'=>'required',
            'type'=>'required',
            'id'=>'required',


        ]); 
        
      
        $farmer= new Institution();

        $farmer->phone=$request->input('phone');
        $farmer->name=$request->input('name');
        $farmer->address=$request->input('address');
        $farmer->location=$request->input('location');
        $farmer->category=$request->input('category');
        $farmer->email=$request->input('email');
        $farmer->type=$request->input('type');
        $farmer->respondant_id=$request->input('id');

        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Institution registered successful','institution'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Institution'];
            return response()->json($response,200);
        }

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function technology_store(Request $request)
    {
        
        $this->validate($request,[
            'tech_form'=>'required',
            'name'=>'required',
            'acquisition'=>'required',
            'capacity'=>'required',
            'category'=>'required',
            'company'=>'required',
            'country'=>'required',
            'status'=>'required',
            'id'=>'required',


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer= new Technology();

        $farmer->tech_form=$request->input('tech_form');
        $farmer->name=$request->input('name');
        $farmer->acquisition=$request->input('acquisition');
        $farmer->capacity=$request->input('capacity');
        $farmer->category=$request->input('category');
        $farmer->company=$request->input('company');
        $farmer->country=$request->input('country');
        $farmer->status=$request->input('status');
        $farmer->institution_id=$request->input('id');

        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Technology registered successful','technology'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Technology'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function explanation_store(Request $request)
    {
        
        $this->validate($request,[
            'features'=>'required',
            'technology'=>'required',
            'type'=>'required',
            'id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer= new Explanation();

        $farmer->type=$request->input('type');
        $farmer->technology=$request->input('technology');
        $farmer->features=$request->input('features');
        $farmer->institution_id=$request->input('id');

        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Explanation registered successful','explanation'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Explanation'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function challenge_store(Request $request)
    {
        
        $this->validate($request,[
            'faced'=>'required',
            'management'=>'required',
            'acquisition'=>'required',
            'id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer= new Challenge();

        $farmer->acquisition=$request->input('acquisition');
        $farmer->management=$request->input('management');
        $farmer->faced=$request->input('faced');
        $farmer->institution_id=$request->input('id');

        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Challenge registered successful', 'challenge'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Challenge'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function office_store(Request $request)
    {
        
        $this->validate($request,[
            'office'=>'required',
            'information'=>'required',
            'id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer= new Office();

        $farmer->office=$request->input('office');
        $farmer->information=$request->input('information');
        $farmer->institution_id=$request->input('id');

        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Office registered successful', 'office'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Office'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function infrastructure_store(Request $request)
    {
        
        $this->validate($request,[
            'name'=>'required',
            'type'=>'required',
            'id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer= new Infrastructure();

        $farmer->name=$request->input('name');
        $farmer->type=$request->input('type');
        $farmer->institution_id=$request->input('id');

        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Infrastructure registered successful', 'infrastructure'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Infrastructure'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function policies_store(Request $request)
    {
        
        $this->validate($request,[
            'suitability'=>'required',
            'explanations'=>'required',
            'id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer= new Policies();

        $farmer->suitability=$request->input('suitability');
        $farmer->explanations=$request->input('explanations');
        $farmer->additional=$request->input('additional');
        $farmer->institution_id=$request->input('id');

        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Policies registered successful', 'policies'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Policies'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function expertise_store(Request $request)
    {
        
        $this->validate($request,[
            'id'=>'required',
            'assesment'=>'required',
            'type'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer= new Expertise();

        $farmer->institution_id=$request->input('id');
        $farmer->assesment=$request->input('assesment');
        $farmer->type=$request->input('type');

        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Expertise registered successful', 'expertise'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Expertise'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function personal_store(Request $request)
    {
        
        $this->validate($request,[
            'name'=>'required',
            'contacts'=>'required',
            'expertise_area'=>'required',
            'experience'=>'required',
            'type'=>'required',
            'training'=>'required',
            'id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer= new Personal();

        $farmer->name=$request->input('name');
        $farmer->contacts=$request->input('contacts');
        $farmer->expertise_area=$request->input('expertise_area');
        $farmer->training=$request->input('training');
        $farmer->experience=$request->input('experience');
        $farmer->type=$request->input('type');
        $farmer->institution_id=$request->input('id');


        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Expertise registered successful', 'expertise'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new Expertise'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_id=auth()->user()->id;
        $user=User::find($user_id);
        $farmer=Farmer::find($id);
      $region=Region::all();
         $district= District::where('region_id', $farmer->region_id)->get();  
       $ward= Ward::where('district_id', $farmer->district_id)->get();
        $group=User::find($user_id)->group;
        if(empty($farmer))
        {

            $response=['success'=>false,'error'=>true,'farmer'=>$user->farmer];
            return response()->json($response,200);

        }
        else
        {
            
            $response=['success'=>true,'error'=>false,'farmer'=>$farmer,'group'=>$group,'region'=> $region, 'district' => $district, 'ward' => $ward];
            return response()->json($response,200);
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
      
        $farmer=Farmer::find($id);
        $user_id=auth()->user()->id;
        $user=User::find($user_id);
         $region=Region::all();
         $district= District::where('region_id', $farmer->region_id)->get();  
      $ward= Ward::where('district_id', $farmer->district_id)->get();
        $farm=User::find($user_id)->farmer;
        //return view('agrihub.dashboard');
        $group=User::find($user_id)->group;


        $response=['success'=>true,'error'=>false,'farmer'=>$farmer,'group'=>$group,'region'=> $region, 'district' => $district, 'ward' => $ward,'id' => $id,'farm' => $farm];
        return response()->json($response,200);
       
        
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
        $data=Farmer::find($id);
         $this->validate($request,[
            'firstname'=>'required',
            'lastname'=>'required',
            'phone'=>'required',

        ]); 
       
        $result=$request->all();
        //print_r($result);
        $result['user_id']=auth()->user()->id;
        
        $data->update($result);
         //retrieve data for manage user page
        $user_id=auth()->user()->id;
        $user=User::find($user_id);
        //Validate update of data 
        if($data)
        {
            $response=['success'=>true,'error'=>false,'message'=>'Updated Successfuly '];
            return response()->json($response,200);
        }
        else
        {
            // return view('agrihub.manage-farmer')->with('farmer',$user->farmer);

            $response=['success'=>false,'error'=>true,'message'=>'Updated Failed ', 'farmer' => $user->farmer];
            return response()->json($response,200);
        }
        
    }

    public function institution_update(Request $request, int $id)
    {
        
        $this->validate($request,[
            'phone'=>'required',
            'name'=>'required',
            'address'=>'required',
            'location'=>'required',
            'category'=>'required',
            'email'=>'required',
            'type'=>'required',


        ]); 
        
      
        // $farmer= new Institution();
        $farmer = Institution::find($id);


        $farmer->phone=$request->input('phone');
        $farmer->name=$request->input('name');
        $farmer->address=$request->input('address');
        $farmer->location=$request->input('location');
        $farmer->category=$request->input('category');
        $farmer->email=$request->input('email');
        $farmer->type=$request->input('type');

        $result = $farmer->save();
        if($result)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Institution Updated successful','institution'=>$farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to Updated new Institution'];
            return response()->json($response,204);
        }

        
    }

    public function technology_update(Request $request, int $id)
    {
        
        $this->validate($request,[
            'tech_form'=>'required',
            'name'=>'required',
            'acquisition'=>'required',
            'capacity'=>'required',
            'category'=>'required',
            'company'=>'required',
            'country'=>'required',
            'status'=>'required',

        ]); 
        
       
      
        $farmer = Technology::find($id);
        // $farmer = Technology::where('id', $id)->get();


        // $data=$request->all();
            
        // $farmer->update($data);


        $farmer->tech_form=$request->input('tech_form');
        $farmer->name=$request->input('name');
        $farmer->acquisition=$request->input('acquisition');
        $farmer->capacity=$request->input('capacity');
        $farmer->category=$request->input('category');
        $farmer->company=$request->input('company');
        $farmer->country=$request->input('country');
        $farmer->status=$request->input('status');

        $result = $farmer->save();
        if($result)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Technology Updated successful'];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to Updated new Technology'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function explanation_update(Request $request, int $id)
    {
        
        $this->validate($request,[
            'features'=>'required',
            'technology_id'=>'required',
            'type'=>'required',
            'institution_id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer = Explanation::find($id);


        $farmer->type=$request->input('type');
        $farmer->technology_id=$request->input('technology_id');
        $farmer->features=$request->input('features');
        $farmer->institution_id=$request->input('institution_id');

        $result = $farmer->update();

        if($result)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Explanation update successful'];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to update new Explanation'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function challenge_update(Request $request, int $id)
    {
        
        $this->validate($request,[
            'features'=>'required',
            'technology_id'=>'required',
            'type'=>'required',
            'institution_id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer = Challenge::find($id);


        $farmer->type=$request->input('type');
        $farmer->technology_id=$request->input('technology_id');
        $farmer->features=$request->input('features');
        $farmer->institution_id=$request->input('institution_id');

        $result = $farmer->update();

        if($result)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Challenge update successful'];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to update new Challenge'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function office_update(Request $request, int $id)
    {
        
        $this->validate($request,[
            'office'=>'required',
            'information'=>'required',
            'id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer = Office::find($id);


        $farmer->office=$request->input('office');
        $farmer->information=$request->input('information');
        $farmer->institution_id=$request->input('id');

        $result = $farmer->update();

        if($result)
        {
            $response=['success'=>true,'error'=>false,'message'=>'Office update successful'];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to update new Office'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function infrastructure_update(Request $request, int $id)
    {
        
        $this->validate($request,[
            'name'=>'required',
            'type'=>'required',
            'id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer = Infrastructure::find($id);


        $farmer->name=$request->input('name');
        $farmer->type=$request->input('type');
        $farmer->institution_id=$request->input('id');

        $result = $farmer->update();

        if($result)
        {
            $response=['success'=>true,'error'=>false,'message'=>'Infrastructure update successful'];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to update  Infrastructure'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function policies_update(Request $request, int $id)
    {
        
        $this->validate($request,[
            'suitability'=>'required',
            'explanations'=>'required',
            'id'=>'required'


        ]);  
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer = Policies::find($id);


        $farmer->suitability=$request->input('suitability');
        $farmer->explanations=$request->input('explanations');
        $farmer->additional=$request->input('additional');
        $farmer->institution_id=$request->input('id');

        $result = $farmer->update();

        if($result)
        {
            $response=['success'=>true,'error'=>false,'message'=>'Policies update successful'];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to update  Policies'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function expertise_update(Request $request, int $id)
    {
        
        $this->validate($request,[
            'id'=>'required',
            'assesment'=>'required',
            'type'=>'required'


        ]);
        
        $farmer = Expertise::find($id);


        // $farmer->institution_id=$request->input('id');
        $farmer->assesment=$request->input('assesment');
        $farmer->type=$request->input('type');
        

        $result = $farmer->update();

        if($result)
        {
            $response=['success'=>true,'error'=>false,'message'=>'Expertise update successful'];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to update  Expertise'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function personal_update(Request $request, int $id)
    {
        
        $this->validate($request,[
            'name'=>'required',
            'contacts'=>'required',
            'expertise_area'=>'required',
            'experience'=>'required',
            'type'=>'required',
            'training'=>'required',
            'id'=>'required'


        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer = Personal::find($id);


        $farmer->name=$request->input('name');
        $farmer->contacts=$request->input('contacts');
        $farmer->expertise_area=$request->input('expertise_area');
        $farmer->training=$request->input('training');
        $farmer->experience=$request->input('experience');
        $farmer->type=$request->input('type');
        $farmer->institution_id=$request->input('id');


        $result = $farmer->update();

        if($result)
        {
            $response=['success'=>true,'error'=>false,'message'=>'Personal update successful'];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to update  Personal'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data=Farmer::find($id);
        $data->delete();
        if($data)
        {

            $response=['success'=>true,'error'=>false,'message'=>'Deleted Successfuly '];
            return response()->json($response,200);
        }
    }

public function findRegion(Request $request)
    {

        $district= District::where('region_id',$request->id)->get(); 
        if ($district) {
            $response=['success'=>true,'error'=>false,'message'=>'Regions Founds', 'district' => $district];
            return response()->json($response, 200);
        }
        else{
            $response=['success'=>false,'error'=>true,'message'=>'Regions Not Founds'];
            return response()->json($response, 200);
        }                                                                                    
               

}

public function findDistrict(Request $request)
    {

        $ward= Ward::where('district_id',$request->id)->get();  
        if ($ward) {
            $response=['success'=>true,'error'=>false,'message'=>'Ward Founds', 'ward' => $ward];
            return response()->json($response, 200);
        }
        else{
            $response=['success'=>false,'error'=>true,'message'=>'Ward Not Founds'];
            return response()->json($response, 200);
        }                                      

}


   public function assign_farmer()
    {
        $farm_all=Farmer::orderBy('id','DESC')->get();

        $response=['success'=>true,'error'=>false,'message'=>'Updated Successfuly', 'farm_all' => $farm_all];
        return response()->json($response,200); 
    }

public function discountModal(Request $request)
    {
                 $id=$request->id;
                 $type = $request->type;
                if($type == 'assign'){
                    $data =  Farmer::find($id);
                    $staff=User::where('role','agronomy')->get();
                    // return view('agrihub.adduser',compact('id','data','staff')); 
                    
                    $response=['success'=>true,'error'=>false,'message'=>'Updated Successfuly', 'id' => $id, 'data' => $data, 'staff' => $staff];
                    return response()->json($response,200);
                 }

                 }

 public function save_farmer(Request $request){
                     //
                     $farmer =  Farmer::find($request->id);
                     $data['assign']=$request->assign;
                      $farmer->update($data);
        
                 if($farmer)
        {
            // $messagev="Farmer Assigned Successfully'";
            // return redirect('/assign_farmer')->with('messagev',$messagev);

            $response=['success'=>true,'error'=>false,'message'=>'Farmer Assigned Successfully'];
            return response()->json($response,200);
        }
              

                 }


}
