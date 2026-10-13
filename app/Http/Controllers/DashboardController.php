<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer\Customer;
use App\Models\Pacel\TempolaryPacel;

use App\Models\Customer\CustomerPacel;
use Illuminate\Support\Facades\Validator;
use App\Services\SmsService;
use App\Services\CargoRouteService;

use App\Models\PacelHistory;
use App\Models\Mikoa;
use App\Models\CargoRoute;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\QueryException;

class DashboardController extends Controller
{
    protected $smsService;
    protected $cargoRouteService;

    public function __construct(SmsService $smsService, CargoRouteService $cargoRouteService)
    {
        $this->smsService = $smsService;
        $this->cargoRouteService = $cargoRouteService;
    }

    // public function index() {
      


    //         // $customer_ID = Session::get('customer_ID');
    //         // // dd($data_array);
    //         // $word_print = 'right';
    //          return view('dashboard.dashboard2');

      
    // }
    public function index(Request $request) {
        // Generate next unique identifier
        $nextIdentifier = $this->generateNextUniqueIdentifier();

        $query = Customer::where('company_id', auth()->user()->company_id);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('created_at', '>=', $request->start_date)
                  ->whereDate('created_at', '<=', $request->end_date);
        }

        // Fetch customer list for the company
        $customer_list = $query->with('registeredBy:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $mikoa = Mikoa::where('company_id', auth()->user()->company_id)->orderBy('regional_name', 'asc')->get();
        $routeViewData = $this->cargoRouteViewData(auth()->user()->company_id);

        // Flash from successful store() or older Session put
        $customer_ID = Session::get('customer_ID');
        
        if($customer_ID){
            $word_print = 'right';
             return view('dashboard.dashboard2', array_merge(compact('word_print', 'customer_ID', 'nextIdentifier', 'customer_list', 'mikoa'), $routeViewData));

        }
        else{

            $word_print = 'wrong';

            // $customer_ID = '0'; mzigo_ID
            if(Session::get('mzigo_ID')){

                $mzigo_ID = Session::get('mzigo_ID');

                return view('dashboard.dashboard2', array_merge(compact('word_print', 'mzigo_ID', 'nextIdentifier', 'customer_list', 'mikoa'), $routeViewData));

            }
            else{

                return view('dashboard.dashboard2', array_merge(compact('word_print', 'nextIdentifier', 'customer_list', 'mikoa'), $routeViewData));

            }

        }
    }
    
      public function editPacel($id)
    {
        if(Session::get('customer_ID')){
            $customer_ID = Session::get('customer_ID');
            $word_print = 'right';
            $data2 = Customer::find($id);
            $items = CustomerPacel::where('customer_id', $id)->get();
            return view('customer.edit_pacel', compact('word_print', 'customer_ID', 'data2', 'items', 'id'));
        } else {
            $word_print = 'wrong';
            $data2 = Customer::find($id);
            $items = CustomerPacel::where('customer_id', $id)->get();
            return view('customer.edit_pacel', compact('word_print', 'data2', 'items', 'id'));
        }
    }

