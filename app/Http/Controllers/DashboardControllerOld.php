<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer\Customer;
use App\Models\Pacel\TempolaryPacel;

use App\Models\Customer\CustomerPacel;
use Illuminate\Support\Facades\Validator;

use App\Models\PacelHistory;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index() {
        if(Session::get('customer_ID')){


            $customer_ID = Session::get('customer_ID');
            // dd($data_array);
            $word_print = 'right';
             return view('dashboard.dashboard1', compact('word_print', 'customer_ID'));

        }
        else{

            $word_print = 'wrong';

            // $customer_ID = '0';



            return view('dashboard.dashboard1',compact('word_print'));

        }
    }

    public function temp_pacel_delete($id){

        $temp_pacel = TempolaryPacel::find(intval($id));


        $data22 = $temp_pacel->delete();

            // dd($data22);

        return redirect()->route('dashboard.index')->with('success', 'Delete Successfully');
    }

    public function store(Request $request){

        $validated = Validator::make($request->all(), [
            'name' => 'required',
        ],
            [
                'name.required' => 'Add at least one mzigo'
            ]);

        if ($validated->fails()) {
            return redirect('staffs/dashboard')
                        ->withErrors($validated)
                        ->withInput();
        }

        $data['mteja'] =$request->mteja;
        $data['mpokeaji'] =$request->mpokeaji;
        $data['added_by'] = auth()->user()->id;
        $customer = Customer::create($data);

        $customer_ID = $customer->id;

        $mteja =$customer->mteja;

        // dd($mteja);
        $mpokeaji =$customer->mpokeaji;
        $activity = "kusajiliwa";
        // $hashtag = "";

        $nameArr =$request->name;
        $idadi =$request->idadi;
        $bei =$request->bei;
        $receipt = $request->receipt;

        $mzigo_unapotokaArr = $request->mzigo_unapotoka;
        $mzigo_unapokwendaArr = $request->mzigo_unapokwenda;
        $jumlaArr = $request->jumla ;
        $ela_iliyopokelewaArr = $request->ela_iliyopokelewa;
       
;
        if(!empty($nameArr)){
            for($i = 0; $i < count($nameArr); $i++){
                if(!empty($nameArr[$i])){

                    $items = array(
                        'mteja' => $mteja,
                        'mpokeaji' => $mpokeaji,
                        'activity' => $activity,
                        'name' => $nameArr[$i],
                        'idadi' => $idadi[$i],
                        'idadi_stoo' => $idadi[$i],
                        'bei' => $bei[$i],
                        'receipt' =>$receipt[$i],
                        'mzigo_unapotoka' =>   $mzigo_unapotokaArr[$i],
                        'mzigo_unapokwenda' =>  $mzigo_unapokwendaArr [$i],
                         'jumla' => $jumlaArr[$i],
                         'customer_id' => $customer->id,
                           'ela_iliyopokelewa' =>  $ela_iliyopokelewaArr[$i],
                           'added_by' => auth()->user()->id);
                       
                        $cp   =   CustomerPacel::create($items);

                     
                    PacelHistory::create([
                        'pacel_id' => $cp->id,
                        'mteja' => $mteja,
                        'mpokeaji' => $mpokeaji,
                        'activity' => $activity,
                        'name' => $cp->name,
                        'idadi' => $cp->idadi,
                        'idadi_stoo' => $cp->idadi,
                        'receipt' =>$cp->receipt,
                        'bei' => $cp->bei,
                        'mzigo_unapotoka' =>   $cp->mzigo_unapotoka,
                        'mzigo_unapokwenda' =>  $cp->mzigo_unapokwenda,
                        'jumla' => $cp->jumla,
                        'ela_iliyopokelewa' =>  $cp->ela_iliyopokelewa,
                        'added_by' => auth()->user()->id
                       
                        ]);  


                        $count = $this->generateUniqueCode();

                        $delivery = $count.$cp->id."-".auth()->user()->id;

                        // dd($delivery);

                        CustomerPacel::where('id', $cp->id)->update(['delivery' => $delivery]);

                        PacelHistory::where('pacel_id', $cp->id)->update(['delivery' => $delivery]);


                        $data['id'] = $cp->id;
                        $pacel_id_arry[] = $data;
    
                }
            }
            
        }  
        TempolaryPacel::where('added_by',auth()->user()->id)->delete();

        return redirect()->back()->with(['success'=>'taharifa zimefanikiwa kuingizwa', 'customer_ID' => $customer_ID]);
    }

    public function generateUniqueCode()
    {
        do {
            $code = random_int(100000, 999999);
        } while (CustomerPacel::where("delivery", "=", $code)->first());
  
        return $code;
    }
}
