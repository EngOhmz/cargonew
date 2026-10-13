<?php

namespace App\Http\Controllers\Management;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\PrintsDocuments;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerPacel;
use App\Models\DriverRoute;
use App\Models\RouteExpense;
use App\Models\Management\Car;
use App\Models\Management\Driver;
use Illuminate\Support\Facades\View;
use App\Models\PacelHistory;
use App\Models\User;
use App\Models\DocumentSetting;
use App\Support\ParcelQuantitySummary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use PDF;

class CarController extends Controller
{
    use PrintsDocuments;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $today = Carbon::today();
        // Filter by company_id
        $companyId = auth()->user()->company_id;
        $cars = Car::where('company_id', $companyId)->get();

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

        // Filter by company_id
        $companyId = auth()->user()->company_id;
        $driverRoute = Car::where('company_id', $companyId)
            ->where('status', '2')
            ->get();
        // dd($driverRoute);


        return view('management.car_today', compact('driverRoute'));


    }

    public function arrived(){
        // Filter by company_id
        $companyId = auth()->user()->company_id;
        $cars = Car::where('company_id', $companyId)
            ->where('status', 1)
            ->get();


        return view('management.arrived_car', compact('cars'));


    }

    public function arrived_car_store(Request $request){

        $date = $request->dateT;
        
        // Filter by company_id
        $companyId = auth()->user()->company_id;

        $cars =  DriverRoute::where('company_id', $companyId)
                                    ->whereDate('created_at', $date)
                                    ->where('status', '!=', '2')
                                    ->get()
                                    ->unique('car_id');
        
        return view('search_view', compact('cars'));
    
    }                                

    public function all(Request $request){
        if ($request->ajax()) {
            // Filter by company_id
            $companyId = auth()->user()->company_id;
            
            $cars = Car::select('cars.*')
                ->where('cars.company_id', $companyId);

            // Join with DriverRoute if source or destination filters are present
            if ($request->filled('source') || $request->filled('destination')) {
                $cars->join('driver_routes', 'cars.id', '=', 'driver_routes.car_id')
                     ->where(function($q) use ($request) {
                        if ($request->filled('source')) {
                            $q->where('driver_routes.from', $request->source);
                        }
                        if ($request->filled('destination')) {
                            $q->where('driver_routes.to', $request->destination);
                        }
                     })
                     ->distinct();
            }
            
            $cars->orderBy('cars.created_at', 'desc');
            
            return Datatables::of($cars)
                ->addIndexColumn()
                ->make(true);
        }
        
        $mikoa = \App\Models\Mikoa::where('company_id', auth()->user()->company_id)->orderBy('regional_name', 'asc')->get();
        return view('management.all_car', compact('mikoa'));
    }

    /**
     * Active cars = currently packing (status 2 / Inapakiwa).
     */
    public function active(Request $request){
        if ($request->ajax()) {
            $companyId = auth()->user()->company_id;

            $cars = Car::select('cars.*')
                ->where('cars.company_id', $companyId)
                ->whereIn('cars.status', ['2', 2]);

            if ($request->filled('source') || $request->filled('destination')) {
                $cars->join('driver_routes', 'cars.id', '=', 'driver_routes.car_id')
                     ->where(function ($q) use ($request) {
                        if ($request->filled('source')) {
                            $q->where('driver_routes.from', $request->source);
                        }
                        if ($request->filled('destination')) {
                            $q->where('driver_routes.to', $request->destination);
                        }
                     })
                     // Prefer open packing trip for this car
                     ->where(function ($q) {
                         $q->whereNull('driver_routes.closeDate')
                           ->orWhere('driver_routes.closeDate', '0000-00-00');
                     })
                     ->distinct();
            }

            $cars->orderBy('cars.updated_at', 'desc')
                 ->orderBy('cars.created_at', 'desc');

            return Datatables::of($cars)
                ->addIndexColumn()
                ->make(true);
        }

        $mikoa = \App\Models\Mikoa::where('company_id', auth()->user()->company_id)->orderBy('regional_name', 'asc')->get();
        $activeOnly = true;

        return view('management.active_car', compact('mikoa', 'activeOnly'));
    }

    public function old(){
        // Filter by company_id
        $companyId = auth()->user()->company_id;
        $cars = Car::where('company_id', $companyId)->get();

        return view('management.old_car', compact('cars'));


    }

    public function car_routes(){
        // Filter by company_id
        $companyId = auth()->user()->company_id;
        $cars = Car::where('company_id', $companyId)->get();

        return view('management.car_routes', compact('cars'));


    }

    /**
     * Show and manage expenses (matumizi) for a given driver route.
     */
    public function route_expenses(Request $request, int $driverRouteId)
    {
        $companyId = auth()->user()->company_id;

        $route = DriverRoute::with(['car', 'driver'])
            ->where('id', $driverRouteId)
            ->firstOrFail();

        // Optional: enforce company filter via car
        if ($route->car && $route->car->company_id && (int)$route->car->company_id !== (int)$companyId) {
            abort(403, 'You are not allowed to view expenses for this route.');
        }

        $expensesQuery = RouteExpense::where('driver_route_id', $driverRouteId);
        if ($companyId) {
            $expensesQuery->where('company_id', $companyId);
        }

        $expenses = $expensesQuery
            ->orderBy('expense_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalAmount = $expenses->sum('amount');

        return view('management.route_expenses', [
            'route' => $route,
            'expenses' => $expenses,
            'totalAmount' => $totalAmount,
        ]);
    }

    /**
     * Store a new expense for a route.
     */
    public function route_expenses_store(Request $request)
    {
        $request->validate([
            'driver_route_id' => 'required|integer|exists:driver_routes,id',
            'car_id' => 'required|integer',
            'driver_id' => 'nullable|integer',
            'expense_date' => 'nullable|date',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();
        $companyId = $user->company_id;

        RouteExpense::create([
            'driver_route_id' => $request->driver_route_id,
            'car_id' => $request->car_id,
            'driver_id' => $request->driver_id,
            'company_id' => $companyId,
            'expense_date' => $request->expense_date ?: now()->toDateString(),
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'created_by' => $user->id,
        ]);

        return redirect()
            ->route('route_expenses.show', $request->driver_route_id)
            ->with('success', 'Expense saved successfully.');
    }

    /**
     * Web overview of routes and their drop off points (web version of drop_off_point_list API).
     * Shows, per route:
     * - Car (carNumber)
     * - From (inapotoka) and To (inapokwenda)
     * - Driver
     * - All drop off points for that route
     * - For each drop off point, whether it has any dropped parcels (kushusha) already.
     */
    public function routes_overview(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Base query for routes for this company
        $routesQuery = DriverRoute::query()
            ->join('cars', 'cars.id', '=', 'driver_routes.car_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'driver_routes.driver_id')
            ->select(
                'driver_routes.id',
                'driver_routes.car_id',
                'driver_routes.driver_id',
                'driver_routes.from',
                'driver_routes.to',
                'driver_routes.status',
                'driver_routes.closeDate',
                'cars.carNumber as car_name',
                'drivers.name as driver_name'
            )
            ->where('cars.company_id', $companyId)
            ->orderBy('driver_routes.created_at', 'DESC');

        $routes = $routesQuery->get();

        if ($routes->isEmpty()) {
            return view('management.routes_overview', [
                'routes' => collect(),
                'dropsByRoute' => [],
            ]);
        }

        $routeIds = $routes->pluck('id')->all();

        // Fetch drop off points for these routes
        $dropPoints = \App\Models\DropOffPoint::whereIn('driver_route_id', $routeIds)
            ->where('company_id', $companyId)
            ->get();

        // Fetch drop_off_parcels with parcel details per drop_off_point and destination
        $drops = DB::table('drop_off_parcels')
            ->join('customer_pacels', 'drop_off_parcels.customer_pacel_id', '=', 'customer_pacels.id')
            ->select(
                'drop_off_parcels.driver_route_id',
                'drop_off_parcels.drop_off_point_id',
                'drop_off_parcels.is_destination',
                'drop_off_parcels.idadi_kushusha',
                'drop_off_parcels.amount_received',
                'customer_pacels.name as pacel_name',
                'customer_pacels.hashtag',
                'customer_pacels.id as pacel_id'
            )
            ->whereIn('drop_off_parcels.driver_route_id', $routeIds)
            ->get();

        // Index drops by drop_off_point_id for quick lookup (normal drop points)
        // and separately aggregate destination drops (is_destination = 1) per route
        $dropsByPoint = [];
        $destDropsByRoute = [];
        foreach ($drops as $dr) {
            $parcelInfo = [
                'name' => $dr->pacel_name,
                'hashtag' => $dr->hashtag ?: $dr->pacel_id,
                'id' => $dr->pacel_id,
                'dropped' => $dr->idadi_kushusha,
                'received' => $dr->amount_received
            ];

            if ((int) $dr->is_destination === 1) {
                if (!isset($destDropsByRoute[$dr->driver_route_id])) {
                    $destDropsByRoute[$dr->driver_route_id] = [
                        'total_dropped' => 0,
                        'total_received' => 0,
                        'parcels' => []
                    ];
                }
                $destDropsByRoute[$dr->driver_route_id]['total_dropped'] += $dr->idadi_kushusha;
                $destDropsByRoute[$dr->driver_route_id]['total_received'] += $dr->amount_received;
                $destDropsByRoute[$dr->driver_route_id]['parcels'][] = $parcelInfo;
            } else {
                if (!isset($dropsByPoint[$dr->drop_off_point_id])) {
                    $dropsByPoint[$dr->drop_off_point_id] = [
                        'total_dropped' => 0,
                        'total_received' => 0,
                        'parcels' => []
                    ];
                }
                $dropsByPoint[$dr->drop_off_point_id]['total_dropped'] += $dr->idadi_kushusha;
                $dropsByPoint[$dr->drop_off_point_id]['total_received'] += $dr->amount_received;
                $dropsByPoint[$dr->drop_off_point_id]['parcels'][] = $parcelInfo;
            }
        }

        // Group drop points by route and attach drop status (partial/full)
        $dropsByRoute = [];
        foreach ($dropPoints as $dp) {
            $pointData = $dropsByPoint[$dp->id] ?? null;
            $hasDrop = $pointData && $pointData['total_dropped'] > 0;
            $dropsByRoute[$dp->driver_route_id][] = [
                'id' => $dp->id,
                'drop_off_point' => $dp->drop_off_point,
                'car_name' => $dp->car ? $dp->car->carNumber : null,
                'driver_name' => $dp->driver ? $dp->driver->name : null,
                'has_drop' => $hasDrop,
                'total_dropped' => $hasDrop ? $pointData['total_dropped'] : 0,
                'total_received' => $hasDrop ? $pointData['total_received'] : 0,
                'parcels' => $hasDrop ? $pointData['parcels'] : [],
                'is_destination' => false,
            ];
        }

        // Add a synthetic "destination" drop-off row per route when is_destination = 1 exists.
        // Use driver_routes.to (destination) as the drop-off label and route's car/driver.
        foreach ($routes as $route) {
            if (isset($destDropsByRoute[$route->id])) {
                $info = $destDropsByRoute[$route->id];
                $hasDrop = $info['total_dropped'] > 0;
                $dropsByRoute[$route->id][] = [
                    'id' => null,
                    'drop_off_point' => $route->to, // destination (inapokwenda)
                    'car_name' => $route->car_name,
                    'driver_name' => $route->driver_name,
                    'has_drop' => $hasDrop,
                    'total_dropped' => $info['total_dropped'],
                    'total_received' => $info['total_received'],
                    'parcels' => $info['parcels'],
                    'is_destination' => true,
                ];
            }
        }

        return view('management.routes_overview', [
            'routes' => $routes,
            'dropsByRoute' => $dropsByRoute,
        ]);
    }

    public function car_pacel(){
        // Filter by company_id
        $companyId = auth()->user()->company_id;
        $cars = Car::where('company_id', $companyId)->get();

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
        
      
        // Set company_id from logged-in user
        $companyId = auth()->user()->company_id;
        
        $car= new Car();

        $car->carNumber=$request->input('carNumber');
        $car->status=1;
        $car->company_id = $companyId; // Set company_id


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
        if ($request->ajax()) {
            $companyId = auth()->user()->company_id ?? null;

            // DataTables sends search as search[value]
            $searchRaw = $request->input('search.value', $request->input('search'));
            $search = is_array($searchRaw) ? trim((string) ($searchRaw['value'] ?? '')) : trim((string) ($searchRaw ?? ''));

            $query = CustomerPacel::query()
                ->with(['customer', 'car'])
                ->when($companyId, function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->orderByDesc('created_at');

            if ($search !== '') {
                $needle = $search;
                // Allow searching RG-001 / RG001 / 001 style refs
                $normalized = preg_replace('/^RG-?/i', '', $needle);

                $query->where(function ($q) use ($needle, $normalized) {
                    $q->where('delivery', 'like', '%' . $needle . '%')
                        ->orWhere('hashtag', 'like', '%' . $needle . '%')
                        ->orWhere('hashtag', 'like', '%' . $normalized . '%')
                        ->orWhere('mteja', 'like', '%' . $needle . '%')
                        ->orWhere('mpokeaji', 'like', '%' . $needle . '%')
                        ->orWhere('name', 'like', '%' . $needle . '%')
                        ->orWhereHas('customer', function ($cq) use ($needle) {
                            $cq->where('unique_identifier', 'like', '%' . $needle . '%');
                        });
                });
            } else {
                // Show nothing until user searches by reference number
                $query->whereRaw('1 = 0');
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('delivery', function ($row) {
                    $ref = $row->delivery ?: ($row->hashtag ? 'RG-' . $row->hashtag : ('#' . $row->id));
                    return e($ref);
                })
                ->editColumn('added_by', function ($row) {
                    $user = User::find($row->added_by);
                    return $user ? e($user->name) : '—';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
                })
                ->addColumn('status_label', function ($row) {
                    $label = \App\Support\ParcelStatusLabel::label(
                        $row->activity,
                        optional($row->car)->status,
                        $row->idadi_shusha,
                        $row->idadi
                    );
                    return '<span class="badge badge-primary">' . e($label) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return view('management.delivery_action', compact('row'))->render();
                })
                ->rawColumns(['action', 'added_by', 'created_at', 'status_label', 'delivery'])
                ->make(true);
        }

        return view('management.customer_history');
    }

    public function pacel_store(Request $request)
    {
            if (!auth()->check()) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                return redirect()->route('login');
            }

            $user = auth()->user();
            if (!$user->company_id) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'User does not have a company assigned'], 403);
                }
                return redirect()->back()->with('error', 'User does not have a company assigned');
            }

            $companyId = $user->company_id;

            if ($request->ajax()) {
                $customers = CustomerPacel::where('company_id', $companyId)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->filter(function ($row) {
                        return ParcelQuantitySummary::breakdown($row)['has_remaining_stoo'];
                    })
                    ->values();

                return DataTables::of($customers)
                    ->addIndexColumn()
                    ->addColumn('stoo_display', function ($row) {
                        $summary = ParcelQuantitySummary::breakdown($row);
                        $html = '<strong class="text-primary">' . $summary['stoo'] . '</strong>';
                        if ($summary['is_partially_loaded']) {
                            $html .= ' <small class="text-muted">/ ' . $summary['total'] . ' jumla</small>';
                        }
                        return $html;
                    })
                    ->editColumn('bei', function ($row) {
                        return !empty($row->bei) ? $row->bei : '0';
                    })
                    ->editColumn('jumla', function ($row) {
                        $summary = ParcelQuantitySummary::breakdown($row);
                        $bei = (float) ($row->bei ?? 0);
                        return number_format($summary['stoo'] * $bei, 0, '.', ',');
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('Y-m-d H:i:s');
                    })
                    ->addColumn('action', function ($row) {
                        return view('management.pacel_action', ['row' => $row, 'isDebtPage' => false]);
                    })
                    ->rawColumns(['action', 'stoo_display'])
                    ->make(true);
            }

        return view('management.pacel_store');
    }

    public function outstanding_debts(Request $request)
    {
        if (!auth()->check()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();
        if (!$user->company_id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'User does not have a company assigned'], 403);
            }
            return redirect()->back()->with('error', 'User does not have a company assigned');
        }

        $companyId = $user->company_id;

        $toFloat = function ($val): float {
            return (float) str_replace(',', '', (string) ($val ?? 0));
        };

        $calcDue = function (CustomerPacel $pacel) use ($toFloat): float {
            $jumla = $toFloat($pacel->jumla);
            $received = $toFloat($pacel->ela_iliyopokelewa);
            return max(0, $jumla - $received);
        };

        if ($request->ajax()) {
            $customerMpokeaji = trim((string) $request->input('customer_mpokeaji', ''));

            $query = CustomerPacel::where('company_id', $companyId)
                ->orderBy('created_at', 'desc');

            if ($customerMpokeaji !== '') {
                $query->where('mpokeaji', $customerMpokeaji);
            }

            $pacels = $query->get()
                ->filter(function ($row) use ($calcDue) {
                    return $calcDue($row) > 0.0001;
                })
                ->values();

            return DataTables::of($pacels)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return optional($row->created_at)->format('Y-m-d H:i:s');
                })
                ->editColumn('jumla', function ($row) use ($toFloat) {
                    return number_format($toFloat($row->jumla), 0, '.', ',');
                })
                ->editColumn('ela_iliyopokelewa', function ($row) use ($toFloat) {
                    return number_format($toFloat($row->ela_iliyopokelewa), 0, '.', ',');
                })
                ->addColumn('hela_iliyobaki', function ($row) use ($calcDue) {
                    return number_format($calcDue($row), 0, '.', ',');
                })
                ->addColumn('action', function ($row) {
                    return view('management.pacel_action', ['row' => $row, 'isDebtPage' => true]);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $pacelsAll = CustomerPacel::where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($row) use ($calcDue) {
                return $calcDue($row) > 0.0001;
            })
            ->values();

        $customersDue = $pacelsAll
            ->groupBy('mpokeaji')
            ->map(function ($rows, $mpokeaji) use ($calcDue, $toFloat) {
                $totalDue = (float) $rows->sum(function ($r) use ($calcDue) {
                    return $calcDue($r);
                });

                $unpaidCount = $rows->filter(function ($r) use ($toFloat) {
                    return $toFloat($r->ela_iliyopokelewa) <= 0.0001;
                })->count();

                $partialCount = $rows->filter(function ($r) use ($toFloat, $calcDue) {
                    $received = $toFloat($r->ela_iliyopokelewa);
                    return $received > 0.0001 && $calcDue($r) > 0.0001;
                })->count();

                return [
                    'mpokeaji' => $mpokeaji,
                    'parcel_count' => $rows->count(),
                    'total_due' => $totalDue,
                    'unpaid_count' => $unpaidCount,
                    'partial_count' => $partialCount,
                ];
            })
            ->sortByDesc('total_due')
            ->values();

        $grandTotalDue = (float) $pacelsAll->sum(function ($r) use ($calcDue) {
            return $calcDue($r);
        });

        return view('management.outstanding_debts', [
            'customersDue' => $customersDue,
            'totalDue' => $grandTotalDue,
            'dueParcelsCount' => $pacelsAll->count(),
            'dueCustomersCount' => $customersDue->count(),
        ]);
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

        return redirect()->back()->with('success', 'Updated Successfully');
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

            return redirect()->back()->with('success', 'Delete Successfully');


        }
        else{

            return redirect()->back()->with('success', 'Failed to Delete');

        }




    }

    public function money_update(Request $request){
        $this->validate($request,[
            'amount_received' => 'required|numeric|min:0',
        ]);

        $pacel = CustomerPacel::find($request->input('pacel_id'));
        if (!$pacel) {
            return redirect()->back()->with('error', 'Parcel haijapatikana');
        }

        $toFloat = function ($val): float {
            return (float) str_replace(',', '', (string) ($val ?? 0));
        };

        $currentReceived = $toFloat($pacel->ela_iliyopokelewa);
        $total = $toFloat($pacel->jumla);
        $due = max(0, $total - $currentReceived);

        $amountReceivedNow = $toFloat($request->input('amount_received'));

        if ($amountReceivedNow > $due + 0.01) {
            return redirect()->back()
                ->withErrors(['amount_received' => 'Kiasi hiki kinazidi deni lililobaki.']);
        }

        // Web input = "amount received now"; app input also increments, so they stay consistent.
        $newReceived = $currentReceived + $amountReceivedNow;
        $pacel->ela_iliyopokelewa = (string) $newReceived;
        $pacel->activity = 'malipo';
        $pacel->update();

        // Sync ela_iliyopokelewa on all existing PacelHistory rows for this parcel
        PacelHistory::where('pacel_id', $pacel->id)
            ->update(['ela_iliyopokelewa' => (string) $newReceived]);

        // Create a new PacelHistory record recording this payment event
        $userId = auth()->check() ? auth()->id() : 0;
        $companyId = auth()->user()->company_id ?? null;
        PacelHistory::create([
            'pacel_id'            => $pacel->id,
            'mteja'               => $pacel->mteja,
            'mpokeaji'            => $pacel->mpokeaji,
            'name'                => $pacel->name,
            'idadi'               => $pacel->idadi,
            'bei'                 => $pacel->bei,
            'hashtag'             => $pacel->hashtag,
            'receipt'             => $pacel->receipt,
            'ela_iliyopokelewa'   => (string) $newReceived,
            'jumla'               => $pacel->jumla,
            'activity'            => 'malipo',
            'mzigo_unapotoka'     => $pacel->mzigo_unapotoka,
            'mzigo_unapokwenda'   => $pacel->mzigo_unapokwenda,
            'car_id'              => $pacel->car_id,
            'customer_id'         => $pacel->customer_id,
            'driver_id'           => $pacel->driver_id,
            'idadi_kupakia'       => $pacel->idadi_kupakia,
            'idadi_shusha'        => 0,
            'delivery'            => $pacel->delivery,
            'status'              => $pacel->status,
            'added_by'            => $userId,
            'company_id'          => $companyId,
        ]);

        return redirect()->back()->with('success', 'Malipo yamepokelewa Successfully');
    }

    public function car_today_routes(Request $request, $id){

        // Optimize: Eager load relationships to avoid N+1 queries
        $car = Car::find(intval($id));
        if(!$car){
            return redirect()->back()->with('error', 'Car not found');
        }
        
        $carNumber = $car->carNumber;
        $carId = $id;

        // Fix: Use original groupBy approach but load driver relationship separately
        // groupBy with with() can cause SQL errors in strict mode, so load relationship after
        $dataResult = DriverRoute::where('car_id', $id)
                        ->groupBy(DB::RAW('DATE(start_date)'))
                        ->groupBy(DB::RAW('DATE(closeDate)'))
                        ->orderBy('created_at', 'DESC')
                        ->get();
        
        // Load driver relationship for all results to avoid N+1 queries
        if($dataResult->isNotEmpty()){
            $dataResult->load('driver');
        }

        // Pre-fetch and cache data that will be needed for manifest/invoice generation
        // This reduces load time when clicking print buttons
        $startDates = $dataResult->pluck('start_date')->unique()->filter();
        
        // Pre-load driver routes for all dates to avoid queries during PDF generation
        if($startDates->isNotEmpty()){
            DriverRoute::where('car_id', $id)
                ->whereIn('start_date', $startDates->toArray())
                ->with('driver')
                ->get();
        }

        return view('management.car_today_routes', compact('carNumber', 'dataResult'));
    }

    public function test1(){
        return view('management.test');
    }

    public function car_pacel_detail(int $id, String $startDate, String $endDate, Request $request){
        $source = $request->input('source');
        $destination = $request->input('destination');

        if($endDate == "0000-00-00"){
            $nowDate = Carbon::now()->format('Y-m-d');
        } else {
            $nowDate = $endDate;
        }

        // Base query for pacels
        $pacelsQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $nowDate);

        if ($request->filled('source')) {
            $pacelsQuery->where('mzigo_unapotoka', $request->source);
        }
        if ($request->filled('destination')) {
            $pacelsQuery->where('mzigo_unapokwenda', $request->destination);
        }

        $pacels = $pacelsQuery->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();

        $pacelUniqueQuery = PacelHistory::where('car_id', $id)
                    ->whereDate('created_at', '>=',$startDate)
                    ->whereDate('created_at', '<=', $nowDate)
                    ->where('idadi_kupakia', '!=', '0');
        
        if ($request->filled('source')) {
            $pacelUniqueQuery->where('mzigo_unapotoka', $request->source);
        }
        if ($request->filled('destination')) {
            $pacelUniqueQuery->where('mzigo_unapokwenda', $request->destination);
        }

        $pacelUnique = $pacelUniqueQuery->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->get(['pacel_id']);

        $summaryQuery = PacelHistory::where('car_id', $id)
                ->whereDate('created_at', '>=',$startDate)
                ->whereDate('created_at', '<=', $nowDate)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0');

        if ($request->filled('source')) {
            $summaryQuery->where('mzigo_unapotoka', $request->source);
        }
        if ($request->filled('destination')) {
            $summaryQuery->where('mzigo_unapokwenda', $request->destination);
        }

        $total = (clone $summaryQuery)->sum('jumla');
        $total_paid = (clone $summaryQuery)->sum('ela_iliyopokelewa');
        $total_pacel = (clone $summaryQuery)->sum('idadi_kupakia');

        $dt = $startDate;
        $dt34 = $nowDate;

        // Optimize: Get car in one query and check if exists
        $car = Car::find($id);
        $carNumber = $car ? $car->carNumber : 'N/A';
        
        $carId = $id;
        $mikoa = \App\Models\Mikoa::where('company_id', auth()->user()->company_id)->orderBy('regional_name', 'asc')->get();

        return view('management.car_routes_pacel_detail', compact('pacelUnique','pacels', 'carNumber', 'total', 'total_paid','total_pacel', 'dt', 'carId', 'dt34', 'mikoa'));
    }

    /**
     * Route report: income (paid from mzigo), matumizi (expenses), and profit/loss for a car/date range.
     */
    public function route_report(int $id, string $startDate, string $endDate)
    {
        $companyId = auth()->user()->company_id ?? null;

        $car = Car::where('id', $id)->first();
        if (!$car) {
            return redirect()->back()->with('error', 'Car not found.');
        }
        if ($companyId && $car->company_id && (int)$car->company_id !== (int)$companyId) {
            return redirect()->back()->with('error', 'You are not allowed to view this report.');
        }

        $nowDate = ($endDate === '0000-00-00') ? Carbon::now()->format('Y-m-d') : $endDate;
        $dt = $startDate;
        $dt34 = $nowDate;

        // Income: total paid (kusanywa) for this car in this date range (same as car_pacel_detail)
        $income = PacelHistory::where('car_id', $id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $nowDate)
            ->where('activity', 'kupakia')
            ->where('idadi_kupakia', '!=', '0')
            ->sum('ela_iliyopokelewa');

        // Driver routes for this car and date range (start or close in range)
        $routeIds = DriverRoute::where('car_id', $id)->where(function ($q) use ($startDate, $nowDate) {
            $q->whereBetween(DB::raw('DATE(start_date)'), [$startDate, $nowDate])
                ->orWhereBetween(DB::raw('DATE(closeDate)'), [$startDate, $nowDate]);
        })->pluck('id');

        $totalExpense = 0;
        if ($routeIds->isNotEmpty()) {
            $totalExpense = RouteExpense::whereIn('driver_route_id', $routeIds)->sum('amount');
        }

        $profit = (float) $income - (float) $totalExpense;
        $carNumber = $car->carNumber;

        return view('management.route_report', [
            'carNumber' => $carNumber,
            'carId' => $id,
            'dt' => $dt,
            'dt34' => $dt34,
            'income' => $income,
            'totalExpense' => $totalExpense,
            'profit' => $profit,
        ]);
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
    //     return $this->streamPdfInline($pdf,'gymkhana_test.pdf');

    // }

    // public function gymkhana_test2(){

    //     // $data = PacelHistory::where('id', $id)->first();

    //     //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
    //     $customPaper = array(0,0,198.425,494.80);

    //     $pdf = PDF::loadView('management.gymkahana2_pdf')->setPaper($customPaper, 'portrait');
    //     return $this->streamPdfInline($pdf,'gymkhana_test2.pdf');

    // }

    public function print_pacel_registered($id){
        $data = PacelHistory::where('id', $id)->first();
        $htmlData = array_merge(compact('data'), $this->getDocumentSettingsVars(true));
        $customPaper = array(0,0,198.425,894.80);

        return $this->printReceiptOrPdf(
            'management.pacel_registered_pdf',
            $htmlData,
            function () use ($data, $customPaper) {
                return PDF::loadView(
                    'management.pacel_registered_pdf',
                    array_merge(compact('data'), $this->getDocumentSettingsVars())
                )->setPaper($customPaper, 'portrait');
            },
            'pacel_registered.pdf'
        );
    }

    public function pacel_reg($customer_ID){
        // Receipt must show all parcels for this customer — not only activity=kusajiliwa.
        // After malipo / kupakia / kushusha, activity changes and the old filter made receipts empty.
        $dataResult = CustomerPacel::withoutTenantScope()
            ->where('customer_id', $customer_ID)
            ->when(auth()->check() && auth()->user()->company_id && !\App\Support\CompanyAccess::isPlatformAdmin(auth()->user()), function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            })
            ->orderBy('id')
            ->get();

        $dataResult2 = Customer::withoutTenantScope()
            ->where('id', $customer_ID)
            ->when(auth()->check() && auth()->user()->company_id && !\App\Support\CompanyAccess::isPlatformAdmin(auth()->user()), function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            })
            ->first();

        if (!$dataResult2) {
            abort(404, 'Mteja hajapatikana');
        }

        $htmlData = array_merge(compact('dataResult', 'dataResult2'), $this->deliveryNoteDocumentSettings(true));
        $customPaper = array(0,0,198.425,894.80);

        return $this->printReceiptOrPdf(
            'dashboard.pacel_reg',
            $htmlData,
            function () use ($dataResult, $dataResult2, $customPaper) {
                return PDF::loadView(
                    'dashboard.pacel_reg',
                    array_merge(compact('dataResult', 'dataResult2'), $this->deliveryNoteDocumentSettings(false))
                )->setPaper($customPaper, 'portrait');
            },
            'delivery_note.pdf'
        );
    }

    public function car_manifest($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // CACHE OPTIMIZATION: Create unique cache key for this manifest
        $cacheKey = "car_manifest_{$id}_{$startDate}_{$endDate}_" . ($destination ?: 'all');
        
        // Check if PDF is already cached (24 hours cache)
        $cachedPdf = Cache::get($cacheKey);
        if($cachedPdf){
            return $this->streamPdfBinaryInline($cachedPdf, 'car_manifest.pdf');
        }

        // PRODUCTION OPTIMIZATION: Use direct date comparison instead of whereDate() for better performance
        // whereDate() is slow on large tables, direct comparison uses indexes better
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';
        
        // Build base query once to reuse
        $baseQuery = PacelHistory::where('car_id', $id)
                        ->where('idadi_kupakia', '!=', '0')
                        ->whereBetween('created_at', [$startDateTime, $endDateTime]);
        
        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        
        $pacelUnique = (clone $baseQuery)
                    ->select('pacel_id')
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->take(80)
                    ->get(['pacel_id']);
        
        // PRODUCTION OPTIMIZATION: Combine all sum queries into one raw query for better performance
        $sumsQuery = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')
                ->selectRaw('
                    COALESCE(SUM(CAST(jumla AS DECIMAL(10,2))), 0) as total_value,
                    COALESCE(SUM(CAST(ela_iliyopokelewa AS DECIMAL(10,2))), 0) as total_paid,
                    COALESCE(SUM(CAST(idadi_kupakia AS UNSIGNED)), 0) as total_pacel
                ')
                ->first();
        
        $value = $sumsQuery->total_value ?? 0;
        $total_paid = $sumsQuery->total_paid ?? 0;
        $total_pacel = $sumsQuery->total_pacel ?? 0;

        // Optimize: Use distinct() in query instead of get()->unique() to avoid loading all records into memory
        $total_customers_list = (clone $baseQuery)
                                    ->where('activity','kupakia')
                                    ->distinct()
                                    ->pluck('mteja');

        // Keep as models since view expects $data->mzigo_unapokwenda property access
        $to_list = (clone $baseQuery)
                                    ->where('activity','kupakia')
                                    ->select('mzigo_unapokwenda')
                                    ->distinct()
                                    ->get();

        $total_customers = $total_customers_list->count(); 

        $printed = Carbon::now();

        // Optimize: Cache driver object to avoid multiple Driver::find() queries
        $driverIDDD = DriverRoute::where('car_id', $id)->where('start_date', $startDate)->first();

        if($driverIDDD && $driverIDDD->driver_id){
            $driverObj = Driver::find($driverIDDD->driver_id);
            if($driverObj){
                $driver = $driverObj->name;
                $dr_no = $driverObj->phone ?: null;
            } else {
                $driver = 'N/A';
                $dr_no = null;
            }
        } else {
            $driver = 'N/A';
            $dr_no = null;
        }

        // CRITICAL OPTIMIZATION: Pre-fetch all CustomerPacel data to avoid N+1 queries in PDF view
        // The view makes 4 queries per parcel (delivery, mteja, receipt, mzigo_unapokwenda)
        $pacelIds = $pacelUnique->pluck('pacel_id')->filter()->unique()->toArray();
        $customerPacels = [];
        if(!empty($pacelIds)){
            $customerPacelsData = CustomerPacel::whereIn('id', $pacelIds)
                ->select('id', 'delivery', 'mteja', 'mpokeaji', 'receipt', 'mzigo_unapokwenda')
                ->get();
            
            // Convert to array with proper structure for production compatibility
            foreach($customerPacelsData as $pacel){
                $customerPacels[$pacel->id] = [
                    'delivery' => $pacel->delivery ?? '',
                    'mteja' => $pacel->mpokeaji ?? ($pacel->mteja ?? ''),
                    'mpokeaji' => $pacel->mpokeaji ?? '',
                    'receipt' => $pacel->receipt ?? '',
                    'mzigo_unapokwenda' => $pacel->mzigo_unapokwenda ?? ''
                ];
            }
        }

        // CRITICAL OPTIMIZATION: Pre-fetch all PacelHistory created_at for kupakia activity
        // The view makes 1 query per parcel for created_at
        $pacelHistoryDates = [];
        if(!empty($pacelIds)){
            // PRODUCTION: Use raw query with GROUP BY for better performance on large datasets
            // Gets latest created_at per pacel_id in one optimized query
            $historyData = DB::table('pacel_histories')
                ->select('pacel_id', DB::raw('MAX(created_at) as created_at'))
                ->whereIn('pacel_id', $pacelIds)
                ->where('activity', 'kupakia')
                ->groupBy('pacel_id')
                ->get();
            
            // Convert to array, ensuring Carbon instances are preserved
            foreach($historyData as $row){
                if($row->created_at){
                    $pacelHistoryDates[$row->pacel_id] = \Carbon\Carbon::parse($row->created_at);
                }
            }
        }

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);
        
        // Optimize: Get car in one query and check if exists
        $car = Car::find($id);
        $carNumber = $car ? $car->carNumber : 'N/A';

        // PRODUCTION OPTIMIZATION: Set PDF options for better performance
        $pdf = PDF::loadView('management.car_manifest_pdf', array_merge(compact('pacelUnique','pacels', 'carNumber', 'to_list', 'value', 'total_paid', 'total_pacel', 'total_customers', 'printed', 'driver', 'dr_no', 'customerPacels', 'pacelHistoryDates'), $this->getDocumentSettingsVars()))
            ->setPaper($customPaper, 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false, // Disable remote content for faster generation
                'enable-local-file-access' => true,
                'chroot' => base_path() // Allow access to public directory
            ]);
        
        // CACHE OPTIMIZATION: Generate PDF output and cache it for 24 hours
        $pdfOutput = $pdf->output();
        
        // Cache the PDF binary for 24 hours (86400 seconds)
        Cache::put($cacheKey, $pdfOutput, now()->addHours(24));
        
        return $this->streamPdfBinaryInline($pdfOutput, 'car_manifest.pdf');

    }
    
    public function car_manifest22($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(80)
                    ->take(80)
                    ->get(['pacel_id']);
        $value = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');

                                  

        // Optimize: Use distinct() in query instead of get()->unique() to avoid loading all records into memory
        $total_customers_list = (clone $baseQuery)
                                    ->where('activity','kupakia')
                                    ->distinct()
                                    ->pluck('mteja');

        // Keep as models since view expects $data->mzigo_unapokwenda property access
        $to_list = (clone $baseQuery)
                                    ->where('activity','kupakia')
                                    ->select('mzigo_unapokwenda')
                                    ->distinct()
                                    ->get();

        $total_customers = $total_customers_list->count(); 

        $printed = Carbon::now();

        // Optimize: Cache driver object to avoid multiple Driver::find() queries
        $driverIDDD = DriverRoute::where('car_id', $id)->where('start_date', $startDate)->first();

        if($driverIDDD && $driverIDDD->driver_id){
            $driverObj = Driver::find($driverIDDD->driver_id);
            if($driverObj){
                $driver = $driverObj->name;
                $dr_no = $driverObj->phone ?: null;
            } else {
                $driver = 'N/A';
                $dr_no = null;
            }
        } else {
            $driver = 'N/A';
            $dr_no = null;
        }

        

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);
        
        // Optimize: Get car in one query and check if exists
        $car = Car::find($id);
        $carNumber = $car ? $car->carNumber : 'N/A';

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_manifest_pdf22', array_merge(compact('pacelUnique','pacels', 'carNumber', 'to_list', 'value', 'total_paid', 'total_pacel', 'total_customers', 'printed', 'driver', 'dr_no'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf, 'car_manifest22.pdf');

    }
    
    public function car_manifest23($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(160)
                    ->take(80)
                    ->get(['pacel_id']);
        $value = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');


        // Optimize: Use distinct() in query instead of get()->unique() to avoid loading all records into memory
        $total_customers_list = (clone $baseQuery)
                                    ->where('activity','kupakia')
                                    ->distinct()
                                    ->pluck('mteja');

        // Keep as models since view expects $data->mzigo_unapokwenda property access
        $to_list = (clone $baseQuery)
                                    ->where('activity','kupakia')
                                    ->select('mzigo_unapokwenda')
                                    ->distinct()
                                    ->get();

        $total_customers = $total_customers_list->count(); 

        $printed = Carbon::now();

        // Optimize: Cache driver object to avoid multiple Driver::find() queries
        $driverIDDD = DriverRoute::where('car_id', $id)->where('start_date', $startDate)->first();

        if($driverIDDD && $driverIDDD->driver_id){
            $driverObj = Driver::find($driverIDDD->driver_id);
            if($driverObj){
                $driver = $driverObj->name;
                $dr_no = $driverObj->phone ?: null;
            } else {
                $driver = 'N/A';
                $dr_no = null;
            }
        } else {
            $driver = 'N/A';
            $dr_no = null;
        }

      

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);
        
        // Optimize: Get car in one query and check if exists
        $car = Car::find($id);
        $carNumber = $car ? $car->carNumber : 'N/A';

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_manifest_pdf23', array_merge(compact('pacelUnique','pacels', 'carNumber', 'to_list', 'value', 'total_paid', 'total_pacel', 'total_customers', 'printed', 'driver', 'dr_no'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_manifest23.pdf');

    }
    
    public function car_manifest24($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(240)
                    ->take(80)
                    ->get(['pacel_id']);
        $value = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');


        // Optimize: Use distinct() in query instead of get()->unique() to avoid loading all records into memory
        $total_customers_list = (clone $baseQuery)
                                    ->where('activity','kupakia')
                                    ->distinct()
                                    ->pluck('mteja');

        // Keep as models since view expects $data->mzigo_unapokwenda property access
        $to_list = (clone $baseQuery)
                                    ->where('activity','kupakia')
                                    ->select('mzigo_unapokwenda')
                                    ->distinct()
                                    ->get();

        $total_customers = $total_customers_list->count(); 

        $printed = Carbon::now();

        // Optimize: Cache driver object to avoid multiple Driver::find() queries
        $driverIDDD = DriverRoute::where('car_id', $id)->where('start_date', $startDate)->first();

        if($driverIDDD && $driverIDDD->driver_id){
            $driverObj = Driver::find($driverIDDD->driver_id);
            if($driverObj){
                $driver = $driverObj->name;
                $dr_no = $driverObj->phone ?: null;
            } else {
                $driver = 'N/A';
                $dr_no = null;
            }
        } else {
            $driver = 'N/A';
            $dr_no = null;
        }


        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);
        
        // Optimize: Get car in one query and check if exists
        $car = Car::find($id);
        $carNumber = $car ? $car->carNumber : 'N/A';

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_manifest_pdf24', array_merge(compact('pacelUnique','pacels', 'carNumber', 'to_list', 'value', 'total_paid', 'total_pacel', 'total_customers', 'printed', 'driver', 'dr_no'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_manifest24.pdf');

    }
    
    
    public function car_manifestAll($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->get(['pacel_id']);
        $value = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('jumla');
        $total_paid = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('ela_iliyopokelewa');
        $total_pacel = (clone $baseQuery)
                ->where('activity','kupakia')
                ->where('idadi_kupakia', '!=', '0')->sum('idadi_kupakia');


        // Optimize: Use distinct() in query instead of get()->unique() to avoid loading all records into memory
        $total_customers_list = (clone $baseQuery)
                                    ->where('activity','kupakia')
                                    ->distinct()
                                    ->pluck('mteja');

        // Keep as models since view expects $data->mzigo_unapokwenda property access
        $to_list = (clone $baseQuery)
                                    ->where('activity','kupakia')
                                    ->select('mzigo_unapokwenda')
                                    ->distinct()
                                    ->get();

        $total_customers = $total_customers_list->count(); 

        $printed = Carbon::now();

        // Optimize: Cache driver object to avoid multiple Driver::find() queries
        $driverIDDD = DriverRoute::where('car_id', $id)->where('start_date', $startDate)->first();

        if($driverIDDD && $driverIDDD->driver_id){
            $driverObj = Driver::find($driverIDDD->driver_id);
            if($driverObj){
                $driver = $driverObj->name;
                $dr_no = $driverObj->phone ?: null;
            } else {
                $driver = 'N/A';
                $dr_no = null;
            }
        } else {
            $driver = 'N/A';
            $dr_no = null;
        }


        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);
        
        // Optimize: Get car in one query and check if exists
        $car = Car::find($id);
        $carNumber = $car ? $car->carNumber : 'N/A';

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_manifest_pdfAll', array_merge(compact('pacelUnique','pacels', 'carNumber', 'to_list', 'value', 'total_paid', 'total_pacel', 'total_customers', 'printed', 'driver', 'dr_no'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_manifestAll.pdf');

    }

    public function car_invoice($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->take(80)
                    ->get(['pacel_id']);
        
        // Optimize: Get car in one query and check if exists
        $carObj = Car::find($id);
        $car = $carObj ? $carObj->carNumber : 'N/A';

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf', array_merge(compact('pacelUnique','pacels', 'car'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_invoice.pdf');
    }
    
    public function car_invoice22($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(80)
                    ->take(80)
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf22', array_merge(compact('pacelUnique','pacels', 'car'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_invoice.pdf');
    }
    
    public function car_invoice23($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(160)
                    ->take(80)
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf23', array_merge(compact('pacelUnique','pacels', 'car'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_invoice.pdf');
    }
    
    public function car_invoice24($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(240)
                    ->take(80)
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf24', array_merge(compact('pacelUnique','pacels', 'car'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_invoice.pdf');
    }
    
    public function car_invoice25($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(320)
                    ->take(80)
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf25', array_merge(compact('pacelUnique','pacels', 'car'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_invoice.pdf');
    }
    
    public function car_invoice26($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(400)
                    ->take(80)
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf26', array_merge(compact('pacelUnique','pacels', 'car'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_invoice.pdf');
    }
    
    public function car_invoice27($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()
                    ->skip(480)
                    ->take(80)
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdf27', array_merge(compact('pacelUnique','pacels', 'car'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_invoice.pdf');
    }
    
    public function car_invoiceAll($id, $startDate, $endDate, Request $request){
        $destination = $request->input('destination');

        // Build base query
        $baseQuery = PacelHistory::where('car_id', $id)
                                ->where('idadi_kupakia', '!=', '0')
                                ->whereDate('created_at', '>=',$startDate)
                                ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('destination')) {
            $baseQuery->where('mzigo_unapokwenda', $request->destination);
        }

        // Optimize: Select only needed columns to reduce memory usage
        $pacels = (clone $baseQuery)
                                ->select('id', 'pacel_id', 'car_id', 'mteja', 'mpokeaji', 'name', 'idadi_kupakia', 'idadi_shusha', 'bei', 'jumla', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'receipt', 'hashtag', 'delivery', 'created_at', 'activity')
                                ->get();
        $pacelUnique = (clone $baseQuery)
                    ->orderBy('pacel_id', 'DESC')
                    ->distinct()    
                    ->get(['pacel_id']);
        
        $car = Car::find($id)->carNumber;

        //if landscape heigth * width but if portrait widht *height      // dd($dataResult);
        $customPaper = array(0,0,198.425,894.80);

        // dd($dataResult);

        $pdf = PDF::loadView('management.car_invoice_pdfAll', array_merge(compact('pacelUnique','pacels', 'car'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_invoice.pdf');
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

        $pdf = PDF::loadView('management.car_single_invoice_pdf', array_merge(compact('pacelUnique','pacels', 'car'), $this->getDocumentSettingsVars()))->setPaper($customPaper, 'portrait');
        return $this->streamPdfInline($pdf,'car_single_invoice.pdf');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Management\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = CustomerPacel::with('customer')->where('id', $id)->first();
        $htmlData = array_merge(compact('data'), $this->getDocumentSettingsVars(true));
        $customPaper = array(0,0,198.425,894.80);

        return $this->printReceiptOrPdf(
            'management.manifest_pdf',
            $htmlData,
            function () use ($data, $customPaper) {
                return PDF::loadView(
                    'management.manifest_pdf',
                    array_merge(compact('data'), $this->getDocumentSettingsVars())
                )->setPaper($customPaper, 'portrait');
            },
            'manifest.pdf'
        );
    }
    
    public function print_delivery_show($id)
    {
        $data = CustomerPacel::with('customer')->where('id', $id)->first();
        if (!$data) {
            abort(404, 'Mzigo haujapatikana');
        }

        $htmlData = array_merge(compact('data'), $this->deliveryNoteDocumentSettings(true));
        $customPaper = array(0, 0, 198.425, 894.80);

        return $this->printReceiptOrPdf(
            'management.delivery_note_pdf',
            $htmlData,
            function () use ($data, $customPaper) {
                return PDF::loadView(
                    'management.delivery_note_pdf',
                    array_merge(compact('data'), $this->deliveryNoteDocumentSettings(false))
                )->setPaper($customPaper, 'portrait');
            },
            'delivery_note.pdf'
        );
    }

    //delivery_show  

    public function delivery_show($id){

        $result = "kusajiliwa";

        $data = PacelHistory::where('pacel_id', $id)->where('activity', $result)->first();

        $data2 = CustomerPacel::where('id', $id)->first();
        
        
        $timecorrect = Customer::where('id', $data2->customer_id)->first();

        $result22 = "kupakia";


        $data3 = PacelHistory::where('pacel_id', $id)->where('activity', $result22)->get();




        return view('management.delivery_show', compact('data', 'data2', 'data3', 'timecorrect'));
    }

//customer_show  
    public function customer_show($id){

        $result = "kusajiliwa";

        $data = PacelHistory::where('id', $id)->where('activity', $result)->first();

        $data2 = CustomerPacel::where('id', $data->pacel_id)->first();
        
        $timecorrect = Customer::where('id', $data2->customer_id)->first();

        $result22 = "kupakia";


        $data3 = PacelHistory::where('pacel_id', $id)->where('activity', $result22)->get();




        return view('management.customer_show', compact('data', 'data2', 'data3', 'timecorrect'));
    }

    //pacel_show

    public function pacel_show($id){

        $result = "kusajiliwa";

        $data = PacelHistory::where('pacel_id', $id)->where('activity', $result)->first();

        $data2 = CustomerPacel::where('id', $id)->first();
        
        $timecorrect = Customer::where('id', $data2->customer_id)->first();

        $result22 = "kupakia";


        $data3 = PacelHistory::where('pacel_id', $id)->where('activity', $result22)->get();




        return view('management.pacel_show', compact('data', 'data2', 'data3', 'timecorrect'));
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

    /**
     * Document settings for PDF views (branch, contact, tunasafirisha text).
     * Used by all print/manifest/invoice PDFs so they respect /document_settings.
     */
    private function getDocumentSettingsVars($forBrowser = false)
    {
        $companyId = auth()->user()->company_id ?? null;
        $doc = DocumentSetting::forCompany($companyId);

        $logoUrl = $forBrowser
            ? asset('assets/images/app-logo.jpeg')
            : public_path('assets/images/app-logo.jpeg');
        $systemLogoTitle = null;
        if ($doc && $doc->logo_type === 'system' && $companyId) {
            $company = \App\Models\Company::find($companyId);
            if ($company && $company->logo) {
                $logoUrl = $forBrowser
                    ? asset('storage/' . $company->logo)
                    : storage_path('app/public/' . $company->logo);
            }
            $systemLogoTitle = $doc->system_logo_title;
        }

        return [
            'logoUrl'           => $logoUrl,
            'systemLogoTitle'   => $systemLogoTitle,
            'tinNumber'         => $doc ? $doc->tin_number : null,
            'docHeaderLine1'    => $doc ? $doc->header_line1 : null,
            'docHeaderLine2'    => $doc ? $doc->header_line2 : null,
            'docOffice1Label'     => $doc ? $doc->office_1_label : null,
            'docOffice1Place'     => $doc ? $doc->office_1_place : null,
            'docOffice1Contact'   => $doc ? $doc->office_1_contact : null,
            'docOffice1Contact2'  => $doc ? $doc->office_1_contact_2 : null,
            'docOffice1Contact3'  => $doc ? $doc->office_1_contact_3 : null,
            'docOffice2Label'   => $doc ? $doc->office_2_label : null,
            'docOffice2Place'   => $doc ? $doc->office_2_place : null,
            'docOffice2Contact' => $doc ? $doc->office_2_contact : null,
            'docOffice3Label'   => $doc ? $doc->office_3_label : null,
            'docOffice3Place'   => $doc ? $doc->office_3_place : null,
            'docOffice3Contact' => $doc ? $doc->office_3_contact : null,
            'kanuni1'           => $doc ? $doc->kanuni_1 : 'MTEJA HAKIKISHA UMEFUNGA MZIGO WAKO IMARA',
            'kanuni2'           => $doc ? $doc->kanuni_2 : 'MTEJA MALI YOYOTE ITAKAYO HARIBIKA NJIANI HATUTOKUWA NA DHAMANA NAYO KUTOKANA NA HALI YA BARABARA NI MUHIMU KUKATIA MZIGO WAKO BIMA KWA KUWA MSAFIRISHAJI HATOHUSIKA NA BIMA YA MZIGO WAKO',
            'kanuni3'           => $doc ? $doc->kanuni_3 : 'MZIGO WA KUVUNJIKA KAMA VILE VIGAE,VIOO,TV N.K HATUTAHUSIKA NA UHARIBIFU WOWOTE UTAKAO JITOKEZA',
            'kanuni4'           => $doc ? $doc->kanuni_4 : 'HATUPOKEI MALALAMIKO YOYOTE KUHUSU MZIGO BAADA YA SIKU 20 TANGU KUPOKEA',
        ];
    }

    /**
     * Delivery Note header: line 1 generic / settings; line 2 = company mikoa list.
     */
    private function deliveryNoteDocumentSettings($forBrowser = false): array
    {
        $vars = $this->getDocumentSettingsVars($forBrowser);
        $companyId = auth()->user()->company_id ?? null;

        $mikoaNames = \App\Models\Mikoa::query()
            ->when($companyId, function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->orderBy('regional_name')
            ->pluck('regional_name')
            ->filter(function ($name) {
                return trim((string) $name) !== '';
            })
            ->values();

        $vars['docHeaderLine1'] = !empty($vars['docHeaderLine1'])
            ? $vars['docHeaderLine1']
            : 'TUNASAFRISHA MIZIGO KWENDA MIKOA MBALI MBALI';

        if ($mikoaNames->isNotEmpty()) {
            $vars['docHeaderLine2'] = $mikoaNames->implode(', ');
        } elseif (empty($vars['docHeaderLine2'])) {
            $vars['docHeaderLine2'] = 'MIKOA MBALI MBALI';
        }

        return $vars;
    }
}