    public function updatePacelList(Request $request)
    {
        // Update existing Customer and CustomerPacel rows in place
        $customer = Customer::find($request->c_id);
        if ($customer) {
            if ($request->filled('mteja')) {
                $customer->mteja = $request->mteja; // Mtumaji
            }
            if ($request->filled('mpokeaji')) {
                $customer->mpokeaji = $request->mpokeaji; // Mpokeaji (ofisi)
            }
            if ($request->filled('customer_phone')) {
                $customer->customer_phone = $request->customer_phone;
            }
            if ($request->filled('receiver_phone')) {
                $customer->receiver_phone = $request->receiver_phone;
            }
            $customer->customer_location = null;
            $customer->save();
        }

        $nameArr = (array) $request->name; // keyed by pacel id
        $idadiArr = (array) $request->quantity;
        $beiArr = (array) $request->price;
        $receiptArr = (array) $request->receipt;
        $fromArr = (array) $request->from;
        $toArr = (array) $request->to;
        $jumlaArr = (array) $request->total_cost;

        foreach ($nameArr as $pacelId => $unused) {
            $pacel = CustomerPacel::find($pacelId);
            if (!$pacel) {
                continue;
            }

            $pacel->mteja = $customer ? $customer->mteja : $pacel->mteja;
            $pacel->mpokeaji = $customer ? $customer->mpokeaji : $pacel->mpokeaji;
            if (isset($nameArr[$pacelId])) $pacel->name = $nameArr[$pacelId];
             if (isset($idadiArr[$pacelId])) {
                $pacel->idadi = $idadiArr[$pacelId];
                $pacel->idadi_stoo = $idadiArr[$pacelId]; 
            }
            // Bei optional — empty → 0
            if (array_key_exists($pacelId, $beiArr)) {
                $beiVal = $beiArr[$pacelId];
                $pacel->bei = ($beiVal === null || $beiVal === '') ? '0' : $beiVal;
            }
            if (isset($receiptArr[$pacelId])) $pacel->receipt = $receiptArr[$pacelId];
            if (isset($fromArr[$pacelId])) $pacel->mzigo_unapotoka = $fromArr[$pacelId];
            if (isset($toArr[$pacelId])) $pacel->mzigo_unapokwenda = $toArr[$pacelId];
            if (isset($jumlaArr[$pacelId])) {
                $pacel->jumla = $jumlaArr[$pacelId];
            } else if (isset($idadiArr[$pacelId]) || isset($beiArr[$pacelId])) {
                $q = isset($idadiArr[$pacelId]) ? (float)$idadiArr[$pacelId] : (float)$pacel->idadi;
                $p = isset($beiArr[$pacelId]) ? (float)$beiArr[$pacelId] : (float)$pacel->bei;
                $pacel->jumla = $q * $p;
            }
            $pacel->save();
        }

        return redirect()->back()->with('success', 'Taarifa zimeboreshwa kikamilifu');
    }

    public function temp_pacel_delete($id){

        $temp_pacel = TempolaryPacel::find(intval($id));


        $data22 = $temp_pacel->delete();

            // dd($data22);

        return redirect()->route('dashboard.index')->with('success', 'Delete Successfully');
    }

    public function store(Request $request){
        try {
        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'mteja' => 'required',
            'mpokeaji' => 'required',
            'route_id' => 'required|array',
            'route_id.*' => 'required|integer',
            'price' => 'nullable|array',
            'price.*' => 'nullable|numeric|min:0',
        ],
            [
                'name.required' => 'Add at least one mzigo',
                'mteja.required' => 'Jina la mtumaji linahitajika',
                'mpokeaji.required' => 'Jina la mpokeaji (ofisi) linahitajika',
                'route_id.required' => 'Chagua route kwa kila mzigo',
            ]);

        if ($validated->fails()) {
            return redirect('staffs/dashboard')
                        ->withErrors($validated)
                        ->withInput();
        }

        $companyId = auth()->user()->company_id;

        // Duplicate prevention with time window: on the same date, block if another record exists within the last 3 minutes
        // Also filter by company_id to prevent duplicates only within the same company
        $existsWithinFiveHours = Customer::where('company_id', $companyId)
            ->where('mteja', $request->mteja)
            ->where('mpokeaji', $request->mpokeaji)
            ->whereDate('created_at', now())
            ->where('created_at', '>=', now()->subMinutes(3))
            ->exists();

        if ($existsWithinFiveHours) {
            return redirect('staffs/dashboard')
                ->withErrors(['duplicate' => 'Huwezi kusajili tena mtumaji na mpokeaji hao ndani ya dakika 3 siku hii hii. Tafadhali subiri hadi upite muda wa dakika 3 au hariri taarifa zilizopo.'])
                ->withInput();
        }

        $data['mteja'] = $request->mteja; // Jina la Mteja (anayepokea SMS)
        $data['mpokeaji'] = $request->mpokeaji; // Jina la Mpokeaji (ofisi)
        $data['customer_phone'] = $request->customer_phone; // Namba ya Mteja (SMS)
        $data['receiver_phone'] = $request->receiver_phone; // Namba ya Mpokeaji (ofisi)
        $data['customer_location'] = null;
        $data['added_by'] = auth()->user()->id;
        $data['company_id'] = $companyId; // Set company_id
        $data['status'] = 'active';

