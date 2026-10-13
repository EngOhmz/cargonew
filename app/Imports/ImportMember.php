<?php

namespace App\Imports;

use App\Models\JournalEntry ;
use App\Models\AccountCodes ;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Models\Cards\Cards;
use App\Models\Cards\CardAssignment;
use DateTime;
use App\Models\Transaction;
use App\Models\Member\Member;
use App\Models\User;
use App\Models\Accounts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Carbon\Carbon;

class ImportMember  implements ToCollection,WithHeadingRow

{ 
//, WithValidation
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
      foreach ($rows as $row) 
      {

        $category = 0;
        if(count($row) == 22){
          $category = 6;
        }else{
        if($row['category'] == "Family"){
          $category = 1;
        }elseif($row['category'] == "Family" || $row['category'] == "FAMILY"){
          $category = 1;
        }
        elseif($row['category'] == "Single"){
          $category = 2;
        }
        elseif($row['category'] == "Corporate"){
          $category = 8;
        }
        elseif($row['category'] == "COUNTRY"){
          $category = 5;
        }
        elseif($row['category'] == "JUNIOR"){
          $category = 4;
        }
      }
        $user_exist = User::where('email',$row['member_id'])->get();
        if(count($user_exist) > 0 || $category == 0){
         // do nothing
        }else{

        
          
$dependant = str_replace('FMM','',preg_replace('/\b\d+\b/', '', $row['dependants']));
     $member = Member::create([
       

        'full_name' => $row['full_name'],
        'member_id' => $row['member_id'],
        'dependants' => str_replace('.','',$dependant),
        'due_date' => $row['due_date'],
        'membership_class' => $category,
        'is_active' => $row['status'],
        'phone' => $row['phone_number'],
        'company' => $row['company'],

        ]);
        if(!empty($dependant)){

          $member_dependant = Member::create([
       

            'full_name' => str_replace('.','',$dependant),
            'member_id' => $row['member_id'].'.1',
            'dependants' => '',
            'is_dependant' => 1,
            'due_date' => $row['due_date'],
            'membership_class' => $category,
    
            ]);

        $user1 = User::create([
       'name'=> str_replace('.','',$dependant),
       'email'=>$row['member_id'].'.1',
       'member_id'=> $member_dependant->id,
       'is_active'=>1,
       'password'=> Hash::make($row['member_id'].'.1'),
      
      ]);

      $role = Role::where('slug','Member')->get()->first();
      $user1->roles()->attach($role->id);


      $last_card_id = Cards::all()->last();
      if(!empty($last_card_id)){
          $reference_no = $last_card_id->id + 1;
      }else{
          $reference_no = 0;
      }


      $data['reference_no'] = "DCG-M-".sprintf('%04d',$reference_no);
      $data['added_by'] = $member_dependant->id;
      $data['type'] = 1;
      $cards = Cards::create($data);


      if(!empty($cards))
      $card_id = $cards->id;
      $member_id = $member_dependant->id;

      if(isset($card_id)){
          $data['member_id'] = $member_id;
          $data['cards_id'] = $card_id;
          $data['added_by'] = $member_dependant->id;

          $assignment  = CardAssignment::create($data);
       }else{

          return redirect()->back()->with(['error'=>'No Card available']);
       }
      if(!empty($assignment->id) && $assignment->id > 0){
          Cards::where('id',$card_id)->update(['status'=>2,'owner_id'=>$member_id]);
          Member::find($member_id)->update(['status'=>1,'card_id'=>$card_id]);

      }
        }

      $user = User::create([
       'name'=> $row['full_name'],
       'email'=>$row['member_id'],
       'member_id'=> $member->id,
       'is_active'=>1,
       'password'=> Hash::make($row['member_id']),
      
      ]);

      $role = Role::where('slug','Member')->get()->first();
      $user->roles()->attach($role->id);


      $last_card_id = Cards::all()->last();
      if(!empty($last_card_id)){
          $reference_no = $last_card_id->id + 1;
      }else{
          $reference_no = 0;
      }


      $data['reference_no'] = "DCG-M-".sprintf('%04d',$reference_no);
      $data['added_by'] = $member->id;
      $data['type'] = 1;
      $cards = Cards::create($data);


      if(!empty($cards))
      $card_id = $cards->id;
      $member_id = $member->id;

      if(isset($card_id)){
          $data['member_id'] = $member_id;
          $data['cards_id'] = $card_id;
          $data['added_by'] = $member->id;

          $assignment  = CardAssignment::create($data);
       }else{

          return redirect()->back()->with(['error'=>'No Card available']);
       }
      if(!empty($assignment->id) && $assignment->id > 0){
          Cards::where('id',$card_id)->update(['status'=>2,'owner_id'=>$member_id]);
          Member::find($member_id)->update(['status'=>1,'card_id'=>$card_id]);

      }
    }

      } 

    
    
  }  




}
