<?php

namespace App\Http\Controllers\Management;


use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerPacel;
use App\Models\DriverRoute;
use App\Models\Management\Car;
use App\Models\Management\Driver;
use Illuminate\Support\Facades\View;
use App\Models\PacelHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use PDF;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $today = Carbon::today();
        $cars = Car::all();

        return view('management.add_car', compact('cars'));


    }

    public function car_today()
    {
        //
        $today = Carbon::today();
        // $cars = Car::all();

        // $cars = DriverRoute::whereDate('created_at', $today)->get();

        // $driverRoute = DriverRoute::join('drivers', 'drivers.id', '=', 'driver_routes.driver_id')
        //                             ->join('cars', 'cars.id', '=', 'driver_routes.car_id' )
        //                             ->whereIn('cars.status', ['2']) 
        //                             ->select('*','cars.id as car_id')
        //                             ->get();

        $driverRoute = Car::where('status', '2')->get();
        // dd($driverRoute);


        return view('management.car_today', compact('driverRoute'));


    }

    public function arrived(){
        // $cars = Car::all();

        $cars = Car::where('status', 1)->get();


        return view('management.arrived_car', compact('cars'));


    }

    public function arrived_car_store(Request $request){

        $date = $request->dateT;

        $cars =  DriverRoute::whereDate('created_at', $date)
                                    ->where('status', '!=', '2')
                                    ->get()
                                    ->unique('car_id');
        
        return view('search_view', compact('cars'));
    
    }                                

    public function all(){
        $cars = Car::all();

        return view('management.all_car', compact('cars'));


    }

    public function old(){
        $cars = Car::all();

        return view('management.old_car', compact('cars'));


    }

    public function car_routes(){
        $cars = Car::all();

        return view('management.car_routes', compact('cars'));


    }

    public function car_pacel(){
        $cars = Car::all();

        return view('management.car_pacels', compact('cars'));


    }

    public function search_view(Request $request){

        $name = $request->input('search');
        

        // return view('management.customer_history');

        return redirect()->route('search_store')->with(compact('name'));



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

        $this->validate($request,[
            'carNumber'=>'required'
        ]); 
        
      
        $car= new Car();

        $car->carNumber=$request->input('carNumber');
        $car->status=1;


        $car->save();

        return redirect()->route('car.index')->with('success', 'Saved Successfully');


    }

    public function search_store(Request $request)
    {
        //

            if($request->ajax())
            {
            $output="";

            $dt = Carbon::now();
            $month2 = $dt->subDays(30);

            $activity = "kusajiliwa";

            // dd($month2);

            // where('number','like','%'.$request['number'].'%');



            $customers = PacelHistory::where('mteja', request('search'))
                                        ->where('activity', $activity)
                                    ->where('created_at','>=',$month2)->get();

                                    

            return DataTables::of($customers)
            ->addIndexColumn()
            ->editColumn('added_by', function ($row) {

                // $customer_id = PacelHistory::where('');

                $user = User::where('id', $row->added_by)->first();
                $mwandishi = $user->name;
                    return $mwandishi;
                
            })
            ->editColumn('created_at', function ($row) {

                return $row->created_at->format('Y-m-d H:i:s');
            
            })
            ->addColumn('action', function($row) {
                return view('management.action', compact('row'));
            })
            ->rawColumns(['action'])
            ->make(true);
        
    
            }

        return view('management.customer_search');

    }

    public function test(Request $request){

        

    }

    public function customer_history(Request $request)
    {
            //
            if($request->ajax())
            {
            $output="";

            $dt = Carbon::now();
            $month2 = $dt->subDays(30);

            // $customers = CustomerPacel::where('delivery',request('search'))->where('created_at','>=',$month2)->get();

            $customers = CustomerPacel::where('delivery',request('search'))->get();

            return DataTables::of($customers)
            ->addIndexColumn()
            ->editColumn('added_by', function ($row) {

                $user = User::where('id', $row->added_by)->first();
                $mwandishi = $user->name;
                    return $mwandishi;  
                
            })
            ->editColumn('created_at', function ($row) {

                    return $row->created_at->format('Y-m-d H:i:s');
                
            })
            ->addColumn('action', function($row) {
                return view('management.delivery_action', compact('row'));
            })
            ->rawColumns(['action', 'added_by', 'created_at'])
            ->make(true);
        
    
            }

        return view('management.customer_history');

    
    }

    public function pacel_store(Request $request)
    {
            //
            // $customers = CustomerPacel::where('delivery',request('search'))->get();

            if($request->ajax())
            {
            $output="";

            $dt = Carbon::now();
            $month2 = $dt->subDays(30);

            // $customers = CustomerPacel::where('delivery',request('search'))->where('created_at','>=',$month2)->get();

            // $customers = CustomerPacel::all();

            $customers = CustomerPacel::where('idadi_stoo', '!=', '0')->get();


            return DataTables::of($customers)
            ->addIndexColumn()
            ->editColumn('bei', function ($row) {

                if(!empty($row->bei)){
                    return $row->bei;
                    

                }
                else{

                    $mwandishi = "0";
                    return $mwandishi;
                }
                
            })
            ->editColumn('created_at', function ($row) {

                return $row->created_at->format('Y-m-d H:i:s');
            
            })
            ->addColumn('action', function($row) {
                return view('management.pacel_action', compact('row'));
            })
            ->rawColumns(['action'])
            ->make(true);
        
    
            }

        return view('management.pacel_store');

    
    }

    public function pacel_edit($id){
        $pacel =  CustomerPacel::find($id);
        return response()->json([
            'status' =>200,
            'paceldata' =>$pacel,
        ]);
    }

    public function money_edit($id){
        $pacel =  CustomerPacel::find($id);
        return response()->json([
            'status' =>200,
            'paceldata2' =>$pacel,
        ]);
    }

    public function pacel_update(Request $request){
        $this->validate($request,[
            'name' => 'required',
            'idadi' => 'required',
            'bei' => 'required',
            'receipt' =>'required',
            'mzigo_unapotoka' =>   'required',
            'mzigo_unapokwenda' =>  'required',
        ]); 
        
             $pacel = CustomerPacel::find($request->input('pacel_id'));

             $pacel->mteja = $request->input('mteja');

            $pacel->mpokeaji = $request->input('mpokeaji');

            $pacel->name = $request->input('name');

            $pacel->idadi = $request->input('idadi');

            $pacel->bei = $request->input('bei');

            $pacel->receipt = $request->input('receipt');


            $pacel->ela_iliyopokelewa = $request->input('ela_iliyopokelewa');

            $pacel->jumla = $request->input('idadi') * $request->input('bei');

            $pacel->mzigo_unapotoka = $request->input('mzigo_unapotoka');

            $pacel->mzigo_unapokwenda = $request->input('mzigo_unapokwenda');
      
            $pacel->update();


            $pacel_history = PacelHistory::where('pacel_id', $request->input('pacel_id'))->get();

            foreach($pacel_history as $row){

            $data['mteja'] = $request->input('mteja');

            $data['mpokeaji'] = $request->input('mpokeaji');

            $data['name'] = $request->input('name');

            $data['idadi'] = $request->input('idadi');

            $data['bei'] = $request->input('bei');

            $data['receipt'] = $request->input('receipt');


            $data['ela_iliyopokelewa'] = $request->input('ela_iliyopokelewa');

            $data['jumla'] = $request->input('idadi') * $request->input('bei');

            $data['mzigo_unapotoka'] = $request->input('mzigo_unapotoka');

            $data['mzigo_unapokwenda'] = $request->input('mzigo_unapokwenda');
      
            $pcl =  $row->update($data);

            }

        return redirect()->route('pacel_store')->with('success', 'Updated Successfully');
    }

    public function pacel_delete($id){

        $pacel = CustomerPacel::find(intval($id));

        // dd($id);

        if(!empty($pacel)){
            $pacel_history = PacelHistory::where('pacel_id', $id)->get();

            foreach($pacel_history as $row){
                $data = $row->delete();
            }

            $data22 = $pacel->delete();

            // dd($data22);

            return redirect()->route('pacel_store')->with('success', 'Delete Successfully');


        }
        else{

            return redirect()->route('pacel_store')->with('success', 'Failed to Delete');

        }




    }

    public function money_update(Request $request){
        $this->validate($request,[
            'ela_iliyopokelewa' => 'required',
        ]); 
        
             $pacel = CustomerPacel::find($request->input('pacel_id'));

            $pacel->ela_iliyopokelewa = $request->input('ela_iliyopokelewa');
      
            $pacel->update();

        return redirect()->route('pacel_store')->with('success', 'Received Money Successfully');
    }

    public function car_today_routes(Request $request, $id){

        // $data = Car::find($id);

        $carNumber = Car::find(intval($id))->carNumber;
        
        $carId = $id;

        $dataResult = DriverRoute::where('car_id', $id)->groupBy(DB::RAW('DATE(start_date)'))->groupBy(DB::RAW('DATE(closeDate)'))->orderBy('created_at', 'ASC')->get();

        // dd($dataResult);

        // dd($dataResult);


        // $response=['success'=>true,'error'=>false,'message'=>'successfully','cars'=>$dataResult];
        // return response()->json($response,200);
        return view('management.car_today_routes', compact('carNumber', 'dataResult'));
    }

    public function test1(){
        return view('management.test');
    }

    public function car_pacel_detail(int $id, String $startDate, String $endDate){
        if($endDate == "0000-00-00"){

            $nowDate = Carbon::now()->format('Y-m-d');


            
        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $nowDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $nowDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->get(['pacel_id']);
        $total = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $nowDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $nowDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $nowDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');

                $dt = $startDate;

                $dt34 = $nowDate;        


        }
        else{

            
        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->get(['pacel_id']);
        $total = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');

        $dt = $startDate;

        $dt34 = $endDate;

        }
        
        $carNumber = Car::find($id)->carNumber;
        
        $carId = $id;
        return view('management.car_routes_pacel_detail', compact('pacelUnique','pacels', 'carNumber', 'total', 'total_paid','total_pacel', 'dt', 'carId', 'dt34'));
    }

    public function print_manifest($id){

        $customers = CustomerPacel::where('id',$id)->firstorFil();


        return redirect()->route ('customer_history');

    }
    // public function gymkhana_test(){

    //     // $data = PacelHistory::where('id', $id)->first();

    //     //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
    //     $customPaper = array(0,0,198.425,494.80);

    //     $pdf = PDF::loadView('management.gymkahana_pdf')->setPaper($customPaper, 'portrait');
    //     return $pdf->stream('gymkhana_test.pdf');

    // }

    // public function gymkhana_test2(){

    //     // $data = PacelHistory::where('id', $id)->first();

    //     //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
    //     $customPaper = array(0,0,198.425,494.80);

    //     $pdf = PDF::loadView('management.gymkahana2_pdf')->setPaper($customPaper, 'portrait');
    //     return $pdf->stream('gymkhana_test2.pdf');

    // }

    public function print_pacel_registered($id){

        $data = PacelHistory::where('id', $id)->first();

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        $pdf = PDF::loadView('management.pacel_registered_pdf', compact('data'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('pacel_details.pdf');

    }

    public function pacel_reg($customer_ID){

        $result = "kusajiliwa";
        $dataResult = CustomerPacel::where('customer_id', $customer_ID)->where('activity', $result)->get();

        $dataResult2 = Customer::where('id', $customer_ID)->first();

        // dd($dataResult);

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        $pdf = PDF::loadView('dashboard.pacel_reg', compact('dataResult','dataResult2'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('pacel_reg_details.pdf');

    }

    public function car_manifest($id, $startDate, $endDate){

        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->take(80)
                    ->get(['pacel_id']);
        $value = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');
                         

        $total_customers_list = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('activity','kupakia')
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->get()
                                    ->unique('mteja');

        $to_list = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('activity','kupakia')
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->get()
                                    ->unique('mzigo_unapokwenda');

        $total_customers = count($total_customers_list); 

        $printed = Carbon::now();

        $driverIDDD = DriverRoute::where('start_date', $startDate)->first();

        $driver = Driver::find($driverIDDD->driver_id)->name;

        if(!empty(Driver::find($driverIDDD->driver_id)->phone)){

            $dr_no = Driver::find($driverIDDD->driver_id)->phone;
        }
        else{

            $dr_no =  null;
        }

        

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);
        
        $carNumber = Car::find($id)->carNumber;

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_manifest_pdf', compact('pacelUnique','pacels', 'carNumber', 'to_list', 'value', 'total_paid', 'total_pacel', 'total_customers', 'printed', 'driver', 'dr_no'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_manifest.pdf');

    }
    
    public function car_manifest22($id, $startDate, $endDate){

        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(80)
                    ->take(80)
                    ->get(['pacel_id']);
        $value = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');

                                  

        $total_customers_list = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('activity','kupakia')
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->get()
                                    ->unique('mteja');

        $to_list = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('activity','kupakia')
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->get()
                                    ->unique('mzigo_unapokwenda');

        $total_customers = count($total_customers_list); 

        $printed = Carbon::now();

        $driverIDDD = DriverRoute::where('start_date', $startDate)->first();

        $driver = Driver::find($driverIDDD->driver_id)->name;

        if(!empty(Driver::find($driverIDDD->driver_id)->phone)){

            $dr_no = Driver::find($driverIDDD->driver_id)->phone;
        }
        else{

            $dr_no =  null;
        }

        

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);
        
        $carNumber = Car::find($id)->carNumber;

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_manifest_pdf22', compact('pacelUnique','pacels', 'carNumber', 'to_list', 'value', 'total_paid', 'total_pacel', 'total_customers', 'printed', 'driver', 'dr_no'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_manifest22.pdf');

    }
    
    public function car_manifest23($id, $startDate, $endDate){

        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(160)
                    ->take(80)
                    ->get(['pacel_id']);
        $value = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');


        $total_customers_list = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('activity','kupakia')
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->get()
                                    ->unique('mteja');

        $to_list = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('activity','kupakia')
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->get()
                                    ->unique('mzigo_unapokwenda');

        $total_customers = count($total_customers_list); 

        $printed = Carbon::now();

        $driverIDDD = DriverRoute::where('start_date', $startDate)->first();

        $driver = Driver::find($driverIDDD->driver_id)->name;

        if(!empty(Driver::find($driverIDDD->driver_id)->phone)){

            $dr_no = Driver::find($driverIDDD->driver_id)->phone;
        }
        else{

            $dr_no =  null;
        }

      

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);
        
        $carNumber = Car::find($id)->carNumber;

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_manifest_pdf23', compact('pacelUnique','pacels', 'carNumber', 'to_list', 'value', 'total_paid', 'total_pacel', 'total_customers', 'printed', 'driver', 'dr_no'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_manifest23.pdf');

    }
    
    public function car_manifest24($id, $startDate, $endDate){

        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(240)
                    ->take(80)
                    ->get(['pacel_id']);
        $value = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');


        $total_customers_list = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('activity','kupakia')
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->get()
                                    ->unique('mteja');

        $to_list = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('activity','kupakia')
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->get()
                                    ->unique('mzigo_unapokwenda');

        $total_customers = count($total_customers_list); 

        $printed = Carbon::now();

        $driverIDDD = DriverRoute::where('start_date', $startDate)->first();

        $driver = Driver::find($driverIDDD->driver_id)->name;

        if(!empty(Driver::find($driverIDDD->driver_id)->phone)){

            $dr_no = Driver::find($driverIDDD->driver_id)->phone;
        }
        else{

            $dr_no =  null;
        }


        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);
        
        $carNumber = Car::find($id)->carNumber;

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_manifest_pdf24', compact('pacelUnique','pacels', 'carNumber', 'to_list', 'value', 'total_paid', 'total_pacel', 'total_customers', 'printed', 'driver', 'dr_no'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_manifest24.pdf');

    }
    
    
    public function car_manifestAll($id, $startDate, $endDate){

        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->get(['pacel_id']);
        $value = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');


        $total_customers_list = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('activity','kupakia')
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->get()
                                    ->unique('mteja');

        $to_list = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('activity','kupakia')
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->get()
                                    ->unique('mzigo_unapokwenda');

        $total_customers = count($total_customers_list); 

        $printed = Carbon::now();

        $driverIDDD = DriverRoute::where('start_date', $startDate)->first();

        $driver = Driver::find($driverIDDD->driver_id)->name;

        if(!empty(Driver::find($driverIDDD->driver_id)->phone)){

            $dr_no = Driver::find($driverIDDD->driver_id)->phone;
        }
        else{

            $dr_no =  null;
        }


        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);
        
        $carNumber = Car::find($id)->carNumber;

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_manifest_pdfAll', compact('pacelUnique','pacels', 'carNumber', 'to_list', 'value', 'total_paid', 'total_pacel', 'total_customers', 'printed', 'driver', 'dr_no'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_manifestAll.pdf');

    }

    public function car_invoice($id, $startDate, $endDate){
        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->take(120)
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf', compact('pacelUnique','pacels', 'car'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_invoice.pdf');

    }
    
    public function car_invoice22($id, $startDate, $endDate){
        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(120)
                    ->take(120)
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf22', compact('pacelUnique','pacels', 'car'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_invoice.pdf');

    }
    
    public function car_invoice23($id, $startDate, $endDate){
        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(240)
                    ->take(120)
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf23', compact('pacelUnique','pacels', 'car'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_invoice.pdf');

    }
    
    public function car_invoice24($id, $startDate, $endDate){
        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(360)
                    ->take(120)
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf24', compact('pacelUnique','pacels', 'car'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_invoice.pdf');

    }
    
    public function car_invoiceAll($id, $startDate, $endDate){
        $pacels = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('idadi_kupakia', '!=', '0')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()    
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdfAll', compact('pacelUnique','pacels', 'car'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_invoice.pdf');

    }

    public function car_single_invoice($id, $startDate, $endDate,  $pacel_id){
        $pacels = PacelHistory::where('car_id', $id)
        ->where('idadi_kupakia', '!=', '0')
        ->whereDate('created_at', '>=',$startDate)
        ->whereDate('created_at', '<=', $endDate)
        ->get();
        // $pacelUnique = PacelHistory::where('car_id', $id)
        // ->whereDate('created_at', '>=',$startDate)
        // ->whereDate('created_at', '<=', $endDate)
        // ->where('idadi_kupakia', '!=', '0')
        // ->orderBy('pacel_id', 'DESC')
        // ->distinct()
        // ->get(['pacel_id']);
        // $dataResult = DriverRoute::where('car_id', $id)->orderBy('created_at', 'DESC')->get();
        // $pacels = PacelHistory::where('car_id', $id)->whereDate('created_at',$date)
        //                         ->get();
        $pacelUnique = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=',$startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->where('pacel_id', $pacel_id)
                                    ->where('idadi_kupakia', '!=', '0')
                                    ->first();

        // $info = PacelHistory::where('car_id', $id)->whereDate('created_at',$date)
        //                             ->latest()->first()->driver_id;  
        // $driver = Driver::find($info)->name;
        
        $car = Car::find($id)->carNumber;

  //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        $pdf = PDF::loadView('management.car_single_invoice_pdf', compact('pacelUnique','pacels', 'car'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('car_single_invoice.pdf');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Management\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $data = null;
        $data = CustomerPacel::where('id', $id)->first();

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,694.80);

        $pdf = PDF::loadView('management.manifest_pdf',
        compact('data'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('delivery_ticket_store.pdf');

    }

    //delivery_show  

    public function delivery_show($id){

        $result = "kusajiliwa";

        $data = PacelHistory::where('pacel_id', $id)->where('activity', $result)->first();

        $data2 = CustomerPacel::where('id', $id)->first();

        $result = "kupakia";


        $data3 = PacelHistory::where('pacel_id', $id)->where('activity', $result)->get();




        return view('management.delivery_show', compact('data', 'data2', 'data3'));
    }

//customer_show  
    public function customer_show($id){

        $result = "kusajiliwa";

        $data = PacelHistory::where('id', $id)->where('activity', $result)->first();

        $data2 = CustomerPacel::where('id', $data->pacel_id)->first();

        $result = "kupakia";


        $data3 = PacelHistory::where('pacel_id', $id)->where('activity', $result)->get();




        return view('management.customer_show', compact('data', 'data2', 'data3'));
    }

    //pacel_show

    public function pacel_show($id){

        $result = "kusajiliwa";

        $data = PacelHistory::where('pacel_id', $id)->where('activity', $result)->first();

        $data2 = CustomerPacel::where('id', $id)->first();

        $result = "kupakia";


        $data3 = PacelHistory::where('pacel_id', $id)->where('activity', $result)->get();




        return view('management.pacel_show', compact('data', 'data2', 'data3'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Management\Car  $car
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $car = Car::find($id);

        return view('management.edit_car', compact('car'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Management\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Car $car)
    {
        //
        $this->validate($request,[
            'carNumber'=>'required'
        ]); 
        
      
        $car->update($request->all());

        return redirect()->route('car.index')->with('success', 'Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Management\Car  $car
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $data=Car::find($id);
        $data->delete();

        return redirect()->route('car.index')->with('success', 'Deleted Successfully');

    }
}