        // Allocate RG-xxx per company; auto-bump on rare race / legacy global unique collisions
        try {
            $customer = $this->createCustomerWithUniqueIdentifier($data, $companyId);
        } catch (QueryException $e) {
            if ($this->isDuplicateUniqueIdentifierException($e)) {
                return redirect('staffs/dashboard')
                    ->withErrors(['unique_identifier' => 'Kitambulisho cha kipekee kimeshatumika. Tafadhali jaribu tena.'])
                    ->withInput();
            }

            throw $e;
        }

        $customer_ID = $customer->id;

        $mteja =$customer->mteja;

        // dd($mteja);
        $mpokeaji =$customer->mpokeaji;
        $activity = "kusajiliwa";
        // $hashtag = "";

        // Ensure inputs are arrays to avoid string offset errors
        $nameArr = array_values((array) $request->name);
        $idadi = array_values((array) $request->quantity);
        $bei = array_values((array) $request->price);
        // receipt[0], receipt[2] style keys must align with name[0], name[1]
        $receiptRaw = (array) $request->receipt;
        ksort($receiptRaw);
        $receipt = array_values($receiptRaw);

        $routeIdArr = array_values((array) $request->route_id);
        $dropMikoaIdArr = (array) $request->drop_mikoa_id;
        $jumlaArr = array_values((array) $request->total_cost);
        $ela_iliyopokelewaArr = array_values((array) $request->ela_iliyopokelewa);
        $companyId = auth()->user()->company_id;

        $subArr = str_replace(",","", (array) $request->subtotal);
       
;

                    // $cost['total_mizigo'] = 0;
                    // $cost['total_amount'] = 0;

        $count22 = $this->generateUniqueCode22();
        
        $count33 = $count22 + 1;
                    
        $unique_col = $count22.'-'.$count33;
        
        $packageDetails = [];
        $packagesString = "";
        $delivery = null;
                    
        $exit_uniqcol = CustomerPacel::where('unique_col', $unique_col)->first();
                                                                                                                    
        if(empty($exit_uniqcol)){
            
            
                        if(!empty($nameArr)){
                    for($i = 0; $i < count($nameArr); $i++){
        
                        if(!empty($nameArr[$i])){
        
                            // $cost['total_amount'] = $subArr[$i];
        
        
                            if(!is_null($idadi[$i])){
                                $idadiT = (int) $idadi[$i];
                            }
                            else{
                                $idadiT = 1;
                        
                            }

                            $cargoRouteId = isset($routeIdArr[$i]) ? (int) $routeIdArr[$i] : null;
                            $destResult = $this->resolvePackageDestinationLines(
                                $request,
                                $i,
                                $idadiT,
                                $cargoRouteId,
                                $companyId
                            );

                            if (!$destResult['ok']) {
                                return redirect('staffs/dashboard')
                                    ->withErrors(['drop_split' => $destResult['message'] . ' (mzigo #' . ($i + 1) . ')'])
                                    ->withInput();
                            }

                            $destLines = $destResult['lines'];
                            $mzigo_unapotokaT = $destResult['from'];
                            $mzigo_unapokwendaT = $destResult['drop_label'];
                            $routeFields = [
                                'cargo_route_id' => $cargoRouteId,
                                'drop_mikoa_id' => count($destLines) === 1 ? $destLines[0]['mikoa_id'] : null,
                            ];

                            // Madekenya: no advance payment on register — always 0
                            $ela_iliyopokelewaT = '0';

                            // Price optional — empty / missing → 0
                            $beiRaw = $bei[$i] ?? null;
                            $beiT = ($beiRaw === null || $beiRaw === '') ? '0' : $beiRaw;

                            if (!empty($jumlaArr[$i]) || (isset($jumlaArr[$i]) && $jumlaArr[$i] === '0')) {
                                $jumlaT = $jumlaArr[$i];
                            } else {
                                $idadiNum = (float) $idadiT;
                                $beiNum = (float) str_replace(',', '', (string) $beiT);
                                $jumlaT = (string) ($idadiNum * $beiNum);
                            }
        
                            // dd($subArr[$i]);
        
                            // $subfloatn = doubleval($subArr[$i]);
                            
                            
        
                            
        
                            
        
                            $lineMtumaji = $this->resolveLineMtumaji($request, $i, $mteja);

                            $items = array(
                                'mteja' => $lineMtumaji,
                                'mpokeaji' => $mpokeaji,
                                'activity' => $activity,
                                'name' => $nameArr[$i],
                                'idadi' => $idadiT,
                                'idadi_stoo' => $idadiT,
                                'bei' => $beiT,
                                'receipt' => is_array($receipt) ? ($receipt[$i] ?? null) : $receipt,
                                'unique_col' => $unique_col,
                                'status' => 'active',
                                'mzigo_unapotoka' =>   $mzigo_unapotokaT,
                                'mzigo_unapokwenda' =>  $mzigo_unapokwendaT,
                                'cargo_route_id' => $routeFields['cargo_route_id'],
                                'drop_mikoa_id' => $routeFields['drop_mikoa_id'],
                                 'jumla' => $jumlaT,
                                 'customer_id' => $customer->id,
                                   'ela_iliyopokelewa' =>  $ela_iliyopokelewaT,
                                   'added_by' => auth()->user()->id,
                                   'company_id' => auth()->user()->company_id); // Set company_id
                               
                                $cp   =   CustomerPacel::create($items);

                                $this->cargoRouteService->createDestinationsForParcel(
                                    $cp,
                                    $destLines,
                                    $companyId
                                );
        
                             
                            PacelHistory::create([
                                'pacel_id' => $cp->id,
                                'mteja' => $lineMtumaji,
                                'mpokeaji' => $mpokeaji,
                                'activity' => $activity,
                                'name' => $cp->name,
                                'idadi' => $cp->idadi,
                                'idadi_stoo' => $cp->idadi,
                                'receipt' =>$cp->receipt,
                                'bei' => $cp->bei,
                                'customer_id' => $customer->id,
                                'mzigo_unapotoka' =>   $cp->mzigo_unapotoka,
                                'mzigo_unapokwenda' =>  $cp->mzigo_unapokwenda,
                                'cargo_route_id' => $cp->cargo_route_id,
                                'drop_mikoa_id' => $cp->drop_mikoa_id,
                                'jumla' => $cp->jumla,
                                'ela_iliyopokelewa' =>  $cp->ela_iliyopokelewa,
                                'status' => 'active',
                                'added_by' => auth()->user()->id,
                                'company_id' => auth()->user()->company_id // Set company_id
                               
                                ]);  
        
        
                                
        
                                // Customer::where('id', $customer->id)->update($cost);
        
        
                                $data['id'] = $cp->id;
                                $pacel_id_arry[] = $data;
                                $packageDetails[] = $idadiT . " " . $nameArr[$i];
            
                        }
                    }
                    
                                $packagesString = implode(", ", $packageDetails);
                                $count = $this->generateUniqueCode();
        
                                $delivery = $count.$customer->id."-".auth()->user()->id;
        
                                // dd($delivery);
        
                                CustomerPacel::where('customer_id', $customer->id)->update(['delivery' => $delivery]);
        
                                PacelHistory::where('customer_id', $customer->id)->update(['delivery' => $delivery]);
                    
                }  
            
            
                                                                                                                        
                                                                                                                        
                }
                    
                    
        
        // TempolaryPacel::where('added_by',auth()->user()->id)->delete();

        // Send SMS notification (only when at least one parcel was saved)
        $smsFlash = null;
        try {
            if (empty($packageDetails)) {
                \Log::warning('Registration SMS skipped: no packages saved', [
                    'customer_id' => $customer->id ?? null,
                ]);
            } elseif (empty($customer->customer_phone)) {
                $smsFlash = 'Usajili umefanikiwa, lakini SMS haikutumwa — namba ya mteja haipo.';
            } else {
                if (empty($delivery)) {
                    $delivery = CustomerPacel::where('customer_id', $customer->id)->value('delivery');
                }
                if ($packagesString === '' && !empty($packageDetails)) {
                    $packagesString = implode(', ', $packageDetails);
                }
                $smsResult = $this->smsService->sendArrivalAtOfficeSms(
                    $customer->customer_phone,
                    $customer->mteja,
                    $packagesString,
                    $customer->id,
                    $customer->company_id,
                    $delivery
                );
                if (empty($smsResult['success'])) {
                    $smsFlash = 'Uajili umefanikiwa, lakini SMS haikutumwa: ' . ($smsResult['message'] ?? 'hitilafu isiyojulikana');
                    \Log::error('Registration SMS failed', [
                        'customer_id' => $customer->id,
                        'phone' => $customer->customer_phone,
                        'result' => $smsResult,
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send registration SMS: " . $e->getMessage());
            $smsFlash = 'Uajili umefanikiwa, lakini SMS haikutumwa.';
        }

        $redirect = redirect()->back()->with([
            'success' => 'taharifa zimefanikiwa kuingizwa',
            'customer_ID' => $customer_ID,
        ]);

        if ($smsFlash) {
            $redirect = $redirect->with('error', $smsFlash);
        }

        return $redirect;
        } catch (\Exception $e) {
            \Log::error("Error saving parcel: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\nStack trace:\n" . $e->getTraceAsString());
            return redirect()->back()->withErrors(['error' => 'Kuna hitilafu. Tafadhali jaribu tena.'])->withInput();
        }
    }

    public function generateUniqueCode()
    {
        do {
            $code = random_int(100000, 999999);
        } while (CustomerPacel::where("delivery", "=", $code)->first());
  
        return $code;
    }
    
    public function generateUniqueCode22()
    {
        do {
            $code = random_int(100000, 999999);
        } while (CustomerPacel::where("unique_col", "=", $code)->first());
  
        return $code;
    }
    
    
    public function editmzigo($id)
    {

        if(Session::get('customer_ID')){


            $customer_ID = Session::get('customer_ID');
            // dd($data_array);
            $word_print = 'right';

            $data2 = Customer::find($id);

            // $data2 = Customer::where('id', $id)->value('level');

            $items = CustomerPacel::where('customer_id', $id)->get();
            $mikoa = Mikoa::where('company_id', auth()->user()->company_id)->orderBy('regional_name', 'asc')->get();
            $routeViewData = $this->cargoRouteViewData(auth()->user()->company_id);
             return view('customer.editmzigo', array_merge(compact('word_print', 'customer_ID', 'data2', 'items', 'id', 'mikoa'), $routeViewData));

        }
        else{

            $word_print = 'wrong';

            //
            $data2 = Customer::find($id);

            // $data2 = Customer::where('id', $id)->value('level');

            $items = CustomerPacel::where('customer_id', $id)->get();
            $mikoa = Mikoa::where('company_id', auth()->user()->company_id)->orderBy('regional_name', 'asc')->get();
            $routeViewData = $this->cargoRouteViewData(auth()->user()->company_id);
            return view('customer.editmzigo', array_merge(compact('word_print', 'data2', 'items', 'id', 'mikoa'), $routeViewData));

        }
        
    }

    public function editmzigolist(Request $request){

        $customer = Customer::find($request->c_id);
        
        $customer_ID = $request->c_id;
        
        $countxx = CustomerPacel::where('customer_id', $customer_ID)->first();

        $delivery = $countxx ? $countxx->delivery : null;

        $mteja =$customer->mteja;

        // dd($mteja);
        $mpokeaji =$customer->mpokeaji;
        $activity = "kusajiliwa";
        // $hashtag = "";

        $nameArr =$request->name;
        $idadi =$request->quantity;
        $bei =$request->price;
        $receipt = $request->receipt;

        $routeIdArr = (array) $request->route_id;
        $dropMikoaIdArr = (array) $request->drop_mikoa_id;
        $companyId = auth()->user()->company_id;
        $jumlaArr = $request->total_cost ;
        $ela_iliyopokelewaArr = $request->ela_iliyopokelewa;

        $subArr = str_replace(",","",$request->subtotal);
       
;

                    // $cost['total_mizigo'] = 0;
                    // $cost['total_amount'] = 0;
                    
     $count22 = $this->generateUniqueCode22();
     
     $count33 = $count22 + 1;
                    
        $unique_col = $count22.'-'.$count33;
                    
        $packageDetails = [];
        $packagesString = "";

        $exit_uniqcol = CustomerPacel::where('unique_col', $unique_col)->first();
                                                                                                                    
        if(empty($exit_uniqcol)){                

            $smsSuppliers = [];

            if(!empty($nameArr)){
                for($i = 0; $i < count($nameArr); $i++){
    
                    if(!empty($nameArr[$i])){
    
                        // $cost['total_amount'] = $subArr[$i];
    
    
                        if(!is_null($idadi[$i])){
                            $idadiT = (int) $idadi[$i];
                        }
                        else{
                            $idadiT = 1;
                    
                        }

                        $cargoRouteId = isset($routeIdArr[$i]) ? (int) $routeIdArr[$i] : null;
                        $destResult = $this->resolvePackageDestinationLines(
                            $request,
                            $i,
                            $idadiT,
                            $cargoRouteId,
                            $companyId
                        );

                        if (!$destResult['ok']) {
                            return redirect()->back()
                                ->withErrors(['drop_split' => $destResult['message']])
                                ->withInput();
                        }

                        $destLines = $destResult['lines'];
                        $mzigo_unapotokaT = $destResult['from'];
                        $mzigo_unapokwendaT = $destResult['drop_label'];
                        $routeFields = [
                            'cargo_route_id' => $cargoRouteId,
                            'drop_mikoa_id' => count($destLines) === 1 ? $destLines[0]['mikoa_id'] : null,
                        ];

                        // Madekenya: no advance payment on register — always 0
                        $ela_iliyopokelewaT = '0';

                        $beiRaw = $bei[$i] ?? null;
                        $beiT = ($beiRaw === null || $beiRaw === '') ? '0' : $beiRaw;

                        if (!empty($jumlaArr[$i]) || (isset($jumlaArr[$i]) && $jumlaArr[$i] === '0')) {
                            $jumlaT = $jumlaArr[$i];
                        } else {
                            $idadiNum = (float) $idadiT;
                            $beiNum = (float) str_replace(',', '', (string) $beiT);
                            $jumlaT = (string) ($idadiNum * $beiNum);
                        }
    
                        // dd($subArr[$i]);
    
                        // $subfloatn = doubleval($subArr[$i]);
    
                        
    
                        
    
                        $receiptT = isset($receipt[$i]) ? $receipt[$i] : (is_array($receipt) ? null : $receipt);

                        $lineMtumaji = $this->resolveLineMtumaji($request, $i, $mteja);
                        if (!in_array($lineMtumaji, $smsSuppliers, true)) {
                            $smsSuppliers[] = $lineMtumaji;
                        }

                        $items = array(
                            'mteja' => $lineMtumaji,
                            'mpokeaji' => $mpokeaji,
                            'activity' => $activity,
                            'name' => $nameArr[$i],
                            'idadi' => $idadiT,
                            'idadi_stoo' => $idadiT,
                            'bei' => $beiT,
                            'receipt' => $receiptT,
                            'status' => 'active',
                            'mzigo_unapotoka' =>   $mzigo_unapotokaT,
                            'mzigo_unapokwenda' =>  $mzigo_unapokwendaT,
                            'cargo_route_id' => $routeFields['cargo_route_id'],
                            'drop_mikoa_id' => $routeFields['drop_mikoa_id'],
                             'jumla' => $jumlaT,
                             'delivery' => $delivery,
                             'customer_id' => $customer->id,
                               'ela_iliyopokelewa' =>  $ela_iliyopokelewaT,
                               'added_by' => auth()->user()->id,
                               'company_id' => auth()->user()->company_id); // Set company_id
                           
                            $cp   =   CustomerPacel::create($items);

                            $this->cargoRouteService->createDestinationsForParcel(
                                $cp,
                                $destLines,
                                $companyId
                            );
    
                         
                        PacelHistory::create([
                            'pacel_id' => $cp->id,
                            'mteja' => $lineMtumaji,
                            'mpokeaji' => $mpokeaji,
                            'activity' => $activity,
                            'name' => $cp->name,
                            'idadi' => $cp->idadi,
                            'idadi_stoo' => $cp->idadi,
                            'receipt' =>$cp->receipt,
                            'bei' => $cp->bei,
                            'delivery' => $delivery,
                            'mzigo_unapotoka' =>   $cp->mzigo_unapotoka,
                            'mzigo_unapokwenda' =>  $cp->mzigo_unapokwenda,
                            'jumla' => $cp->jumla,
                            'ela_iliyopokelewa' =>  $cp->ela_iliyopokelewa,
                            'status' => 'active',
                            'added_by' => auth()->user()->id,
                            'company_id' => auth()->user()->company_id // Set company_id
                           
                            ]);  
    
    
                            
    
                            // Customer::where('id', $customer->id)->update($cost);
    
    
                            $data['id'] = $cp->id;
                            $pacel_id_arry[] = $data;
                            $packageDetails[] = $idadiT . " " . $nameArr[$i];
        
                    }
                }
                
                        
    
                            // dd($delivery);  customer_ID
    
                            // CustomerPacel::where('id', $cp->id)->update(['delivery' => $delivery]);
    
                            // PacelHistory::where('pacel_id', $cp->id)->update(['delivery' => $delivery]);
                
            }  
            // TempolaryPacel::where('added_by',auth()->user()->id)->delete();
            
            // Send SMS notification
            try {
                $packagesString = implode(", ", $packageDetails);
                if ($customer->customer_phone) {
                    $supplierLabel = !empty($smsSuppliers)
                        ? implode(', ', $smsSuppliers)
                        : $customer->mteja;
                    $this->smsService->sendArrivalAtOfficeSms(
                        $customer->customer_phone,
                        $supplierLabel,
                        $packagesString,
                        $customer->id,
                        $customer->company_id,
                        $delivery
                    );
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send registration SMS in editmzigolist: " . $e->getMessage());
            }

        }
        return redirect()->back()->with(['success'=>'taharifa zimefanikiwa kuongezwa', 'customer_ID' => $customer_ID]);
    
    }

    public function findNameItem(Request $request){

        if(empty($request->id)){
            $data = "Andika jina la mzigo Tafadhali";
        }
        else{
            $data = '';
        }

        return response()->json($data);
    }

    public function findNameItem2(Request $request){

        if(empty($request->id)){
            $data = "Andika idadi ya mzigo Tafadhali";
        }
        elseif($request->id < 1){
            $data = "idadi ya mzigo inaanza na 1";
        }
        else{
            $data = '';
        }

        return response()->json($data);
    }

    public function generateNextUniqueIdentifier(?int $companyId = null)
    {
        $companyId = $companyId ?? (auth()->check() ? auth()->user()->company_id : null);

        if (!$companyId) {
            return 'RG-001';
        }

        $identifiers = Customer::withoutTenantScope()
            ->where('company_id', $companyId)
            ->whereNotNull('unique_identifier')
            ->where('unique_identifier', 'like', 'RG-%')
            ->pluck('unique_identifier');

        $maxNumber = 0;
        foreach ($identifiers as $identifier) {
            if (preg_match('/^RG-(\d+)$/', $identifier, $matches)) {
                $maxNumber = max($maxNumber, (int) $matches[1]);
            }
        }

        $nextNumber = $maxNumber + 1;

        return 'RG-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    protected function allocateUniqueIdentifier(?int $companyId, int $maxAttempts = 20): string
    {
        $candidate = $this->generateNextUniqueIdentifier($companyId);

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $exists = Customer::withoutTenantScope()
                ->where('company_id', $companyId)
                ->where('unique_identifier', $candidate)
                ->exists();

            if (!$exists) {
                return $candidate;
            }

            $candidate = $this->bumpUniqueIdentifier($candidate);
            if ($candidate === null) {
                break;
            }
        }

        return $candidate ?? $this->generateNextUniqueIdentifier($companyId);
    }

    /**
     * Create customer with RG-xxx unique to this company.
     * Retries with next number if DB rejects a duplicate (race, or legacy global unique index).
     */
    protected function createCustomerWithUniqueIdentifier(array $data, ?int $companyId, int $maxAttempts = 30)
    {
        $candidate = $this->allocateUniqueIdentifier($companyId);

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $data['unique_identifier'] = $candidate;

            try {
                return Customer::create($data);
            } catch (QueryException $e) {
                if (!$this->isDuplicateUniqueIdentifierException($e)) {
                    throw $e;
                }

                $bumped = $this->bumpUniqueIdentifier($candidate);
                if ($bumped === null) {
                    throw $e;
                }
                $candidate = $bumped;
            }
        }

        throw new \RuntimeException('Could not allocate a free unique_identifier after retries');
    }

    protected function bumpUniqueIdentifier(string $candidate): ?string
    {
        if (!preg_match('/^RG-(\d+)$/', $candidate, $matches)) {
            return null;
        }

        $nextNumber = ((int) $matches[1]) + 1;

        return 'RG-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    protected function isDuplicateUniqueIdentifierException(QueryException $e): bool
    {
        $message = $e->getMessage();

        // Legacy global index name was often just "unique_identifier"
        return str_contains($message, 'unique_identifier')
            || str_contains($message, 'customers_company_unique_identifier_unique')
            || str_contains($message, 'customers_unique_identifier_unique');
    }

    protected function resolveLineMtumaji(Request $request, int $index, ?string $fallbackMtumaji): string
    {
        $lineArr = (array) $request->input('line_mteja', []);
        if (isset($lineArr[$index])) {
            $line = trim((string) $lineArr[$index]);
            if ($line !== '') {
                return $line;
            }
        }

        $fallback = trim((string) ($fallbackMtumaji ?? ''));
        if ($fallback !== '') {
            return $fallback;
        }

        $header = trim((string) $request->input('mteja', ''));

        return $header !== '' ? $header : '—';
    }

    protected function resolvePackageDestinationLines(
        Request $request,
        int $index,
        int $totalQuantity,
        ?int $cargoRouteId,
        ?int $companyId
    ): array {
        $routeMeta = $this->cargoRouteService->resolveParcelRouteFields(
            $cargoRouteId,
            null,
            $companyId
        );

        $dropSplitAll = (array) $request->input('drop_split', []);
        $splitRow = isset($dropSplitAll[$index]) ? (array) $dropSplitAll[$index] : [];

        if (empty($splitRow)) {
            $dropMikoaIdArr = (array) $request->input('drop_mikoa_id', []);
            $dropId = isset($dropMikoaIdArr[$index]) ? (int) $dropMikoaIdArr[$index] : null;
            if ($dropId) {
                $splitRow = [$dropId => $totalQuantity];
            }
        }

        $validated = $this->cargoRouteService->validateDestinationSplit(
            $splitRow,
            $totalQuantity,
            $cargoRouteId,
            $companyId
        );

        if (!$validated['ok']) {
            return [
                'ok' => false,
                'message' => $validated['message'],
                'lines' => [],
                'from' => $routeMeta['from'],
                'drop_label' => '',
            ];
        }

        return [
            'ok' => true,
            'message' => null,
            'lines' => $validated['lines'],
            'from' => $routeMeta['from'],
            'drop_label' => $this->cargoRouteService->buildDropLabelFromLines($validated['lines']),
        ];
    }

    protected function cargoRouteViewData(?int $companyId): array
    {
        return [
            'cargo_routes' => $this->loadCargoRoutesForCompany($companyId),
            'cargo_route_stops' => $this->cargoRouteService->stopsJsonForCompany($companyId),
        ];
    }

    protected function loadCargoRoutesForCompany(?int $companyId)
    {
        if (!$companyId) {
            return collect();
        }

        $routes = CargoRoute::with(['fromMikoa', 'toMikoa', 'stops.mikoa'])
            ->where('company_id', $companyId)
            ->get();

        foreach ($routes as $route) {
            $this->cargoRouteService->syncFinalStop($route);
        }

        return $routes->sortBy(function ($route) {
            return $route->label;
        })->values();
    }

    protected function resolveCargoRouteRegions($routeId, ?int $companyId): array
    {
        if (!$routeId || !$companyId) {
            return ['from' => '#', 'to' => '#'];
        }

        $route = CargoRoute::with(['fromMikoa', 'toMikoa'])
            ->where('company_id', $companyId)
            ->find($routeId);

        if (!$route || !$route->fromMikoa || !$route->toMikoa) {
            return ['from' => '#', 'to' => '#'];
        }

        return [
            'from' => $route->fromMikoa->regional_name,
            'to' => $route->toMikoa->regional_name,
        ];
    }
}
