<?php
namespace App\Http\Controllers\Api_controllers;

use App\Http\Controllers\Controller;
use App\Models\CarHistory;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerPacel;
use App\Models\DriverRoute;
use App\Models\DropOffPoint;
use App\Models\Management\Car;
use App\Models\Management\Driver;
use App\Models\PacelHistory;
use App\Models\PackageLoad;
use App\Models\ParcelCarAssignment;
use App\Models\User;
use App\Models\Mikoa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UserCarSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\SmsService;
use App\Services\CargoRouteService;
use App\Support\ParcelStatusLabel;
use App\Support\ParcelDropPayment;
use App\Support\ParcelQuantitySummary;

class ManagementController extends Controller
{
    protected $smsService;
    protected $cargoRouteService;

    public function __construct(SmsService $smsService, CargoRouteService $cargoRouteService)
    {
        $this->smsService = $smsService;
        $this->cargoRouteService = $cargoRouteService;
    }

    /**
     * Get company_id from user_id (for API requests)
     * If user_id is provided in request, get company_id from that user
     * Otherwise, try to get from authenticated user
     */
    protected function getCompanyId($request)
    {
        // Try to get company_id from user_id in request
        if ($request->has('user_id') && $request->user_id) {
            $user = User::find($request->user_id);
            if ($user && $user->company_id) {
                return $user->company_id;
            }
        }
        
        // Try to get from authenticated user
        if (auth()->check() && auth()->user()->company_id) {
            return auth()->user()->company_id;
        }
        
        // Fallback: return null (will need to handle this case)
        return null;
    }

    /**
     * Compare parcel destination (mzigo_unapokwenda) with route destination (driver_routes.to).
     */
    protected function normalizePackingDestinationLabel(?string $value): string
    {
        return $this->cargoRouteService->normalizeDestinationLabel($value);
    }

    /**
     * Mobile clients often send 0 instead of omitting optional ids; treat as null so
     * required_without / car_id path works.
     */
    protected function normalizePlaceholderZeroIds(Request $request): void
    {
        foreach (['drop_off_point_id', 'car_id'] as $key) {
            if (!$request->has($key)) {
                continue;
            }
            $v = $request->input($key);
            if ($v === '' || $v === null || (is_numeric($v) && (int) $v === 0)) {
                $request->merge([$key => null]);
            }
        }
    }

    /**
     * True when request has a positive integer id (excludes 0, '', null — Flutter often sends 0 as "none").
     */
    protected function hasPositiveIntRequestId(Request $request, string $key): bool
    {
        $v = $request->input($key);
        if ($v === null || $v === '') {
            return false;
        }
        if (!is_numeric($v)) {
            return false;
        }

        return (int) $v > 0;
    }

    /**
     * When there is no drop-off and no car_id, derive car_id from driver_route_id (same trip context).
     */
    protected function inferCarIdFromDriverRouteWhenMissing(Request $request): void
    {
        if ($this->hasPositiveIntRequestId($request, 'drop_off_point_id')) {
            return;
        }
        if ($this->hasPositiveIntRequestId($request, 'car_id')) {
            return;
        }
        if (!$this->hasPositiveIntRequestId($request, 'driver_route_id')) {
            return;
        }
        $route = DriverRoute::find((int) $request->driver_route_id);
        if ($route) {
            $request->merge(['car_id' => $route->car_id]);
        }
    }

    protected function isDriverRouteClosed($route): bool
    {
        if (!$route) {
            return false;
        }
        $closeDate = $route->closeDate ?? null;

        return !empty($closeDate) && $closeDate !== '0000-00-00';
    }

    protected function isCarClosed(int $carId, $companyId = null): bool
    {
        $query = Car::where('id', $carId);
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        $car = $query->first();

        return $car && (string) $car->status === '3';
    }

    /**
     * Resolve car status string (0=in transit, 1=arrived, 2=packing, 3=closed).
     */
    protected function getCarStatus(int $carId, $companyId = null): ?string
    {
        $query = Car::where('id', $carId);
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        $car = $query->first();

        return $car ? (string) $car->status : null;
    }

    /**
     * Trip has started (Anza safari) or arrived — drop-off allowed.
     * Status 2 = still packing; drop-off not allowed yet.
     */
    protected function isCarTripStarted(int $carId, $companyId = null): bool
    {
        $status = $this->getCarStatus($carId, $companyId);
        if ($status === null) {
            return false;
        }

        return in_array($status, ['0', '1'], true);
    }

    /**
     * Block drop-off until Anza safari (car status 0 or 1).
     */
    protected function rejectIfTripNotStarted(int $carId, $companyId = null)
    {
        if ($this->isCarTripStarted($carId, $companyId)) {
            return null;
        }

        return response()->json([
            'success' => false,
            'error' => true,
            'trip_not_started' => true,
            'message' => 'Anza safari kwanza kabla ya kushusha mizigo',
        ], 200);
    }

    /**
     * Whether a drop-off stop still has parcels on the car to unload there.
     */
    protected function dropPointHasRemainingParcels(DropOffPoint $drop, int $carId, $companyId = null): bool
    {
        $this->cargoRouteService->backfillDropPointMikoaId($drop, $companyId);
        $drop->refresh();

        $pacelsQuery = CustomerPacel::with(['destinations.dropMikoa'])
            ->where('car_id', $carId)
            ->where(function ($q) {
                $q->where('idadi_kupakia', '>', 0)
                    ->orWhereHas('destinations', function ($d) {
                        $d->where('idadi_kupakia', '>', 0);
                    });
            });
        if ($companyId) {
            $pacelsQuery->where('company_id', $companyId);
        }
        $pacels = $pacelsQuery->get();
        if ($pacels->isEmpty()) {
            return false;
        }

        $rows = $this->cargoRouteService->expandParcelsForDropList(
            $pacels,
            $drop->drop_off_point,
            $drop->mikoa_id ? (int) $drop->mikoa_id : null,
            false
        );

        return !empty($rows);
    }

    /**
     * Mark intermediate stop completed only when no more parcels remain for that stop.
     * Partial drops must leave the stop open so other parcels can still be unloaded.
     */
    protected function syncDropPointCompletionStatus(DropOffPoint $drop, int $carId, $companyId = null): void
    {
        $hasRemaining = $this->dropPointHasRemainingParcels($drop, $carId, $companyId);
        DropOffPoint::where('id', $drop->id)->update([
            'status' => $hasRemaining ? 0 : 1,
        ]);
    }

    /**
     * Intermediate drop-off points for a driver route that still have parcels to unload.
     *
     * @return \Illuminate\Support\Collection<int, DropOffPoint>
     */
    protected function pendingIntermediateDropStops(int $driverRouteId, int $carId, $companyId = null)
    {
        $stopsQuery = DropOffPoint::with('mikoa')->where('driver_route_id', $driverRouteId);
        if ($companyId) {
            $stopsQuery->where('company_id', $companyId);
        }
        $stops = $stopsQuery->orderBy('stop_order')->orderBy('id')->get();
        if ($stops->isEmpty()) {
            return collect();
        }

        return $stops->filter(function ($stop) use ($carId, $companyId) {
            return $this->dropPointHasRemainingParcels($stop, $carId, $companyId);
        })->values();
    }

    /**
     * Block final-destination drop until all intermediate stops with cargo are cleared.
     * If there are no intermediate stops (or none with remaining cargo), allow.
     */
    protected function rejectIfFinalBeforeIntermediateStops(
        int $driverRouteId,
        int $carId,
        $companyId = null
    ) {
        if (!$this->cargoRouteService->allowsEnrouteDrops()) {
            return null;
        }

        $pending = $this->pendingIntermediateDropStops($driverRouteId, $carId, $companyId);
        if ($pending->isEmpty()) {
            return null;
        }

        $names = $pending->map(function ($stop) {
            if ($stop->mikoa && !empty($stop->mikoa->regional_name)) {
                return $stop->mikoa->regional_name;
            }

            return (string) $stop->drop_off_point;
        })->filter()->unique()->values()->all();

        $list = !empty($names) ? implode(', ', $names) : 'vituo vya njiani';

        return response()->json([
            'success' => false,
            'error' => true,
            'intermediate_pending' => true,
            'pending_stops' => $names,
            'message' => 'Shusha vituo vyote vya njiani kwanza kabla ya kushusha mwisho: ' . $list,
        ], 200);
    }

    /**
     * Block drop-off and payment APIs when the car or driver route is closed.
     */
    protected function rejectIfTripClosed(int $carId, ?int $driverRouteId = null, $companyId = null)
    {
        if ($this->isCarClosed($carId, $companyId)) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Gari imefungwa — huwezi kushusha wala kupokea malipo',
            ], 200);
        }

        if ($driverRouteId) {
            $route = DriverRoute::find($driverRouteId);
            if ($this->isDriverRouteClosed($route)) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'Safari imefungwa — huwezi kushusha wala kupokea malipo',
                ], 200);
            }
        }

        return null;
    }

    /**
     * For drop_parcel / update_parcel_payment: resolve car and driver from drop_off_point_id and/or car_id.
     * Requires driver_route_id on the request; route car must match drop-off car or requested car_id.
     *
     * @return array Keys: ok (bool), response (JsonResponse, when ok false), drop (?DropOffPoint),
     *               car_id, driver_id, receipt_label (string), drop_off_point_id_db (?int)
     */
    protected function resolveDropParcelRouteContext(Request $request, $companyId): array
    {
        $driverRouteId = (int) $request->driver_route_id;
        $route = DriverRoute::find($driverRouteId);
        if (!$route) {
            return [
                'ok' => false,
                'response' => response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'Driver route not found',
                ], 200),
            ];
        }

        if ($this->hasPositiveIntRequestId($request, 'drop_off_point_id')) {
            $drop = DropOffPoint::find((int) $request->drop_off_point_id);
            if (!$drop) {
                return [
                    'ok' => false,
                    'response' => response()->json([
                        'success' => false,
                        'error' => true,
                        'message' => 'Drop off point not found',
                    ], 200),
                ];
            }
            if ($companyId && $drop->company_id && (int) $drop->company_id !== (int) $companyId) {
                return [
                    'ok' => false,
                    'response' => response()->json([
                        'success' => false,
                        'error' => true,
                        'message' => 'You are not allowed to use this drop off point',
                    ], 200),
                ];
            }
            if ((int) $route->car_id !== (int) $drop->car_id) {
                return [
                    'ok' => false,
                    'response' => response()->json([
                        'success' => false,
                        'error' => true,
                        'message' => 'Driver route does not match this drop-off car',
                    ], 200),
                ];
            }

            return [
                'ok' => true,
                'drop' => $drop,
                'car_id' => $drop->car_id,
                'driver_id' => $drop->driver_id,
                'receipt_label' => (string) $drop->drop_off_point,
                'drop_off_point_id_db' => (int) $drop->id,
            ];
        }

        $requestedCarId = (int) $request->car_id;
        $carQuery = Car::query()->where('id', $requestedCarId);
        if ($companyId) {
            $carQuery->where('company_id', $companyId);
        }
        $car = $carQuery->first();
        if (!$car) {
            return [
                'ok' => false,
                'response' => response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => $companyId
                        ? 'Car not found or you are not allowed to use this car'
                        : 'Car not found',
                ], 200),
            ];
        }
        if ((int) $route->car_id !== $requestedCarId) {
            return [
                'ok' => false,
                'response' => response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'Car does not match the selected driver route',
                ], 200),
            ];
        }

        $destinationParam = $request->input('destination');
        $receiptLabel = !empty($destinationParam)
            ? (string) $destinationParam
            : (string) ($route->to ?? 'Destination');

        return [
            'ok' => true,
            'drop' => null,
            'car_id' => $route->car_id,
            'driver_id' => $route->driver_id,
            'receipt_label' => $receiptLabel,
            'drop_off_point_id_db' => null,
        ];
    }

    /**
     * Fetch mikoa (regions) by company_id.
     * GET /api/mikoa/index?company_id=1
     * Or with user_id: GET /api/mikoa/index?user_id=5
     */
    public function mikoa(Request $request)
    {
        $companyId = $this->getCompanyId($request);

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'error'   => true,
                'message' => 'company_id or user_id required',
                'mikoa'   => [],
            ], 400);
        }

        $mikoa = Mikoa::where('company_id', $companyId)
            ->orderBy('regional_name')
            ->get(['id', 'company_id', 'regional_name']);

        return response()->json([
            'success' => true,
            'error'   => false,
            'message' => 'successfully',
            'mikoa'   => $mikoa,
        ]);
    }
    
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //homepage
    public function pacel(String $date, Request $request)
    {
        //
        $companyId = $this->getCompanyId($request);
        $query = CustomerPacel::whereDate('created_at', $date);
        
        // Filter by company_id if available
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        // Most recent first
        $pacels = $query->orderBy('created_at', 'DESC')->get();
        // $mizigo = $pacels->count();

       
        
        if($pacels->isEmpty()){

            $data['mizigo'] = 0;

            $data['jumla_gari'] = 0;

            $data['mizigo_gari'] = 0;

            $farmers = $data;

            $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$farmers];

            return response()->json($response,200);
                
        }
        else{

                $mizigoQuery = CustomerPacel::whereDate('created_at', $date)->where('idadi_stoo', '!=', '0');
                if ($companyId) {
                    $mizigoQuery->where('company_id', $companyId);
                }
                $data['mizigo'] = $mizigoQuery->count();

                $jumlaGariQuery = Car::whereDate('created_at', $date);
                if ($companyId) {
                    $jumlaGariQuery->where('company_id', $companyId);
                }
                $jumla_gari = $jumlaGariQuery->get();

                if($jumla_gari->isEmpty()){

                    $data['jumla_gari'] = 0;

                }
                else{

                    $jumlaGariCountQuery = Car::whereDate('created_at', $date);
                    if ($companyId) {
                        $jumlaGariCountQuery->where('company_id', $companyId);
                    }
                    $data['jumla_gari'] = $jumlaGariCountQuery->count();

                }

                $mizigoGariQuery = Car::where('status', 2)->whereDate('created_at', $date);
                if ($companyId) {
                    $mizigoGariQuery->where('company_id', $companyId);
                }
                $mizigo_gari = $mizigoGariQuery->get();

                if($mizigo_gari->isEmpty()){

                    $data['mizigo_gari'] = 0;
                    
                }
                else{
                    $mizigoGariCountQuery = Car::where('status', 2)->whereDate('created_at', $date);
                    if ($companyId) {
                        $mizigoGariCountQuery->where('company_id', $companyId);
                    }
                    $data['mizigo_gari'] = $mizigoGariCountQuery->count();


                }


                $farmers = $data;
     
           

            $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$farmers];
            return response()->json($response,200);
        } 
    }
    
    public function delete_car_route(String $date){

        $mizigo = Car::whereDate('created_at', '<=', $date)->get();

        // dd($mizigo);

        if($mizigo->isNotEmpty()){


            foreach($mizigo as $row){

            $data = $row->delete();


            }

            // foreach($mizigo as $row){

            // }

            $response=['success'=>true,'error'=>false,'message'=>'Deleted successfully'];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No Cars found'];
            return response()->json($response,200);
        }
    }
    
    public function delete_driver_route(String $date){

        $mizigo = DriverRoute::whereDate('created_at', '<=', $date)->get();

        // dd($mizigo);

        if($mizigo->isNotEmpty()){


            foreach($mizigo as $row){

            $data = $row->delete();


            }

            // foreach($mizigo as $row){

            // }

            $response=['success'=>true,'error'=>false,'message'=>'Deleted successfully'];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No Cars found'];
            return response()->json($response,200);
        }
    }

     public function delete_mizigo(String $date){

        $mizigo = CustomerPacel::whereDate('created_at', '<=', $date)->get();

        // dd($mizigo);

        if($mizigo->isNotEmpty()){


            foreach($mizigo as $row){

            $data = $row->delete();


            }

            // foreach($mizigo as $row){

            // }

            $response=['success'=>true,'error'=>false,'message'=>'Deleted successfully'];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No Cars found'];
            return response()->json($response,200);
        }
    }

    public function delete_mizigo_history(String $date){

        $mizigo = PacelHistory::whereDate('created_at', '<=', $date)->get();

        if($mizigo->isNotEmpty()){

            foreach($mizigo as $row){
                $data = $row->delete();
            }

            $response=['success'=>true,'error'=>false,'message'=>'Deleted successfully'];
            return response()->json($response,200);
        }
        else{

            $response=['success'=>false,'error'=>true,'message'=>'No Cars found'];
            return response()->json($response,200);
        }
    }

    /**
     * Get list of cars for drop off point selection
     */
    public function drop_off_point_cars(Request $request)
    {
        $companyId = $this->getCompanyId($request);

        $query = Car::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $cars = $query->get();

        if ($cars->isEmpty()) {
            $response = ['success' => false, 'error' => true, 'message' => 'No Cars found'];
            return response()->json($response, 200);
        }

        $data = [];
        foreach ($cars as $row) {
            $item['id'] = $row->id;
            $item['carNumber'] = $row->carNumber;
            // For drop off point selection we only need id, carNumber, driver_id
            // Do NOT include status here to avoid type mismatches with the mobile model
            $item['driver_id'] = $row->driver_id;
            $data[] = $item;
        }

        $response = ['success' => true, 'error' => false, 'message' => 'successfully', 'cars' => $data];
        return response()->json($response, 200);
    }

    /**
     * Store multiple drop off points for a car and driver.
     * Route information (source/destination) is NOT stored on drop_off_points,
     * but can be retrieved later via driver_routes.
     * We still require a valid route so we can link via driver_route_id.
     */
    public function drop_off_point_store(Request $request)
    {
        if (!$this->cargoRouteService->allowsEnrouteDrops()) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Madekenya haishushi mizigo njiani. Kushusha kunaruhusiwa mwisho wa safari tu.',
            ], 200);
        }

        $request->validate([
            'car_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'driver_route_id' => 'nullable|integer',
            'points' => 'required|array|min:1',
            'points.*.drop_off_point' => 'required|string',
        ]);

        $companyId = $this->getCompanyId($request);

        // Build base query for driver route
        $driverRouteQuery = DriverRoute::where('car_id', $request->car_id);

        // Filter by company_id if available
        if ($companyId) {
            $driverRouteQuery->where('company_id', $companyId);
        }

        // Use specific route if provided
        if ($request->driver_route_id) {
            $driverRouteQuery->where('id', $request->driver_route_id);
        } else {
            // Otherwise try to narrow by driver_id if it is a positive value
            if (!empty($request->driver_id) && (int)$request->driver_id > 0) {
                $driverRouteQuery->where('driver_id', $request->driver_id);
            }
        }

        // Use the latest matching route (we'll read from/to later when fetching)
        $driverRoute = $driverRouteQuery
            ->orderBy('id', 'DESC')
            ->first();

        if (!$driverRoute) {
            // No route with destination found for this company + car (+ optional driver)
            $response = [
                'success' => false,
                'error' => true,
                'message' => 'No route destination (column \"to\") found for this car/driver in your company. Set route first before saving drop off points.',
            ];
            return response()->json($response, 200);
        }

        $destination = $driverRoute->to;

        $created = [];
        foreach ($request->points as $index => $point) {
            $mikoaId = isset($point['mikoa_id']) ? (int) $point['mikoa_id'] : null;
            if (!$mikoaId && !empty($point['drop_off_point'])) {
                $mikoaId = $this->cargoRouteService->resolveMikoaIdForLabel(
                    $point['drop_off_point'],
                    $companyId
                );
            }
            $drop = DropOffPoint::create([
                'car_id' => $request->car_id,
                'driver_id' => $request->driver_id,
                'driver_route_id' => $driverRoute->id,
                'drop_off_point' => $point['drop_off_point'],
                'mikoa_id' => $mikoaId ?: null,
                'stop_order' => isset($point['stop_order']) ? (int) $point['stop_order'] : ($index + 1),
                'user_id' => $request->user_id ?? (auth()->check() ? auth()->id() : null),
                'company_id' => $companyId,
            ]);

            $created[] = [
                'id' => $drop->id,
                'car_id' => $drop->car_id,
                'driver_id' => $drop->driver_id,
                'driver_route_id' => $drop->driver_route_id,
                'drop_off_point' => $drop->drop_off_point,
                'mikoa_id' => $drop->mikoa_id,
                'stop_order' => $drop->stop_order,
            ];
        }

        $response = [
            'success' => true,
            'error' => false,
            'message' => 'Drop off points saved successfully',
            'drop_off_points' => $created,
        ];

        return response()->json($response, 200);
    }

    /**
     * Get drop off routes (driver_routes) with their drop off points for a given company.
     * Filters by:
     * - company_id (from user_id/auth)
     * - optional car_id
     * - optional driver_id
     *
     * For each driver route, also fetches:
     * - associated drop_off_points by driver_route_id
     * - route open/close status based on closeDate (null or '0000-00-00' => open, otherwise closed)
     * - created_at on each route (driver_routes) and each drop_off_point (Africa/Nairobi)
     */
    public function drop_off_point_list(Request $request)
    {
        $companyId = $this->getCompanyId($request);

        if (!$companyId) {
            $payload = [
                'success' => false,
                'error' => true,
                'message' => 'Company not found for this user. Please provide a valid user_id.',
            ];
            Log::debug('drop_off_point_list response', $payload);

            return response()->json($payload, 200);
        }

        // Start from driver_routes, join cars to filter by company_id,
        // and join drivers for driver name.
        // Fetch all routes (open and closed) for this company from driver_routes
        $query = DriverRoute::query()
            ->join('cars', 'cars.id', '=', 'driver_routes.car_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'driver_routes.driver_id')
            ->select(
                'driver_routes.id',
                'driver_routes.car_id',
                'driver_routes.driver_id',
                'driver_routes.from',
                'driver_routes.to',
                'driver_routes.status',
                'driver_routes.start_date',
                'driver_routes.closeDate',
                'driver_routes.created_at as route_created_at',
                'cars.company_id',
                'cars.carNumber as car_name',
                'cars.status as car_status',
                'drivers.name as driver_name'
            )
            ->where('cars.company_id', $companyId);

        // Optional filter by open/closed routes based on closeDate
        // filter=open or filteropen  -> closeDate null or '0000-00-00'  (open)
        // filter=close or filterclose -> closeDate NOT null and NOT '0000-00-00' (closed)
        $filter = strtolower((string) $request->query('filter', ''));
        if ($filter === 'open' || $filter === 'filteropen') {
            $query->where(function ($q) {
                $q->whereNull('driver_routes.closeDate')
                  ->orWhere('driver_routes.closeDate', '0000-00-00');
            });
        } elseif ($filter === 'close' || $filter === 'filterclose') {
            $query->whereNotNull('driver_routes.closeDate')
                  ->where('driver_routes.closeDate', '<>', '0000-00-00');
        }

        $routes = $query
            ->orderByRaw('COALESCE(driver_routes.start_date, DATE(driver_routes.created_at)) DESC')
            ->orderBy('driver_routes.created_at', 'DESC')
            ->get();

        if ($routes->isEmpty()) {
            $payload = [
                'success' => false,
                'error' => true,
                'message' => 'No routes found for this car/driver',
            ];
            Log::debug('drop_off_point_list response', array_merge($payload, [
                'company_id' => $companyId,
                'filter' => $filter,
            ]));

            return response()->json($payload, 200);
        }

        $data = [];
        foreach ($routes as $route) {
            // Determine if route is open or closed based on closeDate
            $closeDate = $route->closeDate;
            $isClosed = !empty($closeDate) && $closeDate !== '0000-00-00';
            $routeStatus = $isClosed ? 'closed' : 'open';

            $driverRouteModel = DriverRoute::find($route->id);
            if ($driverRouteModel && !$isClosed) {
                $this->cargoRouteService->syncDriverRouteDropPointsFromCargoStops(
                    $driverRouteModel,
                    $companyId
                );
            }

            $dropPoints = collect();
            if ($this->cargoRouteService->allowsEnrouteDrops()) {
                $dropPoints = DropOffPoint::with('mikoa')
                    ->where('driver_route_id', $route->id)
                    ->where('company_id', $companyId)
                    ->orderBy('stop_order')
                    ->orderBy('id')
                    ->get();
            }

            foreach ($dropPoints as $dp) {
                $this->cargoRouteService->backfillDropPointMikoaId($dp, $companyId);
            }

            $cargoStops = $driverRouteModel
                ? $this->cargoRouteService->cargoStopsForDriverRoute($driverRouteModel, $companyId)
                : [];

            $carId = (int) $route->car_id;
            $carStatus = (string) ($route->car_status ?? '');
            $tripStarted = in_array($carStatus, ['0', '1'], true);
            $pendingIntermediate = collect();
            if ($this->cargoRouteService->allowsEnrouteDrops() && $tripStarted && !$isClosed) {
                $pendingIntermediate = $this->pendingIntermediateDropStops((int) $route->id, $carId, $companyId);
            }
            $canDropFinal = $tripStarted && !$isClosed && $pendingIntermediate->isEmpty();

            $startDate = null;
            if (!empty($route->start_date) && $route->start_date !== '0000-00-00') {
                try {
                    $startDate = Carbon::parse($route->start_date)->toDateString();
                } catch (\Throwable $e) {
                    $startDate = (string) $route->start_date;
                }
            } elseif ($route->route_created_at) {
                try {
                    $startDate = Carbon::parse($route->route_created_at)
                        ->setTimezone('Africa/Nairobi')
                        ->toDateString();
                } catch (\Throwable $e) {
                    $startDate = null;
                }
            }

            $data[] = [
                'driver_route_id' => $route->id,
                'car_id' => $carId,
                'car_name' => $route->car_name,
                'company_id' => $route->company_id,
                'driver_id' => $route->driver_id,
                'driver_name' => $route->driver_name,
                'source' => $route->from,
                'destination' => $route->to,
                'start_date' => $startDate,
                'close_date' => $closeDate,
                'route_status' => $routeStatus, // 'open' or 'closed' based on closeDate
                'car_status' => $carStatus,
                'trip_started' => $tripStarted,
                'can_drop_final' => $canDropFinal,
                'pending_intermediate_count' => $pendingIntermediate->count(),
                'created_at' => $route->route_created_at
                    ? Carbon::parse($route->route_created_at)->setTimezone('Africa/Nairobi')->toDateTimeString()
                    : null,
                'cargo_route_stops' => $cargoStops,
                'drop_off_points' => $dropPoints->map(function ($dp) use ($carId, $companyId, $isClosed) {
                    $hasRemaining = false;
                    if (!$isClosed) {
                        $hasRemaining = $this->dropPointHasRemainingParcels($dp, $carId, $companyId);
                        $this->syncDropPointCompletionStatus($dp, $carId, $companyId);
                        $dp->refresh();
                    }

                    return [
                        'id' => $dp->id,
                        'drop_off_point' => $dp->drop_off_point,
                        'mikoa_id' => $dp->mikoa_id,
                        'mikoa_name' => $dp->mikoa ? $dp->mikoa->regional_name : null,
                        'stop_order' => (int) ($dp->stop_order ?? 0),
                        'car_id' => (int) $dp->car_id,
                        'driver_id' => $dp->driver_id,
                        'status' => (int) ($dp->status ?? 0),
                        'has_remaining' => $hasRemaining,
                        'created_at' => $dp->created_at
                            ? Carbon::parse($dp->created_at)->setTimezone('Africa/Nairobi')->toDateTimeString()
                            : null,
                    ];
                })->values()->all(),
            ];
        }

        $payload = [
            'success' => true,
            'error' => false,
            'message' => 'successfully',
            'routes' => $data,
        ];
        Log::debug('drop_off_point_list response', array_merge($payload, [
            'company_id' => $companyId,
            'filter' => $filter,
            'routes_count' => count($data),
        ]));

        return response()->json($payload, 200);
    }

    /**
     * List parcels that can be offloaded (kushusha) for a car.
     * Pass either drop_off_point_id (resolves car from drop_off_points) or car_id (direct, e.g. routes with no stops).
     * Uses customer_pacels on this car with idadi_kupakia > 0 (quantity still loaded).
     */
    public function drop_off_point_parcels(Request $request)
    {
        $this->normalizePlaceholderZeroIds($request);
        if (!$this->hasPositiveIntRequestId($request, 'drop_off_point_id')
            && !$this->hasPositiveIntRequestId($request, 'car_id')
            && $this->hasPositiveIntRequestId($request, 'driver_route_id')) {
            $route = DriverRoute::find((int) $request->driver_route_id);
            if ($route) {
                $request->merge(['car_id' => $route->car_id]);
            }
        }

        $request->validate([
            'drop_off_point_id' => 'nullable|integer|required_without:car_id',
            'car_id' => 'nullable|integer|required_without:drop_off_point_id',
        ]);

        $companyId = $this->getCompanyId($request);
        $dropOffPointId = $this->hasPositiveIntRequestId($request, 'drop_off_point_id')
            ? (int) $request->drop_off_point_id
            : null;
        $requestedCarId = $this->hasPositiveIntRequestId($request, 'car_id')
            ? (int) $request->car_id
            : null;

        if ($dropOffPointId !== null && !$this->cargoRouteService->allowsEnrouteDrops()) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Madekenya haishushi mizigo njiani. Kushusha kunaruhusiwa mwisho wa safari tu.',
            ], 200);
        }

        Log::debug('drop_off_point_parcels request', [
            'drop_off_point_id' => $dropOffPointId,
            'car_id' => $requestedCarId,
            'company_id' => $companyId,
            'query' => $request->query(),
            'body' => $request->except(['password', 'password_confirmation', 'token']),
        ]);

        $carId = null;

        if ($dropOffPointId !== null) {
            $drop = DropOffPoint::find($dropOffPointId);
            if (!$drop) {
                $payload = [
                    'success' => false,
                    'error' => true,
                    'message' => 'Drop off point not found',
                ];
                Log::debug('drop_off_point_parcels response', array_merge($payload, [
                    'drop_off_point_id' => $dropOffPointId,
                    'company_id' => $companyId,
                ]));

                return response()->json($payload, 200);
            }

            if ($companyId && $drop->company_id && (int) $drop->company_id !== (int) $companyId) {
                $payload = [
                    'success' => false,
                    'error' => true,
                    'message' => 'You are not allowed to view parcels for this drop off point',
                ];
                Log::debug('drop_off_point_parcels response', array_merge($payload, [
                    'drop_off_point_id' => $dropOffPointId,
                    'company_id' => $companyId,
                    'drop_off_point_company_id' => $drop->company_id,
                ]));

                return response()->json($payload, 200);
            }

            $carId = $drop->car_id;

            // Re-open stop if it was marked complete too early but parcels remain
            if ((int) ($drop->status ?? 0) === 1) {
                if ($this->dropPointHasRemainingParcels($drop, (int) $carId, $companyId)) {
                    DropOffPoint::where('id', $drop->id)->update(['status' => 0]);
                    $drop->status = 0;
                } else {
                    $payload = [
                        'success' => false,
                        'error' => true,
                        'stop_completed' => true,
                        'message' => 'Kituo hiki tayari kimeshushwa — chagua kituo kingine',
                    ];
                    Log::debug('drop_off_point_parcels response', array_merge($payload, [
                        'drop_off_point_id' => $dropOffPointId,
                        'company_id' => $companyId,
                    ]));

                    return response()->json($payload, 200);
                }
            }
        } else {
            $carQuery = Car::query()->where('id', $requestedCarId);
            if ($companyId) {
                $carQuery->where('company_id', $companyId);
            }
            $car = $carQuery->first();
            if (!$car) {
                $payload = [
                    'success' => false,
                    'error' => true,
                    'message' => $companyId
                        ? 'Car not found or you are not allowed to view parcels for this car'
                        : 'Car not found',
                ];
                Log::debug('drop_off_point_parcels response', array_merge($payload, [
                    'car_id' => $requestedCarId,
                    'company_id' => $companyId,
                ]));

                return response()->json($payload, 200);
            }

            $carId = $car->id;
        }

        $closedResponse = $this->rejectIfTripClosed((int) $carId, null, $companyId);
        if ($closedResponse) {
            return $closedResponse;
        }

        $notStartedResponse = $this->rejectIfTripNotStarted((int) $carId, $companyId);
        if ($notStartedResponse) {
            return $notStartedResponse;
        }

        // Parcels still on the car (including split destination lines)
        $query = CustomerPacel::with(['destinations.dropMikoa'])
            ->where('car_id', $carId)
            ->where(function ($q) {
                $q->where('idadi_kupakia', '>', 0)
                    ->orWhereHas('destinations', function ($d) {
                        $d->where('idadi_kupakia', '>', 0);
                    });
            });

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $pacels = $query->get();

        $dropLabel = null;
        $dropMikoaId = null;
        $isFinalDestinationDrop = false;

        if ($dropOffPointId !== null && isset($drop)) {
            $this->cargoRouteService->backfillDropPointMikoaId($drop, $companyId);
            $drop->refresh();
            $dropLabel = $drop->drop_off_point;
            $dropMikoaId = $drop->mikoa_id;
        } else {
            // car_id only = unload at final destination (Madekenya has no en-route stops)
            $isFinalDestinationDrop = true;
            $routeForFilter = null;
            if ($this->hasPositiveIntRequestId($request, 'driver_route_id')) {
                $routeForFilter = DriverRoute::find((int) $request->driver_route_id);
            }
            if (!$routeForFilter && $carId) {
                $routeForFilter = DriverRoute::where('car_id', $carId)
                    ->orderByDesc('id')
                    ->first();
            }
            if ($routeForFilter) {
                $dropLabel = $routeForFilter->to;
            }
        }

        if ($isFinalDestinationDrop && $this->hasPositiveIntRequestId($request, 'driver_route_id')) {
            $finalGate = $this->rejectIfFinalBeforeIntermediateStops(
                (int) $request->driver_route_id,
                (int) $carId,
                $companyId
            );
            if ($finalGate) {
                return $finalGate;
            }
        }

        $data = $this->cargoRouteService->expandParcelsForDropList(
            $pacels,
            $dropLabel,
            $dropMikoaId,
            $isFinalDestinationDrop
        );

        if (empty($data)) {
            $payload = [
                'success' => true,
                'error' => false,
                'message' => 'Hakuna mizigo ya kushusha hapa',
                'parcels' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 100,
                    'total' => 0,
                ],
            ];
            Log::debug('drop_off_point_parcels response', array_merge($payload, [
                'drop_off_point_id' => $dropOffPointId,
                'company_id' => $companyId,
                'car_id' => $carId,
            ]));

            return response()->json($payload, 200);
        }

        $page = max(1, (int) $request->query('page', 1));
        $perPage = 100;
        $total = count($data);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $offset = ($page - 1) * $perPage;
        $pagedData = array_slice($data, $offset, $perPage);

        $payload = [
            'success' => true,
            'error' => false,
            'message' => 'successfully',
            'parcels' => $pagedData,
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
            ],
        ];
        Log::debug('drop_off_point_parcels response', array_merge($payload, [
            'drop_off_point_id' => $dropOffPointId,
            'company_id' => $companyId,
            'car_id' => $carId,
            'parcels_count' => count($pagedData),
        ]));

        return response()->json($payload, 200);
    }

    /**
     * Perform dropping (kushusha) of a parcel at a drop off point or at final destination (no stop).
     * Inputs:
     * - customer_pacel_id, idadi_kushusha, driver_route_id (required)
     * - drop_off_point_id OR car_id (required_without each other; prefer drop_off_point_id when both sent)
     * - destination, amount_received (optional)
     *
     * Effects:
     * - Updates CustomerPacel: decreases idadi_kupakia, increases idadi_shusha, activity kushusha
     * - Creates PacelHistory record with activity = 'kushusha'
     * - Records operation in drop_off_parcels (drop_off_point_id null when car-only / destination-only)
     */
    public function drop_parcel(Request $request)
    {
        $this->normalizePlaceholderZeroIds($request);
        $this->inferCarIdFromDriverRouteWhenMissing($request);

        $request->validate([
            'customer_pacel_id' => 'required|integer',
            'customer_pacel_destination_id' => 'nullable|integer',
            'drop_off_point_id' => 'nullable|integer|required_without:car_id',
            'car_id' => 'nullable|integer|required_without:drop_off_point_id',
            'driver_route_id' => 'required|integer',
            'idadi_kushusha' => 'required|integer|min:1',
            'destination' => 'nullable|string',
            'amount_received' => 'nullable|numeric',
        ]);

        $companyId = $this->getCompanyId($request);

        Log::debug('drop_parcel request', [
            'company_id' => $companyId,
            'customer_pacel_id' => (int) $request->customer_pacel_id,
            'drop_off_point_id' => $request->input('drop_off_point_id'),
            'car_id' => $request->input('car_id'),
            'driver_route_id' => (int) $request->driver_route_id,
            'idadi_kushusha' => (int) $request->idadi_kushusha,
            'destination' => $request->destination,
            'amount_received' => $request->amount_received,
            'user_id' => $request->user_id,
            'query' => $request->query(),
            'body' => $request->except(['password', 'password_confirmation', 'token']),
        ]);

        $ctx = $this->resolveDropParcelRouteContext($request, $companyId);
        if (!$ctx['ok']) {
            $resolverPayload = $ctx['response']->getData(true);
            Log::debug('drop_parcel response', array_merge(
                is_array($resolverPayload) ? $resolverPayload : [],
                ['company_id' => $companyId, 'stage' => 'resolveDropParcelRouteContext']
            ));

            return $ctx['response'];
        }

        $dropModel = $ctx['drop'];
        if ($dropModel && (int) ($dropModel->status ?? 0) === 1) {
            // Allow more drops if parcels still remain at this stop (partial unload)
            if (!$this->dropPointHasRemainingParcels(
                $dropModel,
                (int) $ctx['car_id'],
                $companyId
            )) {
                $payload = [
                    'success' => false,
                    'error' => true,
                    'message' => 'Kituo hiki tayari kimeshushwa — huwezi kushusha tena hapa',
                ];
                Log::debug('drop_parcel response', array_merge($payload, [
                    'company_id' => $companyId,
                    'drop_off_point_id' => $dropModel->id,
                ]));

                return response()->json($payload, 200);
            }
            DropOffPoint::where('id', $dropModel->id)->update(['status' => 0]);
            $dropModel->status = 0;
        }

        $carId = $ctx['car_id'];
        $driverId = $ctx['driver_id'];
        $receiptLabel = $ctx['receipt_label'];
        $dropOffPointIdDb = $ctx['drop_off_point_id_db'];

        if ($dropOffPointIdDb !== null && !$this->cargoRouteService->allowsEnrouteDrops()) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Madekenya haishushi mizigo njiani. Kushusha kunaruhusiwa mwisho wa safari tu.',
            ], 200);
        }

        $closedResponse = $this->rejectIfTripClosed(
            (int) $carId,
            (int) $request->driver_route_id,
            $companyId
        );
        if ($closedResponse) {
            return $closedResponse;
        }

        $notStartedResponse = $this->rejectIfTripNotStarted((int) $carId, $companyId);
        if ($notStartedResponse) {
            return $notStartedResponse;
        }

        // Final destination (no drop_off_point): require intermediate stops cleared first
        if ($dropOffPointIdDb === null) {
            $finalGate = $this->rejectIfFinalBeforeIntermediateStops(
                (int) $request->driver_route_id,
                (int) $carId,
                $companyId
            );
            if ($finalGate) {
                return $finalGate;
            }
        }

        $pacel = CustomerPacel::find($request->customer_pacel_id);
        if (!$pacel) {
            $payload = [
                'success' => false,
                'error' => true,
                'message' => 'Parcel not found',
            ];
            Log::debug('drop_parcel response', array_merge($payload, [
                'company_id' => $companyId,
                'customer_pacel_id' => (int) $request->customer_pacel_id,
                'car_id_resolved' => $carId,
            ]));

            return response()->json($payload, 200);
        }

        if ($companyId && $pacel->company_id && (int) $pacel->company_id !== (int) $companyId) {
            $payload = [
                'success' => false,
                'error' => true,
                'message' => 'You are not allowed to drop this parcel',
            ];
            Log::debug('drop_parcel response', array_merge($payload, [
                'company_id' => $companyId,
                'parcel_company_id' => $pacel->company_id,
            ]));

            return response()->json($payload, 200);
        }

        if ((int) $pacel->car_id !== (int) $carId) {
            $payload = [
                'success' => false,
                'error' => true,
                'message' => 'Parcel is not loaded on this car',
            ];
            Log::debug('drop_parcel response', array_merge($payload, [
                'parcel_car_id' => $pacel->car_id,
                'resolved_car_id' => $carId,
            ]));

            return response()->json($payload, 200);
        }

        $idadiKushusha = (int) $request->idadi_kushusha;
        $pacel->loadMissing('destinations');

        $driverRouteId = (int) $request->driver_route_id;
        $destinationLine = null;
        if ($this->hasPositiveIntRequestId($request, 'customer_pacel_destination_id')) {
            $destinationLine = \App\Models\Customer\CustomerPacelDestination::where('id', (int) $request->customer_pacel_destination_id)
                ->where('customer_pacel_id', $pacel->id)
                ->first();
            if (!$destinationLine) {
                $payload = [
                    'success' => false,
                    'error' => true,
                    'message' => 'Sehemu ya mgawanyo haikupatikana kwa mzigo huu',
                ];
                return response()->json($payload, 200);
            }
        }

        $availableOnTruck = $destinationLine
            ? (int) $destinationLine->idadi_kupakia
            : $this->cargoRouteService->packedQuantityOnTruck($pacel);

        if ($availableOnTruck < $idadiKushusha) {
            $payload = [
                'success' => false,
                'error' => true,
                'message' => 'Quantity to drop exceeds packed quantity',
            ];
            Log::debug('drop_parcel response', array_merge($payload, [
                'idadi_kushusha' => $idadiKushusha,
                'idadi_kupakia' => (int) $pacel->idadi_kupakia,
                'available_on_truck' => $availableOnTruck,
                'customer_pacel_id' => $pacel->id,
                'customer_pacel_destination_id' => $destinationLine ? $destinationLine->id : null,
            ]));

            return response()->json($payload, 200);
        }

        // Keep parcel header in sync when destination lines hold the packed qty.
        if ((int) $pacel->idadi_kupakia < $availableOnTruck && !$destinationLine) {
            $pacel->idadi_kupakia = $availableOnTruck;
        } elseif ($destinationLine && (int) $pacel->idadi_kupakia < $idadiKushusha) {
            $pacel->idadi_kupakia = max((int) $pacel->idadi_kupakia, $idadiKushusha);
        }

        $maxPayment = ParcelDropPayment::maxAllowedAmount($pacel);
        $amountReceived = $request->amount_received !== null
            ? (float) $request->amount_received
            : 0.0;

        if ($amountReceived < 0) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Kiasi cha malipo hakiwezi kuwa hasi',
            ], 200);
        }

        if ($amountReceived > $maxPayment + 0.01) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Kiasi kinazidi deni lililobaki: ' . number_format($maxPayment, 0, '.', ','),
                'max_amount' => $maxPayment,
                'amount_received' => $amountReceived,
            ], 200);
        }

        $dropMikoaId = $dropModel ? $dropModel->mikoa_id : null;
        if ($dropModel) {
            $this->cargoRouteService->backfillDropPointMikoaId($dropModel, $companyId);
            $dropModel->refresh();
            $dropMikoaId = $dropModel->mikoa_id;
        }

        if ($dropOffPointIdDb === null) {
            $routeForDrop = DriverRoute::find($driverRouteId);
            if ($destinationLine) {
                if (!$this->cargoRouteService->parcelLineMatchesFinalDestination($destinationLine, $routeForDrop ? $routeForDrop->to : null)) {
                    $payload = [
                        'success' => false,
                        'error' => true,
                        'message' => 'Mzigo huu si wa kushusha mwisho wa safari hii',
                    ];
                    return response()->json($payload, 200);
                }
            } elseif ($routeForDrop && !$this->cargoRouteService->parcelMatchesFinalDestination(
                $pacel,
                $routeForDrop->to,
                $companyId
            )) {
                $payload = [
                    'success' => false,
                    'error' => true,
                    'message' => 'Mzigo huu si wa kushusha mwisho wa safari hii',
                ];
                Log::debug('drop_parcel response', array_merge($payload, [
                    'customer_pacel_id' => $pacel->id,
                    'mzigo_unapokwenda' => $pacel->mzigo_unapokwenda,
                    'drop_mikoa_id' => $pacel->drop_mikoa_id,
                ]));

                return response()->json($payload, 200);
            }
        } elseif ($destinationLine) {
            if (!$this->cargoRouteService->parcelLineMatchesDropPoint($destinationLine, $receiptLabel, $dropMikoaId)) {
                $payload = [
                    'success' => false,
                    'error' => true,
                    'message' => 'Mzigo huu si wa kushusha kwenye kituo hiki',
                ];
                return response()->json($payload, 200);
            }
        } elseif (!$this->cargoRouteService->parcelMatchesDropPoint($pacel, $receiptLabel, $dropMikoaId)) {
            $payload = [
                'success' => false,
                'error' => true,
                'message' => 'Mzigo huu si wa kushusha kwenye kituo hiki',
            ];
            Log::debug('drop_parcel response', array_merge($payload, [
                'customer_pacel_id' => $pacel->id,
                'mzigo_unapokwenda' => $pacel->mzigo_unapokwenda,
                'drop_mikoa_id' => $pacel->drop_mikoa_id,
                'drop_off_point' => $receiptLabel,
            ]));

            return response()->json($payload, 200);
        }

        $userId = $request->user_id ?? (auth()->check() ? auth()->id() : null);
        $destinationParam = $request->destination;

        $isDestination = $dropOffPointIdDb === null
            ? 1
            : (!empty($destinationParam) ? 1 : 0);

        $createdHistory = null;
        DB::transaction(function () use (
            $dropModel,
            $pacel,
            $destinationLine,
            $idadiKushusha,
            $companyId,
            $userId,
            $amountReceived,
            $driverRouteId,
            &$createdHistory,
            $carId,
            $driverId,
            $receiptLabel,
            $dropOffPointIdDb,
            $isDestination
        ) {
            if ($destinationLine) {
                $this->cargoRouteService->dropFromDestinationLine($destinationLine, $idadiKushusha);
            } else {
                $this->cargoRouteService->dropAcrossDestinationLines($pacel, $idadiKushusha);
            }

            $pacel->idadi_kupakia = max(0, (int) $pacel->idadi_kupakia - $idadiKushusha);
            $pacel->idadi_shusha = $pacel->idadi_shusha + $idadiKushusha;
            $pacel->activity = $pacel->idadi_kupakia > 0 ? 'ipo njiani' : 'kushusha';
            if ($amountReceived > 0) {
                $currentReceived = (float) ($pacel->ela_iliyopokelewa ?? 0);
                $pacel->ela_iliyopokelewa = $currentReceived + $amountReceived;
            }
            $pacel->update();

            $receivedVal = (float) ($pacel->ela_iliyopokelewa ?? 0);

            $dropName = $destinationLine && $destinationLine->dropMikoa
                ? $destinationLine->dropMikoa->regional_name
                : $pacel->mzigo_unapokwenda;

            $createdHistory = PacelHistory::create([
                'pacel_id' => $pacel->id,
                'mteja' => $pacel->mteja,
                'mpokeaji' => $pacel->mpokeaji,
                'name' => $pacel->name,
                'idadi' => $pacel->idadi,
                'bei' => $pacel->bei,
                'hashtag' => $pacel->hashtag,
                'receipt' => $receiptLabel,
                'ela_iliyopokelewa' => $receivedVal,
                'jumla' => $pacel->jumla,
                'activity' => 'kushusha',
                'mzigo_unapotoka' => $pacel->mzigo_unapotoka,
                'mzigo_unapokwenda' => $dropName,
                'car_id' => $carId,
                'customer_id' => $pacel->customer_id,
                'driver_id' => $driverId,
                'idadi_kupakia' => $pacel->idadi_kupakia,
                'idadi_shusha' => $idadiKushusha,
                'delivery' => $pacel->delivery,
                'status' => $pacel->status,
                'added_by' => $userId,
                'company_id' => $companyId,
            ]);

            DB::table('drop_off_parcels')->insert([
                'drop_off_point_id' => $dropOffPointIdDb,
                'driver_route_id' => $driverRouteId,
                'customer_pacel_id' => $pacel->id,
                'pacel_history_id' => $createdHistory->id,
                'car_id' => $carId,
                'driver_id' => $driverId,
                'user_id' => $userId,
                'company_id' => $companyId,
                'idadi_kushusha' => $idadiKushusha,
                'amount_received' => $amountReceived,
                'is_destination' => $isDestination,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($dropModel) {
                $this->syncDropPointCompletionStatus($dropModel, (int) $carId, $companyId);
            }

            $assignment = ParcelCarAssignment::where([
                'customer_id' => $pacel->customer_id,
                'car_id' => $carId,
                'pacel_id' => $pacel->id,
            ])->first();

            if ($assignment) {
                $newQuantity = $assignment->idadi - $idadiKushusha;
                if ($newQuantity <= 0) {
                    $assignment->delete();
                } else {
                    $assignment->update(['idadi' => $newQuantity]);
                }
            }
        });

        $jumlaValResponse = (float) str_replace(',', '', $pacel->jumla);
        $receivedValResponse = (float) ($pacel->ela_iliyopokelewa ?? 0);
        $remainingValResponse = $jumlaValResponse - $receivedValResponse;

        // SMS [Amount]: show payment taken on this drop when provided; otherwise balance still due (legacy)
        $arrivalSmsAmount = ($amountReceived !== null)
            ? (float) $amountReceived
            : $remainingValResponse;

        try {
            $customer = Customer::find($pacel->customer_id);
            if ($customer && $customer->customer_phone) {
                $packagesInfo = $idadiKushusha . " " . $pacel->name;
                $this->smsService->sendArrivalAtDestinationSms(
                    $customer->customer_phone,
                    $receiptLabel,
                    $packagesInfo,
                    $arrivalSmsAmount,
                    $customer->id,
                    $companyId,
                    $pacel->delivery
                );
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send arrival SMS for parcel {$pacel->id}: " . $e->getMessage());
        }

        // Same dispatch SMS — only customers whose parcels still travel past this stop
        if ($dropOffPointIdDb !== null) {
            try {
                $carForSms = Car::find($carId);
                $parcelsQuery = CustomerPacel::where('car_id', $carId)
                    ->whereIn('activity', ['kupakia', 'ipo njiani']);
                if ($companyId) {
                    $parcelsQuery->where('company_id', $companyId);
                }
                $parcelsByCustomer = $parcelsQuery->get()->groupBy('customer_id');

                foreach ($parcelsByCustomer as $customerIdLoop => $customerParcels) {
                    $stillTraveling = $customerParcels->filter(function ($p) use ($receiptLabel) {
                        return !$this->cargoRouteService->destinationsMatch(
                            $p->mzigo_unapokwenda ?? '',
                            $receiptLabel
                        );
                    });

                    if ($stillTraveling->isEmpty()) {
                        continue;
                    }

                    $customerLoop = Customer::find($customerIdLoop);
                    if ($customerLoop && $customerLoop->customer_phone && $carForSms) {
                        $totalPackages = $stillTraveling->sum('idadi');
                        $this->smsService->sendDispatchSms(
                            $customerLoop->customer_phone,
                            $carForSms->carNumber,
                            $totalPackages,
                            $customerLoop->id,
                            $companyId
                        );
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send dispatch SMS after drop_parcel (drop-off point): " . $e->getMessage());
            }
        }

        $payload = [
            'success' => true,
            'error' => false,
            'message' => 'Parcel dropped successfully',
            'pacel_history' => $createdHistory,
            'jumla_kuu' => $jumlaValResponse,
            'ela_iliyopokelewa' => $receivedValResponse,
            'hela_iliyobaki' => $remainingValResponse,
        ];
        Log::debug('drop_parcel response', [
            'success' => true,
            'error' => false,
            'message' => $payload['message'],
            'customer_pacel_id' => $pacel->id,
            'pacel_history_id' => $createdHistory ? $createdHistory->id : null,
            'company_id' => $companyId,
            'car_id' => $carId,
            'driver_id' => $driverId,
            'drop_off_point_id_db' => $dropOffPointIdDb,
            'driver_route_id' => $driverRouteId,
            'idadi_kushusha' => $idadiKushusha,
            'receipt_label' => $receiptLabel,
            'is_destination' => $isDestination,
            'jumla_kuu' => $jumlaValResponse,
            'ela_iliyopokelewa' => $receivedValResponse,
            'hela_iliyobaki' => $remainingValResponse,
        ]);

        return response()->json($payload, 200);
    }

    /**
     * Update parcel payment (received money) without dropping any quantity.
     * Inputs:
     * - customer_pacel_id, driver_route_id, amount_received (required)
     * - drop_off_point_id OR car_id (required_without each other)
     *
     * Effects:
     * - Updates CustomerPacel: increases ela_iliyopokelewa
     * - Creates PacelHistory record with activity = 'malipo'
     * - Records operation in drop_off_parcels table with idadi_kushusha = 0
     */
    public function update_parcel_payment(Request $request)
    {
        return response()->json([
            'success' => false,
            'error' => true,
            'message' => 'Malipo yanapaswa kukusanywa wakati wa kushusha tu. Huruhusiwi kupokea tena kwa drop-off hii.',
        ], 200);

        $this->normalizePlaceholderZeroIds($request);
        $this->inferCarIdFromDriverRouteWhenMissing($request);

        $request->validate([
            'customer_pacel_id' => 'required|integer',
            'drop_off_point_id' => 'nullable|integer|required_without:car_id',
            'car_id' => 'nullable|integer|required_without:drop_off_point_id',
            'driver_route_id' => 'required|integer',
            'amount_received' => 'required|numeric',
        ]);

        $companyId = $this->getCompanyId($request);

        $ctx = $this->resolveDropParcelRouteContext($request, $companyId);
        if (!$ctx['ok']) {
            return $ctx['response'];
        }

        $dropModel = $ctx['drop'];
        $carId = $ctx['car_id'];
        $driverId = $ctx['driver_id'];
        $receiptLabel = $ctx['receipt_label'];
        $dropOffPointIdDb = $ctx['drop_off_point_id_db'];

        $closedResponse = $this->rejectIfTripClosed(
            (int) $carId,
            (int) $request->driver_route_id,
            $companyId
        );
        if ($closedResponse) {
            return $closedResponse;
        }

        $notStartedResponse = $this->rejectIfTripNotStarted((int) $carId, $companyId);
        if ($notStartedResponse) {
            return $notStartedResponse;
        }

        $pacel = CustomerPacel::find($request->customer_pacel_id);
        if (!$pacel) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Parcel not found',
            ], 200);
        }

        if ($companyId && $pacel->company_id && (int) $pacel->company_id !== (int) $companyId) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'You are not allowed to update this parcel',
            ], 200);
        }

        if ((int) $pacel->car_id !== (int) $carId) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Parcel is not loaded on this car',
            ], 200);
        }

        $userId = $request->user_id ?? (auth()->check() ? auth()->id() : null);
        $amountReceived = (float) $request->amount_received;
        $driverRouteId = (int) $request->driver_route_id;

        $isDestination = $dropOffPointIdDb === null ? 1 : 0;

        $createdHistory = null;
        DB::transaction(function () use (
            $dropModel,
            $pacel,
            $companyId,
            $userId,
            $amountReceived,
            $driverRouteId,
            &$createdHistory,
            $carId,
            $driverId,
            $receiptLabel,
            $dropOffPointIdDb,
            $isDestination
        ) {
            // Update CustomerPacel: increment money received
            $currentReceived = (float) ($pacel->ela_iliyopokelewa ?? 0);
            $pacel->ela_iliyopokelewa = $currentReceived + $amountReceived;
            $pacel->activity = 'malipo'; // Use 'malipo' for payment only updates
            $pacel->update();

            $receivedVal = (float) ($pacel->ela_iliyopokelewa ?? 0);

            // Create history record
            $createdHistory = PacelHistory::create([
                'pacel_id' => $pacel->id,
                'mteja' => $pacel->mteja,
                'mpokeaji' => $pacel->mpokeaji,
                'name' => $pacel->name,
                'idadi' => $pacel->idadi,
                'bei' => $pacel->bei,
                'hashtag' => $pacel->hashtag,
                'receipt' => $receiptLabel,
                'ela_iliyopokelewa' => $receivedVal,
                'jumla' => $pacel->jumla,
                // Mark this history entry explicitly as a payment update
                'activity' => 'malipo',
                'mzigo_unapotoka' => $pacel->mzigo_unapotoka,
                'mzigo_unapokwenda' => $pacel->mzigo_unapokwenda,
                'car_id' => $carId,
                'customer_id' => $pacel->customer_id,
                'driver_id' => $driverId,
                'idadi_kupakia' => $pacel->idadi_kupakia,
                'idadi_shusha' => 0, // No quantity dropped in this operation
                'delivery' => $pacel->delivery,
                'status' => $pacel->status,
                'added_by' => $userId,
                'company_id' => $companyId,
            ]);

            // Record operation in drop_off_parcels
            DB::table('drop_off_parcels')->insert([
                'drop_off_point_id' => $dropOffPointIdDb,
                'driver_route_id' => $driverRouteId,
                'customer_pacel_id' => $pacel->id,
                'pacel_history_id' => $createdHistory->id,
                'car_id' => $carId,
                'driver_id' => $driverId,
                'user_id' => $userId,
                'company_id' => $companyId,
                'idadi_kushusha' => 0, // 0 for payment only
                'amount_received' => $amountReceived,
                'is_destination' => $isDestination,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Payment-only: do not mark the stop complete
        });

        // Updated balance info for response
        $jumlaValResponse = (float) str_replace(',', '', $pacel->jumla);
        $receivedValResponse = (float) ($pacel->ela_iliyopokelewa ?? 0);
        $remainingValResponse = $jumlaValResponse - $receivedValResponse;

        // Send Payment Confirmation SMS
        try {
            $customer = Customer::find($pacel->customer_id);
            if ($customer && $customer->customer_phone) {
                $this->smsService->sendPaymentConfirmationSms(
                    $customer->customer_phone,
                    $amountReceived,
                    $customer->id,
                    $companyId
                );
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send payment confirmation SMS for parcel {$pacel->id}: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'Payment updated successfully',
            'pacel_history' => $createdHistory,
            'jumla_kuu' => $jumlaValResponse,
            'ela_iliyopokelewa' => $receivedValResponse,
            'hela_iliyobaki' => $remainingValResponse,
        ], 200);
    }

    /**
     * Get drop off parcels history filtered by driver_route_id.
     * Inputs (query params or body):
     * - driver_route_id (required)
     */
    public function drop_off_parcels_history(Request $request)
    {
        $request->validate([
            'driver_route_id' => 'required|integer',
        ]);

        $companyId = $this->getCompanyId($request);

        $query = DB::table('drop_off_parcels')
            ->join('customer_pacels', 'customer_pacels.id', '=', 'drop_off_parcels.customer_pacel_id')
            ->leftJoin('drop_off_points', 'drop_off_points.id', '=', 'drop_off_parcels.drop_off_point_id')
            ->leftJoin('pacel_histories', 'pacel_histories.id', '=', 'drop_off_parcels.pacel_history_id')
            ->select(
                'drop_off_parcels.id',
                'drop_off_parcels.car_id',
                'drop_off_parcels.driver_route_id',
                'drop_off_parcels.drop_off_point_id',
                'drop_off_parcels.customer_pacel_id', // explicitly include parcel ID
                'drop_off_parcels.idadi_kushusha',
                'drop_off_parcels.is_destination',
                'drop_off_parcels.amount_received as transaction_amount', // correct transaction amount
                'drop_off_parcels.created_at',
                'customer_pacels.mteja',
                'customer_pacels.mpokeaji',
                'customer_pacels.name as parcel_name',
                'customer_pacels.jumla',
                'customer_pacels.ela_iliyopokelewa',
                'customer_pacels.bei',
                'customer_pacels.idadi',
                'customer_pacels.idadi_kupakia',
                'drop_off_points.drop_off_point',
                'drop_off_points.status as drop_status'
            )
            ->where('drop_off_parcels.driver_route_id', $request->driver_route_id);

        if ($companyId) {
            $query->where('drop_off_parcels.company_id', $companyId);
        }

        $rows = $query->orderBy('drop_off_parcels.created_at', 'asc')->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'No drop off parcel history found for the given route',
            ], 200);
        }

        $data = [];
        foreach ($rows as $row) {
            $jumlaVal = (float) str_replace(',', '', $row->jumla);
            $receivedVal = (float) ($row->ela_iliyopokelewa ?? 0);
            $remainingVal = $jumlaVal - $receivedVal;

            $data[] = [
                'id' => $row->id,
                'car_id' => $row->car_id,
                'driver_route_id' => $row->driver_route_id,
                'drop_off_point_id' => $row->drop_off_point_id,
                'customer_pacel_id' => $row->customer_pacel_id, // expose correctly for the app
                'drop_off_point' => $row->drop_off_point,
                'drop_status' => isset($row->drop_status) ? (int) $row->drop_status : null,
                'mteja' => $row->mteja,
                'mpokeaji' => $row->mpokeaji,
                'parcel_name' => $row->parcel_name,
                'idadi_kushusha' => $row->idadi_kushusha,
                'idadi_kupakia' => (int) ($row->idadi_kupakia ?? 0),
                'idadi' => (int) ($row->idadi ?? 0),
                'bei' => (float) str_replace(',', '', $row->bei ?? 0),
                'is_destination' => (int) $row->is_destination,
                'amount_received' => $row->transaction_amount, // specific to this drop
                'jumla_kuu' => $jumlaVal,
                'ela_iliyopokelewa' => $receivedVal, // cumulative
                'hela_iliyobaki' => $remainingVal,
                'created_at' => $row->created_at,
            ];
        }

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'successfully',
            'history' => $data,
        ], 200);
    }

    /**
     * Update an existing drop off point (edit submitted drop off).
     * Allows changing drop_off_point text and optionally car/driver/route links.
     */
    public function drop_off_point_update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'drop_off_point' => 'required|string',
            'car_id' => 'nullable|integer',
            'driver_id' => 'nullable|integer',
            'driver_route_id' => 'nullable|integer',
        ]);

        $companyId = $this->getCompanyId($request);

        $drop = DropOffPoint::find($request->id);
        if (!$drop) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Drop off point not found',
            ], 200);
        }

        // Ensure this drop off belongs to the same company (if company_id is set)
        if ($companyId && $drop->company_id && (int)$drop->company_id !== (int)$companyId) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'You are not allowed to edit this drop off point',
            ], 200);
        }

        // Apply updates
        $drop->drop_off_point = $request->drop_off_point;
        if (!empty($request->car_id)) {
            $drop->car_id = $request->car_id;
        }
        if (!empty($request->driver_id)) {
            $drop->driver_id = $request->driver_id;
        }
        if (!empty($request->driver_route_id)) {
            $drop->driver_route_id = $request->driver_route_id;
        }
        if ($companyId) {
            $drop->company_id = $companyId;
        }
        $drop->save();

        // Reload with joins to include source/destination and driver name
        $row = DropOffPoint::query()
            ->leftJoin('driver_routes', 'driver_routes.id', '=', 'drop_off_points.driver_route_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'drop_off_points.driver_id')
            ->select(
                'drop_off_points.id',
                'drop_off_points.car_id',
                'drop_off_points.company_id',
                'drop_off_points.driver_id',
                'drop_off_points.drop_off_point',
                'driver_routes.from as source',
                'driver_routes.to as destination',
                'drivers.name as driver_name'
            )
            ->where('drop_off_points.id', $drop->id)
            ->first();

        $result = $row ? [
            'id' => $row->id,
            'car_id' => (int) $row->car_id,
            'company_id' => $row->company_id,
            'drop_off_point' => $row->drop_off_point,
            'driver_id' => $row->driver_id,
            'driver_name' => $row->driver_name,
            'source' => $row->source,
            'destination' => $row->destination,
        ] : null;

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'Drop off point updated successfully',
            'drop_off_point' => $result,
        ], 200);
    }

    // public function test_unique_number(){
    //     $latestOrder = App\Order::orderBy('created_at','DESC')->first();
    //     $order->order_nr = '#'.str_pad($latestOrder->id + 1, 8, "0", STR_PAD_LEFT);
    // }
    
    // mizigo according to date
    public function pacel_today(Request $request)
    {
        //
        $today = Carbon::today();
        // $pacels = CustomerPacel::whereDate('created_at', $date)->get();
        // $pacels = CustomerPacel::where('idadi_stoo', '!=', '0')->get();

        // $data['mizigo_stoo'] = CustomerPacel::where('idadi_stoo', '!=', '0')->count();
        
        // $date = "2022-11-29";
        // $date = "2022-12-16";
        // $date = "2023-02-19";
         // $date = "2023-05-03";
        // $date = "2023-07-22";
        // $date = "2023-08-26";
        // $date = "2023-09-21";
        // $date = "2023-11-0

        
        $date = "2025-10-19";
        
		// Get company_id from request
		$companyId = $this->getCompanyId($request);
		
		// Build query with optional fuzzy search on mteja+mpokeaji, then paginate by 6
		$query = CustomerPacel::where('idadi_stoo', '!=', '0')
								->whereDate('created_at', '>', $date);
		
		// Filter by company_id if available
		if ($companyId) {
			$query->where('company_id', $companyId);
		}
		$search = request('search', request('q'));
		$location = request('location');
		if (!empty($search)) {
			$searchStr = trim((string)$search);
			// If user provides comma-separated terms, treat them as a list that applies to `name` only
			if (strpos($searchStr, ',') !== false) {
				$rawTerms = array_values(array_filter(array_map(function($t){
					return trim($t);
				}, explode(',', $searchStr)), function($t){ return $t !== ''; }));
				// Further split each comma term by the word "or" (case-insensitive)
				$terms = [];
				foreach ($rawTerms as $rt) {
					$split = preg_split('/\bor\b/i', $rt);
					foreach ($split as $st) {
						$st = trim($st);
						if ($st !== '') { $terms[] = $st; }
					}
				}
				if (!empty($terms)) {
					// Require at least one match on name among the provided terms
					$query->where(function($q) use ($terms) {
						foreach ($terms as $t) {
							$needle = preg_replace('/\s+/', '', strtolower($t));
							$len = strlen($needle);
							if ($len === 0) { continue; }
							$minLen = (int)ceil($len * 0.6);
							$segment = substr($needle, 0, $minLen);
							$q->orWhereRaw("LOWER(REPLACE(name, ' ', '')) LIKE ?", ['%' . $segment . '%']);
						}
					});

					// Rank by number of matched terms on name, then most recent
					$caseParts = [];
					$orderBindings = [];
					foreach ($terms as $t) {
						$termLower = strtolower($t);
						$needleNoSpace = preg_replace('/\s+/', '', $termLower);
						$len = strlen($needleNoSpace);
						if ($len === 0) { continue; }
						$minLen = (int)ceil($len * 0.6);
						$segment = substr($needleNoSpace, 0, $minLen);
						// Base score (1) for partial match on name (spaces ignored)
						$caseParts[] = "CASE WHEN LOWER(REPLACE(name, ' ', '')) LIKE ? THEN 1 ELSE 0 END";
						$orderBindings[] = '%' . $segment . '%';
						// Extra boost if the term is a multi-word phrase (weight 5)
						if (preg_match('/\s+/', $t)) {
							$caseParts[] = "CASE WHEN LOWER(name) LIKE ? THEN 5 ELSE 0 END";
							$orderBindings[] = '%' . $termLower . '%';
						}
						// Strong boost for exact name equality (weight 10)
						$caseParts[] = "CASE WHEN LOWER(name) = ? THEN 10 ELSE 0 END";
						$orderBindings[] = $termLower;
					}
					if (!empty($caseParts)) {
						$orderExpr = '(' . implode(' + ', $caseParts) . ') DESC, created_at DESC';
						// Use orderByRaw with bindings for safe parameterization
						$query->orderByRaw($orderExpr, $orderBindings);
					} else {
						$query->orderBy('created_at', 'DESC');
					}
				}
			} else {
				// Default single-term search: fuzzy across mteja, mpokeaji, and also include name
				$needle = preg_replace('/\s+/', '', $searchStr);
				$len = strlen($needle);
				if ($len > 0) {
					$minLen = (int)ceil($len * 0.6);
					$segment = substr($needle, 0, $minLen);
					// Fuzzy match 60%: search contiguous in concatenated mteja+mpokeaji or in name (ignore spaces, case-insensitive)
					$query->where(function($q) use ($segment) {
						$q->whereRaw(
							"LOWER(REPLACE(CONCAT(COALESCE(mteja,''), ' ', COALESCE(mpokeaji,'')), ' ', '')) LIKE ?",
							['%' . strtolower($segment) . '%']
						)
						->orWhereRaw(
							"LOWER(REPLACE(name, ' ', '')) LIKE ?",
							['%' . strtolower($segment) . '%']
						);
					});

					// Build relevance ranking for any input (no hard-coded terms):
					// - Exact full phrase equality highest
					// - Full phrase substring next
					// - More token matches rank higher
					$fullPhraseLower = strtolower(trim($searchStr));
					$tokens = preg_split('/\s+/', trim($searchStr));
					$tokens = array_values(array_filter(array_map(function($t){ return trim($t); }, $tokens), function($t){ return $t !== ''; }));

					$scoreParts = [];
					$scoreBindings = [];
					// Exact equality
					$scoreParts[] = "CASE WHEN LOWER(name) = ? THEN 20 ELSE 0 END";
					$scoreBindings[] = $fullPhraseLower;
					// Full phrase substring
					$scoreParts[] = "CASE WHEN LOWER(name) LIKE ? THEN 8 ELSE 0 END";
					$scoreBindings[] = '%' . $fullPhraseLower . '%';
					// Token presence scoring (spaces-insensitive)
					foreach ($tokens as $tok) {
						$tokNoSpace = preg_replace('/\s+/', '', strtolower($tok));
						if ($tokNoSpace === '') { continue; }
						$tokMinLen = (int)ceil(strlen($tokNoSpace) * 0.6);
						$tokSeg = substr($tokNoSpace, 0, $tokMinLen);
						$scoreParts[] = "CASE WHEN LOWER(REPLACE(name, ' ', '')) LIKE ? THEN 2 ELSE 0 END";
						$scoreBindings[] = '%' . $tokSeg . '%';
					}
					if (!empty($scoreParts)) {
						$scoreExpr = '(' . implode(' + ', $scoreParts) . ') DESC, created_at DESC';
						$query->orderByRaw($scoreExpr, $scoreBindings);
					}
				}
			}
		}
		if (!empty($location)) {
			// Split on whitespace; require each token to match either origin or destination (AND across tokens, OR across columns)
			$tokens = preg_split('/\s+/', trim((string)$location), -1, PREG_SPLIT_NO_EMPTY);
			if (!empty($tokens)) {
				foreach ($tokens as $tok) {
					$tokStripped = preg_replace('/\s+/', '', $tok);
					$tokLen = strlen($tokStripped);
					if ($tokLen === 0) { continue; }
					$tokMinLen = (int)ceil($tokLen * 0.6);
					$tokSegment = substr($tokStripped, 0, $tokMinLen);
					$query->where(function($q) use ($tokSegment) {
						$q->whereRaw("LOWER(REPLACE(mzigo_unapotoka, ' ', '')) LIKE ?", ['%' . strtolower($tokSegment) . '%'])
						  ->orWhereRaw("LOWER(REPLACE(mzigo_unapokwenda, ' ', '')) LIKE ?", ['%' . strtolower($tokSegment) . '%']);
					});
				}
			}
		}
		// If not already ordered by the comma-term ranking above, ensure recent first.
		// Add id as a stable tie-breaker so paginated responses do not repeat rows.
		if (empty($search) || strpos((string)$search, ',') === false) {
			$query->orderBy('created_at', 'DESC');
		}
		$query->orderBy('id', 'DESC');
		$paginator = $query->paginate(180);
		$pacels = collect($paginator->items())
            ->filter(function ($row) {
                return ParcelQuantitySummary::breakdown($row)['has_remaining_stoo'];
            })
            ->unique('id')
            ->values();

        $mizigoStooQuery = CustomerPacel::where('idadi_stoo', '!=', '0')->whereDate('created_at', '<', $date);
        if ($companyId) {
            $mizigoStooQuery->where('company_id', $companyId);
        }
        $data['mizigo_stoo'] = $mizigoStooQuery->count();

        // $mizigo = $pacels->count();

        if($pacels->isEmpty()){

            $response=['success'=>false,'error'=>true,'message'=>'No Pacels found on that date'];
            return response()->json($response,200);

            
        }
        
        else{
            $farmers = [];
            foreach($pacels as $row){
                $summary = ParcelQuantitySummary::breakdown($row);
                
                $data['id'] = $row->id;
                $data['name'] = $row->name;
                $data['idadi'] = $row->idadi;

                $data['delivery'] = $row->delivery;

                $data['bei'] = $row->bei;

                $data['idadi_stoo'] = $summary['stoo'];


                $data['mteja'] = $row->mteja;

                $data['mpokeaji'] = $row->mpokeaji;

                $data['hashtag'] = $row->hashtag;





                // $customer = Customer::where('id', $row->pacel_id)->get();

                // foreach($customer as $row2){

                $data['activity'] = $row->activity;

                // }

                $data['mzigo_unapotoka'] = $row->mzigo_unapotoka;
                $data['created_at'] = $row->created_at
                    ? Carbon::parse($row->created_at)->setTimezone('Africa/Nairobi')->toDateTimeString()
                    : null;
                $data['mzigo_unapokwenda'] = $row->mzigo_unapokwenda;
                $data['jumla'] = $row->jumla;
                $data['ela_iliyopokelewa'] = $row->ela_iliyopokelewa;

                $data = ParcelStatusLabel::enrichPayload($data, $row);

                $farmers[] = $data;
            }

            $response=[
                'success'=>true,
                'error'=>false,
                'message'=>'successfully',
                'pacels'=>$farmers,
                'pagination'=>[
                    'current_page'=>$paginator->currentPage(),
                    'last_page'=>$paginator->lastPage(),
                    'per_page'=>$paginator->perPage(),
                    'total'=>$paginator->total(),
                ]
            ];
            return response()->json($response,200);
        }
    }


    // total parcels according to the same filter as pacel_today
     public function total_pacel(Request $request)
    {
        // Get company_id from request
        $companyId = $this->getCompanyId($request);
        
        // Sum parcels from 2025-10-19 to current date
        $query = CustomerPacel::where('idadi_stoo', '!=', '0')
            ->where('created_at', '>=', '2025-10-19')
            ->where('created_at', '<=', now());
        
        // Filter by company_id if available
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        $pacels = $query->get();
        $totalParcels = 0;
        foreach ($pacels as $pacel) {
            $summary = ParcelQuantitySummary::breakdown($pacel);
            $totalParcels += $summary['stoo'];
        }

        $response = [
            'success' => true,
            'error' => false,
            'message' => 'successfully',
            'total_parcels' => $totalParcels,
        ];

        return response()->json($response, 200);
    }

    //mizigo siku saba nyuma

    public function pacel_Date2(String $date)
    {
        //
        // $dt = Carbon::now();
        // $month2 = $dt->subDays(7);

       $xy = Carbon::createFromFormat('Y-m-d', $date)->subDays(7)->format('Y-m-d');

       

        $pacels = CustomerPacel::whereBetween('created_at', [$xy, $date])
                                ->where('idadi_stoo', '!=', '0')
                                ->orderBy('created_at', 'DESC')
                                ->orderBy('id', 'DESC')
                                ->get()
                                ->filter(function ($row) {
                                    return ParcelQuantitySummary::breakdown($row)['has_remaining_stoo'];
                                })
                                ->unique('id')
                                ->values();

        
        // $pacels = CustomerPacel::all();

        // $mizigo = $pacels->count();

        if($pacels->isEmpty()){

            $response=['success'=>false,'error'=>true,'message'=>'No Pacels found on that date'];
            return response()->json($response,200);

            
        }
        
        else{
            $farmers = [];
            foreach($pacels as $row){
                $summary = ParcelQuantitySummary::breakdown($row);
                
                $data['id'] = $row->id;
                $data['name'] = $row->name;
                $data['idadi'] = $row->idadi;

                $data['delivery'] = $row->delivery;

                $data['bei'] = $row->bei;

                $data['idadi_stoo'] = $summary['stoo'];


                $data['mteja'] = $row->mteja;

                $data['mpokeaji'] = $row->mpokeaji;

                $data['hashtag'] = $row->hashtag;





                // $customer = Customer::where('id', $row->pacel_id)->get();

                // foreach($customer as $row2){

                $data['activity'] = $row->activity;

                // }

                $data['mzigo_unapotoka'] = $row->mzigo_unapotoka;
                $data['created_at'] = $row->created_at
                    ? Carbon::parse($row->created_at)->setTimezone('Africa/Nairobi')->toDateTimeString()
                    : null;
                $data['mzigo_unapokwenda'] = $row->mzigo_unapokwenda;
                $data['jumla'] = $row->jumla;
                $data['ela_iliyopokelewa'] = $row->ela_iliyopokelewa;

                $data = ParcelStatusLabel::enrichPayload($data, $row);

                $farmers[] = $data;
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$farmers];
            return response()->json($response,200);
        }
    }

    //car zilizopakia mtu according to tarehe
    public function car_today(int $id, String $date)
    {
        //
        // $today = Carbon::today();
        $carSelected = DriverRoute::join('users', 'users.id', '=', 'driver_routes.user_id' )
                    ->join('cars', 'cars.id', '=', 'driver_routes.car_id' )
                    ->whereIn('cars.status', ['1','2','4'])
                    ->where('users.id', $id)->whereDate('driver_routes.created_at', '<=' ,$date)
                    ->get();
        
        // $pacels = Car::where()->whereDate('created_at', $date)->get();
        // $mizigo = $pacels->count();

        if($carSelected->isEmpty()){

            $response=['success'=>false,'error'=>true,'message'=>'No Cars found with that user id'];
            return response()->json($response,200);

            
        }
        
        else{

            foreach($carSelected as $row){

                $car = Car::find($row->car_id);
             
                $data['carNumber'] = $car->carNumber;
                $data['id'] = intval($car->id);
                $driver = Driver::where('id', $car->driver_id)->value('name');
                $data['from'] = $row->from;

                $data['to'] = $row->to;

                $data['driver_id'] = intval($car->driver_id);

                $data['driver'] = $driver;


                $farmers = $data;
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','car'=>$farmers];
            return response()->json($response,200);
        }
    }


    // 
    public function driver(String $date, Request $request)
    {
        //
        $companyId = $this->getCompanyId($request);
        $query = Driver::where('status', '1');
        
        // Filter by company_id if available
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        $drivers = $query->get();
        // $mizigo = $pacels->count();

       
        
        if($drivers->isEmpty()){

            $response=['success'=>false,'error'=>true,'message'=>'No driver found'];
            return response()->json($response,200);

            
        }
        else{

            foreach($drivers as $row){

                $data['id'] = $row->id;
                

                $data['name'] = $row->name;
                $phone = $row->phone;
                if(empty($phone)){

                    $data['phone'] = "no phone registerd";

                }
                else{

                    $data['phone'] = $row->phone;

 
                }

                if( $row->status == '2'){

                $data['status'] = "Assigned";

                }
                elseif( $row->status == '1'){

                $data['status'] = "Not Assigned";

                }
                else{

                    $data['status'] = "Start offload";
    
                    }

                if ($row->assigned_date == $date) {
                        $data['assigned'] = "true";
                    }

                else{
                     
                        $data['assigned'] = "false";
                        
                    }    

                
                
                

                $farmers[] = $data;
        
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','drivers'=>$farmers];
            return response()->json($response,200);

            
        } 
    }

    public function car(Request $request)
    {
		// Get company_id from request
		$companyId = $this->getCompanyId($request);
		
		// build query with optional filter
		$query = Car::query();
		
		// Filter by company_id if available
		if ($companyId) {
			$query->where('company_id', $companyId);
		}
		
        $filter = request('filter');
		$search = request('search', request('q'));
        if (!empty($filter)) {
            $filter = strtolower($filter);
            if ($filter === 'inapakiwa') {
                // status '2' corresponds to 'inapakiwa' (loading)
                $query->where('status', '2');
            } elseif ($filter === 'yaliyofika') {
                // status '1' corresponds to cars that have arrived
                $query->where('status', '1');
            } elseif ($filter === 'yaliyonjiani') {
                // status '0' corresponds to cars in transit
                $query->where('status', '0');
            } elseif ($filter === 'yote') {
                // 'yote' means all statuses; no additional where clause
            }
        }
		if (!empty($search)) {
			$needle = preg_replace('/\s+/', '', (string)$search);
			$len = strlen($needle);
			if ($len > 0) {
				$minLen = (int)ceil($len * 0.6);
				$segment = substr($needle, 0, $minLen);
				// Match where at least 60% of the query appears contiguously in carNumber (case-insensitive, ignoring spaces)
				$query->whereRaw("LOWER(REPLACE(carNumber, ' ', '')) LIKE ?", ['%' . strtolower($segment) . '%']);
			}
		}

		$paginator = $query->orderBy('created_at', 'DESC')->paginate(40);
		$cars = collect($paginator->items());

		if($cars->isEmpty()){

			$response=['success'=>false,'error'=>true,'message'=>'No Cars found'];
			return response()->json($response,200);

			
		}
		else{

			foreach($cars as $row){

                $data['id'] = $row->id;


                $data['carNumber'] = $row->carNumber;
                //driver_id

                $driver_id = $row->driver_id;
                if(empty($driver_id)){
                    $data['driver_id'] = null;

                    $data['driver'] = 'null';

                    $data['from'] = null;

                    $data['to'] = null;

                    $data['start_date'] = null;

                }
                else{
                    $driver = Driver::find($driver_id);

                    if ($driver) {
                        $data['driver_id'] = intval($driver->id);
                        $data['driver'] = $driver->name;
                    } else {
                        $data['driver_id'] = null;
                        $data['driver'] = 'null';
                    }

                    $driverRoute = DriverRoute::where('car_id', $row->id)
                        ->where('driver_id', $row->driver_id)
                        ->orderBy('created_at', 'DESC')
                        ->first();

                    if ($row->status == '2' && $driverRoute) {
                        $data['from'] = $driverRoute->from;
                        $data['to'] = $driverRoute->to;
                        $data['start_date'] = $driverRoute->start_date;
                    } else {
                        $data['from'] = null;
                        $data['to'] = null;
                        $data['start_date'] = null;
                    }
                }


                // Return raw numeric status so mobile can map 0,1,2,3,5 itself
                $data['status'] = (int) $row->status;

				$farmers[] = $data;
			}

			$response=[
				'success'=>true,
				'error'=>false,
				'message'=>'successfully',
				'cars'=>$farmers,
				'pagination'=>[
					'current_page'=>$paginator->currentPage(),
					'last_page'=>$paginator->lastPage(),
					'per_page'=>$paginator->perPage(),
					'total'=>$paginator->total(),
				]
			];
			return response()->json($response,200);

            
        } 
    }
    
    public function car_today_test($id){

        $carNumber = Car::find(intval($id))->carNumber;
        
        $carId = $id;

        $dataResult = DriverRoute::where('car_id', $id)->groupBy(DB::RAW('DATE(start_date)'))->groupBy(DB::RAW('DATE(closeDate)'))->orderBy('created_at', 'ASC')->get();

        if($dataResult->isNotEmpty()){

            $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$dataResult];
            return response()->json($response,200);
            
        }
        else{
            $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
            return response()->json($response,200);
        }
        // return view('management.car_today_routes', compact('carNumber', 'dataResult'));
    }
    
    
    public function packing_pacel_date_new(int $id){

      
        
        $dataResult = DriverRoute::where('car_id', $id)->groupBy(DB::RAW('DATE(start_date)'))->groupBy(DB::RAW('DATE(closeDate)'))->orderBy('created_at', 'DESC')->get();

        if($dataResult->isEmpty()){

            $response=['success'=>false,'error'=>true,'message'=>'No Data found'];
            return response()->json($response,200);

            
        }
        else{
            
                
                foreach($dataResult as $row){
                    
                    $data['day'] = $row->start_date;
                    
                    $data['closeDate'] = $row->closeDate;
                    
                    $totalCount = PacelHistory::where('car_id', $id)->whereDate('created_at', '>=', $row->start_date)->whereDate('created_at', '<=', $row->closeDate)->sum('idadi');
                   
                    $data['totalPacel'] = $totalCount;
    
                    $farmers[] = $data;

                }
    
                
    
                $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$farmers];
                return response()->json($response,200);

            
            
        } 

      

    }

    public function packing_pacel_date(int $id){

        // $pacels = PacelHistory::where('car_id'. $id)
        //             ->groupBy('created_at')
        //             ->orderBy('created_at', 'DESC')
        //             ->get(array(
        //                 DB::raw('Date(created_at) as date'),
        //                 DB::raw('COUNT(*) as "views"')
        //             ));
       
         
        
        $pacels = PacelHistory::where('car_id',$id)
        ->orderBy('created_at', 'DESC')
        ->get()
        ->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
         });

         

        if($pacels->isEmpty()){

            $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
            return response()->json($response,200);

            
        }
        else{

            foreach($pacels as $key => $pacel){
                $data['day'] = $key;
                // $data['totalCount'] = $pacel->count();
                $data['totalPacel'] = $pacel->sum('idadi');

                $farmers[] = $data;

            }

            

            $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$farmers];
            return response()->json($response,200);
            
        } 

        // foreach($pacels as $row){

        //     $data['date'] = $row->created_at;
            
        // }

    }
    
    
     public function pacel_packet_lenght_request_fx(int $id , String  $startDate, String  $endDate)
    {
        
           if($endDate == "0000-00-00"){
    
            $nowDate = Carbon::now()->format('Y-m-d');
            
            }
            else{
                $nowDate = $endDate;
            }
            
            
        
         $pacelUnique =  PacelHistory::where('car_id', $id)
                                        ->whereDate('created_at', '>=', $startDate)
                                        ->whereDate('created_at', '<=', $nowDate)
                                        ->where('idadi_kupakia', '!=', '0')
                                        ->orderBy('pacel_id', 'DESC')
                                        ->distinct()
                                        ->get(['pacel_id'])
                                        ->count();
                                        
                                        
            // dd($pacelUnique);
            
            // $farmer_total = Farmer::where('user_id', $usr->id)->where('added_by', $usr->added_by)->count();
            
            $xyz = $pacelUnique/50;
            
            // $xyz = $farmer_total/2;
            
            

            
               
            $farmers = ceil($xyz);
            
            // dd($farmers);
     
            

            $response=['success'=>true,'error'=>false,'message'=>'successfully','manifest_pacel_packet_length'=>$farmers];
            return response()->json($response,200);
        
        
        
    }
    
    
    public function manifest_pacels_limit(int $id , String  $startDate, String  $endDate)
    {
        
        if($endDate == "0000-00-00"){

            $nowDate = Carbon::now()->format('Y-m-d');
            
            }
            else{
                $nowDate = $endDate;
            }

            
            
            $pacels = PacelHistory::where('car_id', $id)
                                    ->whereDate('created_at', '>=', $startDate)
                                    ->whereDate('created_at', '<=', $nowDate)
                                    ->get();
            
            $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->take(10)
                                            ->get(['pacel_id']);
            
             if($pacels->isEmpty()){
    
                $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
                return response()->json($response,200);
       
            }
            else{
        

            foreach($pacelUnique as $it){
                
                
                
                $customerPacellll11 = CustomerPacel::where('id', $it->pacel_id)->first();
                
                

                $delivery = $customerPacellll11->delivery;

                $mteja = $customerPacellll11->mteja;

                $mpokeaji = $customerPacellll11->mpokeaji;

                $name = $customerPacellll11->name;



                $idadi = $customerPacellll11->idadi;

                $idadi_stoo = $customerPacellll11->idadi_stoo;


                $created_at = $customerPacellll11->created_at;

                $mzigo_unapotoka = $customerPacellll11->mzigo_unapotoka;

                $mzigo_unapokwenda = $customerPacellll11->mzigo_unapokwenda;
               
                     $carP = Car::find($id);

                     if(!empty($carP)){
     
                         $data['car_id'] = $carP->id;
         
                         $data['carNumber'] = $carP->carNumber;
                     }
                     else{
     
                         $data['car_id'] = null;
         
                         $data['carNumber'] = null;
                     }

                    $data['id'] = intval($it->pacel_id);
                    $data['mteja'] = $mteja;
                    $data['mpokeaji'] = $mpokeaji;
                    $data['name'] = $name;


                    $driver_id = DriverRoute::where('car_id', $id)->whereDate('start_date', '>=', $startDate)->whereDate('closeDate', '<=', $nowDate)->value('driver_id');

                    $data['driver'] = Driver::where('id', $driver_id)->value('name');

                    $data['mzigo_unapotoka'] = $mzigo_unapotoka;
                    $data['mzigo_unapokwenda'] = $mzigo_unapokwenda;



                    $data['delivery'] = $delivery;

                    $data['idadi'] = $idadi;
    
    
                    $data['idadi_stoo'] = $idadi_stoo;


                    $data['created_at'] = $created_at;
                    
                    $vivid_new =  PacelHistory::where('pacel_id',$it->pacel_id)->where('car_id', $id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $nowDate)->where('activity', 'kupakia')->latest()->first();
                    
                    $data['pacel_hist_id_new'] = $vivid_new->id;

                    $history = [];

                 $histories = PacelHistory::where('pacel_id',$it->pacel_id)->where('car_id', $id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $nowDate)->where('activity', 'kupakia')->get();

                 foreach($histories as $row){

                    // if( $row->activity == "kupakia"){

                        $data2['idadi_kupakia'] = $row->idadi_kupakia;
                        $data2['idadi_shusha'] = $row->idadi_shusha;
                        $data2['hashtag'] = $row->hashtag;
                        $data2['bei'] = $row->bei;
                        $data2['ela_iliyopokelewa'] = $row->ela_iliyopokelewa;
                        $data2['jumla'] = $row->jumla;
                        $data2['activity'] = $row->activity;
                        
                        //  $data['user__id'] = $row->added_by;

                    
            
                        // $user = User::where('id', $row->added_by)->first();
                       $user = User::where('id', $row->added_by)->first();
                        if(!empty($user)){
                            $data['mwandishi'] = $user->name; 
                        }
                        else{
                             $data['mwandishi'] = "kafutwa!!!";
                        }
            
                        
                        // $created_at = CustomerPacel::where('id', $row->pacel_id)->value('created_at');

                        $data2['created_at'] = $row->created_at;

                        $data2['pacel_hist_id'] = $row->id;

                        
                        $history[] = $data2;


                    // }

                        

                 }

                 $data['history'] = $history;


                 $items[] = $data;


            }
    
                $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$items];
                return response()->json($response,200);
                
            } 
            
    }
    
    
    public function manifest_pacels_last_limit(int $id , String  $startDate, String  $endDate, int $lastId)
    {
        
        if($endDate == "0000-00-00"){

            $nowDate = Carbon::now()->format('Y-m-d');
            
            }
            else{
                $nowDate = $endDate;
            }

            
            
            $pacels = PacelHistory::where('car_id', $id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $nowDate)
            ->get();
            
            $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->where('id', '<' ,$lastId)
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->take(10)
                                            ->get(['pacel_id']);
            
             if($pacels->isEmpty()){
    
                $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
                return response()->json($response,200);
       
            }
            else{
                
                if($pacelUnique->isNotEmpty()){
                    
                    foreach($pacelUnique as $it){
                
                
                        
                        $customerPacellll11 = CustomerPacel::where('id', $it->pacel_id)->first();
                        
                        
        
                        $delivery = $customerPacellll11->delivery;
        
                        $mteja = $customerPacellll11->mteja;
        
                        $mpokeaji = $customerPacellll11->mpokeaji;
        
                        $name = $customerPacellll11->name;
        
        
        
                        $idadi = $customerPacellll11->idadi;
        
                        $idadi_stoo = $customerPacellll11->idadi_stoo;
        
        
                        $created_at = $customerPacellll11->created_at;
        
                        $mzigo_unapotoka = $customerPacellll11->mzigo_unapotoka;
        
                        $mzigo_unapokwenda = $customerPacellll11->mzigo_unapokwenda;
                       
                             $carP = Car::find($id);
        
                             if(!empty($carP)){
             
                                 $data['car_id'] = $carP->id;
                 
                                 $data['carNumber'] = $carP->carNumber;
                             }
                             else{
             
                                 $data['car_id'] = null;
                 
                                 $data['carNumber'] = null;
                             }
        
                            $data['id'] = intval($it->pacel_id);
                            $data['mteja'] = $mteja;
                            $data['mpokeaji'] = $mpokeaji;
                            $data['name'] = $name;
        
        
                            $driver_id = DriverRoute::where('car_id', $id)->whereDate('start_date', '>=', $startDate)->whereDate('closeDate', '<=', $nowDate)->value('driver_id');
        
                            $data['driver'] = Driver::where('id', $driver_id)->value('name');
        
                            $data['mzigo_unapotoka'] = $mzigo_unapotoka;
                            $data['mzigo_unapokwenda'] = $mzigo_unapokwenda;
        
        
        
                            $data['delivery'] = $delivery;
        
                            $data['idadi'] = $idadi;
            
            
                            $data['idadi_stoo'] = $idadi_stoo;
        
        
                            $data['created_at'] = $created_at;
                            
                            $vivid_new =  PacelHistory::where('pacel_id',$it->pacel_id)->where('car_id', $id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $nowDate)->where('activity', 'kupakia')->latest()->first();
                    
                            $data['pacel_hist_id_new'] = $vivid_new->id;
        
                            $history = [];
        
                         $histories = PacelHistory::where('pacel_id',$it->pacel_id)->where('car_id', $id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $nowDate)->where('activity', 'kupakia')->get();
        
                         foreach($histories as $row){
        
                            // if( $row->activity == "kupakia"){
        
                                $data2['idadi_kupakia'] = $row->idadi_kupakia;
                                $data2['idadi_shusha'] = $row->idadi_shusha;
                                $data2['hashtag'] = $row->hashtag;
                                $data2['bei'] = $row->bei;
                                $data2['ela_iliyopokelewa'] = $row->ela_iliyopokelewa;
                                $data2['jumla'] = $row->jumla;
                                $data2['activity'] = $row->activity;
                                
                                //  $data['user__id'] = $row->added_by;
        
                            
                    
                                // $user = User::where('id', $row->added_by)->first();
                               $user = User::where('id', $row->added_by)->first();
                                if(!empty($user)){
                                    $data['mwandishi'] = $user->name; 
                                }
                                else{
                                     $data['mwandishi'] = "kafutwa!!!";
                                }
                    
                                
                                // $created_at = CustomerPacel::where('id', $row->pacel_id)->value('created_at');
        
                                $data2['created_at'] = $row->created_at;
        
                                $data2['pacel_hist_id'] = $row->id;
        
                                
                                $history[] = $data2;
        
        
                            // }
        
                                
        
                         }
        
                         $data['history'] = $history;
        
        
                         $items[] = $data;
        
        
                    }
            
                        $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$items];
                        return response()->json($response,200);
                    
                }
                else{
                    
                    $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
                    return response()->json($response,200);
                    
                }
        

                
                
            } 
            
    }
    
    
    //returns mizigo iliyopakiwa kwenye gari according to date
    public function packing_pacel_new_by_length(int $id , String  $startDate, String  $endDate, int $packet_length){
        
            
            
            if($endDate == "0000-00-00"){
                // Default: include today and the previous two days
                $startDate = Carbon::now()->subDays(3)->toDateString();
                $nowDate = Carbon::now()->toDateString();
            }
            else{
                $nowDate = $endDate;
            }

            
            
            $pacels = PacelHistory::where('car_id', $id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $nowDate)
            ->get();
            
            if($packet_length == 1){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 2){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(50)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 3){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(100)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 4){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(150)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 5){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(200)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 6){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(250)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 7){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(300)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 8){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(350)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 9){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(400)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 10){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(450)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 11){
                
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(500)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 12){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(550)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 13){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(600)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            elseif($packet_length == 14){
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->skip(650)
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
            else{
                
                $pacelUnique =  PacelHistory::where('car_id', $id)
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $nowDate)
                                            ->where('idadi_kupakia', '!=', '0')
                                            ->orderBy('pacel_id', 'DESC')
                                            ->distinct()
                                            ->take(50)
                                            ->get(['pacel_id']);
                
            }
     

            

            
    
            if($pacels->isEmpty()){
    
                $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
                return response()->json($response,200);
    
            //    
            }
            else{
        

            foreach($pacelUnique as $it){

                $delivery = CustomerPacel::where('id', $it->pacel_id)->value('delivery');

                $mteja = CustomerPacel::where('id', $it->pacel_id)->value('mteja');

                $mpokeaji = CustomerPacel::where('id', $it->pacel_id)->value('mpokeaji');

                $name = CustomerPacel::where('id', $it->pacel_id)->value('name');



                $idadi = CustomerPacel::where('id', $it->pacel_id)->value('idadi');

                $idadi_stoo = CustomerPacel::where('id', $it->pacel_id)->value('idadi_stoo');


                $created_at = CustomerPacel::where('id', $it->pacel_id)->value('created_at');

                $mzigo_unapotoka = CustomerPacel::where('id', $it->pacel_id)->value('mzigo_unapotoka');

                $mzigo_unapokwenda = CustomerPacel::where('id', $it->pacel_id)->value('mzigo_unapokwenda');
               
                     $carP = Car::find($id);

                     if(!empty($carP)){
     
                         $data['car_id'] = $carP->id;
         
                         $data['carNumber'] = $carP->carNumber;
                     }
                     else{
     
                         $data['car_id'] = null;
         
                         $data['carNumber'] = null;
                     }

                    $data['id'] = intval($it->pacel_id);
                    $data['mteja'] = $mteja;
                    $data['mpokeaji'] = $mpokeaji;
                    $data['name'] = $name;


                    $driver_id = DriverRoute::where('car_id', $id)->whereDate('start_date', '>=', $startDate)->whereDate('closeDate', '<=', $nowDate)->value('driver_id');

                    $data['driver'] = Driver::where('id', $driver_id)->value('name');

                    $data['mzigo_unapotoka'] = $mzigo_unapotoka;
                    $data['mzigo_unapokwenda'] = $mzigo_unapokwenda;



                    $data['delivery'] = $delivery;

                    $data['idadi'] = $idadi;
    
    
                    $data['idadi_stoo'] = $idadi_stoo;


                    $data['created_at'] = $created_at;

                    $history = [];

                 $histories = PacelHistory::where('pacel_id',$it->pacel_id)->where('car_id', $id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $nowDate)->where('activity', 'kupakia')->get();

                 foreach($histories as $row){

                    // if( $row->activity == "kupakia"){

                        $data2['idadi_kupakia'] = $row->idadi_kupakia;
                        $data2['idadi_shusha'] = $row->idadi_shusha;
                        $data2['hashtag'] = $row->hashtag;
                        $data2['bei'] = $row->bei;
                        $data2['ela_iliyopokelewa'] = $row->ela_iliyopokelewa;
                        $data2['jumla'] = $row->jumla;
                        $data2['activity'] = $row->activity;
                        
                        //  $data['user__id'] = $row->added_by;

                    
            
                        // $user = User::where('id', $row->added_by)->first();
                       $user = User::where('id', $row->added_by)->first();
                        if(!empty($user)){
                            $data['mwandishi'] = $user->name; 
                        }
                        else{
                             $data['mwandishi'] = "kafutwa!!!";
                        }
            
                        
                        // $created_at = CustomerPacel::where('id', $row->pacel_id)->value('created_at');

                        $data2['created_at'] = $row->created_at;

                        $data2['pacel_hist_id'] = $row->id;

                        
                        $history[] = $data2;


                    // }

                        

                 }

                 $data['history'] = $history;


                 $items[] = $data;


            }
    
                $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$items];
                return response()->json($response,200);
                
            } 
       
        
       

    }
    
    public function packing_pacel_new_22(int $id , String  $startDate, String  $endDate, String $key, String $word){
        
        

        if($key == "Manifest"){
            
            
            if($endDate == "0000-00-00"){
                // Default: include today, yesterday, and the day before yesterday (date-only)
                $startDate = Carbon::now()->subDays(2)->toDateString();
                $nowDate = Carbon::now()->toDateString();
            }
            else{
                // Use explicit dates as date-only strings
                $startDate = Carbon::parse($startDate)->toDateString();
                $nowDate = Carbon::parse($endDate)->toDateString();
            }

            
            
            // $pacels = PacelHistory::where('car_id', $id)
            // ->whereDate('created_at', '>=', $startDate)
            // ->whereDate('created_at', '<=', $nowDate)
            // ->where('activity', 'kupakia')
            // ->where('idadi_kupakia', '!=', '0')
            // ->where('mteja', 'like', "%{$word}%")
            // ->orwhere('name', 'like', "%{$word}%")
            // ->orwhere('mzigo_unapokwenda', 'like', "%{$word}%")
            // ->orwhere('mzigo_unapotoka', 'like', "%{$word}%")
            // ->orderBy('pacel_id', 'DESC')
            // ->get();
            
            // ->orderBy('pacel_id', 'DESC')
            // ->groupBy('added_by', 'pacel_id');
            // ->groupBy(function($item) {
            //     return $item->added_by;
            //  });
            
            // No +/- 1 day expansion; using startOfDay/endOfDay already covers full dates
            
            $pacels = PacelHistory::where('car_id', $id)
                            ->whereDate('created_at', '>=', $startDate)
                            ->whereDate('created_at', '<=', $nowDate)
                            ->where(function($query) use ($word) {
                                $query->whereRaw('LOWER(mteja) LIKE ?', ['%'.strtolower($word).'%'])
                                      ->orWhereRaw('SOUNDEX(mteja) = SOUNDEX(?)', [$word]);
                            })
                            ->where('activity', 'kupakia')
                            ->where('idadi_kupakia', '!=', '0')
                            ->get();
            
            
            // 
            //->whereDate('created_at',$startDate)

            // $pacelUnique =  PacelHistory::where('car_id', $id)
            // ->whereDate('created_at', '>=', $startDate)
            // ->whereDate('created_at', '<=', $nowDate)
            // ->where('activity', 'kupakia')
            // ->where('idadi_kupakia', '!=', '0')
            // ->where('mteja', 'like', "%{$word}%")
            // ->orwhere('name', 'like', "%{$word}%")
            // ->orwhere('mzigo_unapokwenda', 'like', "%{$word}%")
            // ->orwhere('mzigo_unapotoka', 'like', "%{$word}%")
            // ->orderBy('pacel_id', 'DESC')
            // ->distinct()
            // ->get(['pacel_id']);
            
            // $pacelUnique =  PacelHistory::where('car_id', $id)
            // ->whereBetween('created_at', [$startDate, $nowDate])
            // ->where('activity', 'kupakia')
            // ->where('idadi_kupakia', '!=', '0')
            // ->where(function($query) use ($word) {
            //     $query->where('mteja', 'like', "%{$word}%")
            //           ->orWhere('name', 'like', "%{$word}%")
            //           ->orWhere('mzigo_unapokwenda', 'like', "%{$word}%")
            //           ->orWhere('mzigo_unapotoka', 'like', "%{$word}%");
            // })
            // ->orderBy('pacel_id', 'DESC')
            // ->distinct()
            // ->get(['pacel_id']);
            
            $pacelUnique =  PacelHistory::where('car_id', $id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $nowDate)
            ->where('activity', 'kupakia')
            ->where('idadi_kupakia', '!=', '0')
            ->where(function($query) use ($word) {
                $query->whereRaw('LOWER(mteja) LIKE ?', ['%'.strtolower($word).'%'])
                      ->orWhereRaw('SOUNDEX(mteja) = SOUNDEX(?)', [$word]);
            })
            ->orderBy('pacel_id', 'DESC')
            ->distinct()
            ->get(['pacel_id']);

            // dd($pacels);  ->take(50)

            
    
            if($pacels->isEmpty()){
    
                $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
                return response()->json($response,200);
    
            //    
            }
            else{
        

            foreach($pacelUnique as $it){

                $delivery = CustomerPacel::where('id', $it->pacel_id)->value('delivery');

                $mteja = CustomerPacel::where('id', $it->pacel_id)->value('mteja');

                $mpokeaji = CustomerPacel::where('id', $it->pacel_id)->value('mpokeaji');

                $name = CustomerPacel::where('id', $it->pacel_id)->value('name');



                $idadi = CustomerPacel::where('id', $it->pacel_id)->value('idadi');

                $idadi_stoo = CustomerPacel::where('id', $it->pacel_id)->value('idadi_stoo');


                $created_at = CustomerPacel::where('id', $it->pacel_id)->value('created_at');

                $mzigo_unapotoka = CustomerPacel::where('id', $it->pacel_id)->value('mzigo_unapotoka');

                $mzigo_unapokwenda = CustomerPacel::where('id', $it->pacel_id)->value('mzigo_unapokwenda');
               
                     $carP = Car::find($id);

                     if(!empty($carP)){
     
                         $data['car_id'] = $carP->id;
         
                         $data['carNumber'] = $carP->carNumber;
                     }
                     else{
     
                         $data['car_id'] = null;
         
                         $data['carNumber'] = null;
                     }

                    $data['id'] = intval($it->pacel_id);
                    $data['mteja'] = $mteja;
                    $data['mpokeaji'] = $mpokeaji;
                    $data['name'] = $name;


                    $driver_id = DriverRoute::where('car_id', $id)->whereDate('start_date', '>=', $startDate)->whereDate('closeDate', '<=', $nowDate)->value('driver_id');

                    $data['driver'] = Driver::where('id', $driver_id)->value('name');

                    $data['mzigo_unapotoka'] = $mzigo_unapotoka;
                    $data['mzigo_unapokwenda'] = $mzigo_unapokwenda;



                    $data['delivery'] = $delivery;

                    $data['idadi'] = $idadi;
    
    
                    $data['idadi_stoo'] = $idadi_stoo;


                    $data['created_at'] = $created_at;

                    $history = [];

                 $histories = PacelHistory::where('pacel_id',$it->pacel_id)->where('car_id', $id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $nowDate)->where('activity', 'kupakia')->get();

                 foreach($histories as $row){

                    // if( $row->activity == "kupakia"){

                        $data2['idadi_kupakia'] = $row->idadi_kupakia;
                        $data2['idadi_shusha'] = $row->idadi_shusha;
                        $data2['hashtag'] = $row->hashtag;
                        $data2['bei'] = $row->bei;
                        $data2['ela_iliyopokelewa'] = $row->ela_iliyopokelewa;
                        $data2['jumla'] = $row->jumla;
                        $data2['activity'] = $row->activity;
                        
                        //  $data['user__id'] = $row->added_by;

                    
            
                        // $user = User::where('id', $row->added_by)->first();
                       $user = User::where('id', $row->added_by)->first();
                        if(!empty($user)){
                            $data['mwandishi'] = $user->name; 
                        }
                        else{
                             $data['mwandishi'] = "kafutwa!!!";
                        }
            
                        
                        // $created_at = CustomerPacel::where('id', $row->pacel_id)->value('created_at');

                        $data2['created_at'] = $row->created_at;

                        $data2['pacel_hist_id'] = $row->id;

                        
                        $history[] = $data2;


                    // }

                        

                 }

                 $data['history'] = $history;


                 $items[] = $data;


            }
    
                $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$items];
                return response()->json($response,200);
                
            } 
        }
        else{
            
            if($endDate == "0000-00-00"){

                $nowDate = Carbon::now()->format('Y-m-d');
            
            }
            else{
                $nowDate = $endDate;
            }
            
            
            // Subtract one day from start date
            if ($startDate) {
                $startDate = Carbon::parse($startDate)->subDay()->toDateString();
            }
    
            // Add one day to end date
            if ($nowDate) {
                $nowDate = Carbon::parse($nowDate)->addDay()->toDateString();
            }
    
            
            // ->whereDate('created_at', $date)

            $pacels = PacelHistory::where('car_id', $id)
            ->whereBetween('created_at', [$startDate, $nowDate])
            ->where('activity', 'kupakia')
            ->where('idadi_kupakia', '!=', '0')
            ->where(function($query) use ($word) {
                $query->where('mteja', 'like', "%{$word}%")
                      ->orWhere('name', 'like', "%{$word}%")
                      ->orWhere('mzigo_unapokwenda', 'like', "%{$word}%")
                      ->orWhere('mzigo_unapotoka', 'like', "%{$word}%");
            })
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($item) {
                return $item->added_by;
            });
            
            // dd($pacels);



            if ($pacels->isEmpty()) {
                $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
                return response()->json($response, 200);
            } else {
                foreach ($pacels as $key => $pacel) {
                    // $data = $pacel;


                    $data['user_id'] = $key;

                    foreach ($pacel as $row) {
                        $data = $row;

                        $carP = Car::find($row->car_id);

                        if(!empty($carP)){

                            $data['car_id'] = intval($carP->id);
            
                            $data['carNumber'] = $carP->carNumber;
                        }
                        else{

                            $data['car_id'] = null;
            
                            $data['carNumber'] = null;
                        }

                        $data['user_id'] = $key;

                        $data['mteja'] = $row->mteja;
                        $data['id'] = $row->pacel_id;
                        $data['delivery'] = $row->delivery;


                        $data['mpokeaji'] = $row->mpokeaji;
                        $data['hashtag'] = $row->hashtag;

                        $user = User::where('id', $row->added_by)->first();
                        if(!empty($user)){
                            $data['mwandishi'] = $user->name; 
                        }
                        else{
                             $data['mwandishi'] = "Kafutwa!!!";
                        }
                        // $data['mwandishi'] = $user->name;
                        // $data['activity'] = $row->activity;
                        $idadi = CustomerPacel::where('id', $row->pacel_id)->value('idadi');
                        $data['idadi'] = $idadi;

                        $idadi_stoo = CustomerPacel::where('id', $row->pacel_id)->value('idadi_stoo');

                        $data['idadi_stoo'] = $idadi_stoo;



                        // $data['idadi_kupakia'] =  PacelHistory::where('car_id', $id)->whereDate('created_at',$date)->where('pacel_id', $row->pacel_id)->where('added_by', $row->added_by)->sum('idadi_kupakia');


                        // $data['idadi_shusha'] = PacelHistory::where('car_id', $id)->whereDate('created_at',$date)->where('pacel_id', $row->pacel_id)->where('added_by', $row->added_by)->sum('idadi_shusha');


                        $data['idadi_kupakia'] =  $row->idadi_kupakia;


                        $data['idadi_shusha'] = $row->idadi_shusha;

                     

                        $userPacels[]=$data;
                    }

                    $staffData["mwandishi"]=$key;
                    $staffData["pacels"]=$userPacels;
                    $farmers[] = $staffData;
                }

                $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$farmers];
                return response()->json($response, 200);
    }

        }
       

    }
    
    //returns mizigo iliyopakiwa kwenye gari according to date
    public function packing_pacel_new(int $id , String  $startDate, String  $endDate, String $key, Request $request){
        
        // Get company_id from user_id query parameter
        $companyId = $this->getCompanyId($request);
        
        if($key == "Manifest"){
            
            
            if($endDate == "0000-00-00"){

            $nowDate = Carbon::now()->format('Y-m-d');
            
            }
            else{
                $nowDate = $endDate;
            }

            
            
            // Filter by company_id
            $pacelsQuery = PacelHistory::where('car_id', $id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $nowDate);
            
            if ($companyId) {
                $pacelsQuery->where('company_id', $companyId);
            }
            
            $pacels = $pacelsQuery->get();
            // ->orderBy('pacel_id', 'DESC')
            // ->groupBy('added_by', 'pacel_id');
            // ->groupBy(function($item) {
            //     return $item->added_by;
            //  });
            
            // 
            //->whereDate('created_at',$startDate)

            // Filter by company_id
            $pacelUniqueQuery = PacelHistory::where('car_id', $id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $nowDate)
            ->where('idadi_kupakia', '!=', '0');
            
            if ($companyId) {
                $pacelUniqueQuery->where('company_id', $companyId);
            }
            
            $pacelUnique = $pacelUniqueQuery->orderBy('pacel_id', 'DESC')
            ->distinct()
            ->take(30)
            ->get(['pacel_id']);

            // dd($pacels);  ->take(50)  ->take(100)

            
    
            if($pacels->isEmpty()){
    
                $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
                return response()->json($response,200);
    
            //    
            }
            else{
        

            foreach($pacelUnique as $it){

                $delivery = CustomerPacel::where('id', $it->pacel_id)->when($companyId, function($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })->value('delivery');

                $mteja = CustomerPacel::where('id', $it->pacel_id)->when($companyId, function($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })->value('mteja');

                $mpokeaji = CustomerPacel::where('id', $it->pacel_id)->when($companyId, function($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })->value('mpokeaji');

                $name = CustomerPacel::where('id', $it->pacel_id)->when($companyId, function($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })->value('name');



                $idadi = CustomerPacel::where('id', $it->pacel_id)->when($companyId, function($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })->value('idadi');

                $idadi_stoo = CustomerPacel::where('id', $it->pacel_id)->when($companyId, function($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })->value('idadi_stoo');


                $created_at = CustomerPacel::where('id', $it->pacel_id)->when($companyId, function($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })->value('created_at');

                $mzigo_unapotoka = CustomerPacel::where('id', $it->pacel_id)->when($companyId, function($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })->value('mzigo_unapotoka');

                $mzigo_unapokwenda = CustomerPacel::where('id', $it->pacel_id)->when($companyId, function($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })->value('mzigo_unapokwenda');
               
                     $carP = Car::find($id);

                     if(!empty($carP)){
     
                         $data['car_id'] = $carP->id;
         
                         $data['carNumber'] = $carP->carNumber;
                     }
                     else{
     
                         $data['car_id'] = null;
         
                         $data['carNumber'] = null;
                     }

                    $data['id'] = intval($it->pacel_id);
                    $data['mteja'] = $mteja;
                    $data['mpokeaji'] = $mpokeaji;
                    $data['name'] = $name;


                    $driverRouteQuery = DriverRoute::where('car_id', $id)->whereDate('start_date', '>=', $startDate)->whereDate('closeDate', '<=', $nowDate);
                    if ($companyId) {
                        $driverRouteQuery->where('company_id', $companyId);
                    }
                    $driver_id = $driverRouteQuery->value('driver_id');

                    $data['driver'] = Driver::where('id', $driver_id)->value('name');

                    $data['mzigo_unapotoka'] = $mzigo_unapotoka;
                    $data['mzigo_unapokwenda'] = $mzigo_unapokwenda;



                    $data['delivery'] = $delivery;

                    $data['idadi'] = $idadi;
    
    
                    $data['idadi_stoo'] = $idadi_stoo;


                    $data['created_at'] = $created_at;

                    $history = [];

                 $historiesQuery = PacelHistory::where('pacel_id',$it->pacel_id)->where('car_id', $id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $nowDate)->where('activity', 'kupakia');
                 if ($companyId) {
                     $historiesQuery->where('company_id', $companyId);
                 }
                 $histories = $historiesQuery->get();

                 foreach($histories as $row){

                    // if( $row->activity == "kupakia"){

                        $data2['idadi_kupakia'] = $row->idadi_kupakia;
                        $data2['idadi_shusha'] = $row->idadi_shusha;
                        $data2['hashtag'] = $row->hashtag;
                        $data2['bei'] = $row->bei;
                        $data2['ela_iliyopokelewa'] = $row->ela_iliyopokelewa;
                        $data2['jumla'] = $row->jumla;
                        $data2['activity'] = $row->activity;
                        
                        //  $data['user__id'] = $row->added_by;

                    
            
                        // $user = User::where('id', $row->added_by)->first();
                       $user = User::where('id', $row->added_by)->first();
                        if(!empty($user)){
                            $data['mwandishi'] = $user->name; 
                        }
                        else{
                             $data['mwandishi'] = "kafutwa!!!";
                        }
            
                        
                        // $created_at = CustomerPacel::where('id', $row->pacel_id)->value('created_at');

                        $data2['created_at'] = $row->created_at;

                        $data2['pacel_hist_id'] = $row->id;

                        
                        $history[] = $data2;


                    // }

                        

                 }

                 $data['history'] = $history;


                 $items[] = $data;


            }
    
                $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$items];
                return response()->json($response,200);
                
            } 
        }
        else{
            
            if($endDate == "0000-00-00"){

                $nowDate = Carbon::now()->format('Y-m-d');
            
            }
            else{
                $nowDate = $endDate;
            }
    
            
            // ->whereDate('created_at', $date)

            // Filter by company_id
            $pacelsQuery = PacelHistory::where('car_id', $id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $nowDate);
            
            if ($companyId) {
                $pacelsQuery->where('company_id', $companyId);
            }
            
            $pacels = $pacelsQuery->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($item) {
                return $item->added_by;
            });
            
            // dd($pacels);



            if ($pacels->isEmpty()) {
                $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
                return response()->json($response, 200);
            } else {
                foreach ($pacels as $key => $pacel) {
                    // $data = $pacel;


                    $data['user_id'] = $key;

                    foreach ($pacel as $row) {
                        $data = $row;

                        $carP = Car::find($row->car_id);

                        if(!empty($carP)){

                            $data['car_id'] = intval($carP->id);
            
                            $data['carNumber'] = $carP->carNumber;
                        }
                        else{

                            $data['car_id'] = null;
            
                            $data['carNumber'] = null;
                        }

                        $data['user_id'] = $key;

                        $data['mteja'] = $row->mteja;
                        $data['id'] = $row->pacel_id;
                        $data['delivery'] = $row->delivery;


                        $data['mpokeaji'] = $row->mpokeaji;
                        $data['hashtag'] = $row->hashtag;

                        $user = User::where('id', $row->added_by)->first();
                        if(!empty($user)){
                            $data['mwandishi'] = $user->name; 
                        }
                        else{
                             $data['mwandishi'] = "Kafutwa!!!";
                        }
                        // $data['mwandishi'] = $user->name;
                        // $data['activity'] = $row->activity;
                        $idadi = CustomerPacel::where('id', $row->pacel_id)->when($companyId, function($q) use ($companyId) {
                            return $q->where('company_id', $companyId);
                        })->value('idadi');
                        $data['idadi'] = $idadi;

                        $idadi_stoo = CustomerPacel::where('id', $row->pacel_id)->when($companyId, function($q) use ($companyId) {
                            return $q->where('company_id', $companyId);
                        })->value('idadi_stoo');

                        $data['idadi_stoo'] = $idadi_stoo;



                        // $data['idadi_kupakia'] =  PacelHistory::where('car_id', $id)->whereDate('created_at',$date)->where('pacel_id', $row->pacel_id)->where('added_by', $row->added_by)->sum('idadi_kupakia');


                        // $data['idadi_shusha'] = PacelHistory::where('car_id', $id)->whereDate('created_at',$date)->where('pacel_id', $row->pacel_id)->where('added_by', $row->added_by)->sum('idadi_shusha');


                        $data['idadi_kupakia'] =  $row->idadi_kupakia;


                        $data['idadi_shusha'] = $row->idadi_shusha;

                     

                        $userPacels[]=$data;
                    }

                    $staffData["mwandishi"]=$key;
                    $staffData["pacels"]=$userPacels;
                    $farmers[] = $staffData;
                }

                $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$farmers];
                return response()->json($response, 200);
    }

        }
       

    }
    
    
    

    //returns mizigo iliyopakiwa kwenye gari according to date
    public function packing_pacel(int $id , String  $date, String $key){

        if($key == "Manifest"){

            
            
            $pacels = PacelHistory::where('car_id', $id)->whereDate('created_at',$date)->get();
            // ->orderBy('pacel_id', 'DESC')
            // ->groupBy('added_by', 'pacel_id');
            // ->groupBy(function($item) {
            //     return $item->added_by;
            //  });

            $pacelUnique =  PacelHistory::where('car_id', $id)
            ->whereDate('created_at',$date)
            ->where('idadi_kupakia', '!=', '0')
            ->orderBy('pacel_id', 'DESC')
            ->distinct()
            ->get(['pacel_id']);

            // dd($pacels);

            
    
            if($pacels->isEmpty()){
    
                $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
                return response()->json($response,200);
    
            //    
            }
            else{
        

            foreach($pacelUnique as $it){

                $delivery = CustomerPacel::where('id', $it->pacel_id)->value('delivery');

                $mteja = CustomerPacel::where('id', $it->pacel_id)->value('mteja');

                $mpokeaji = CustomerPacel::where('id', $it->pacel_id)->value('mpokeaji');

                $name = CustomerPacel::where('id', $it->pacel_id)->value('name');



                $idadi = CustomerPacel::where('id', $it->pacel_id)->value('idadi');

                $idadi_stoo = CustomerPacel::where('id', $it->pacel_id)->value('idadi_stoo');


                $created_at = CustomerPacel::where('id', $it->pacel_id)->value('created_at');

                $mzigo_unapotoka = CustomerPacel::where('id', $it->pacel_id)->value('mzigo_unapotoka');

                $mzigo_unapokwenda = CustomerPacel::where('id', $it->pacel_id)->value('mzigo_unapokwenda');
               
                     $carP = Car::find($id);

                     if(!empty($carP)){
     
                         $data['car_id'] = $carP->id;
         
                         $data['carNumber'] = $carP->carNumber;
                     }
                     else{
     
                         $data['car_id'] = null;
         
                         $data['carNumber'] = null;
                     }

                    $data['id'] = intval($it->pacel_id);
                    $data['mteja'] = $mteja;
                    $data['mpokeaji'] = $mpokeaji;
                    $data['name'] = $name;


                    $driver_id = DriverRoute::where('car_id', $id)->whereDate('created_at', $date)->value('driver_id');

                    $data['driver'] = Driver::where('id', $driver_id)->value('name');

                    $data['mzigo_unapotoka'] = $mzigo_unapotoka;
                    $data['mzigo_unapokwenda'] = $mzigo_unapokwenda;



                    $data['delivery'] = $delivery;

                    $data['idadi'] = $idadi;
    
    
                    $data['idadi_stoo'] = $idadi_stoo;


                    $data['created_at'] = $created_at;

                    $history = [];

                 $histories = PacelHistory::where('pacel_id',$it->pacel_id)->where('car_id', $id)->whereDate('created_at',$date)->where('activity', 'kupakia')->get();

                 foreach($histories as $row){

                    // if( $row->activity == "kupakia"){

                        $data2['idadi_kupakia'] = $row->idadi_kupakia;
                        $data2['idadi_shusha'] = $row->idadi_shusha;
                        $data2['hashtag'] = $row->hashtag;
                        $data2['bei'] = $row->bei;
                        $data2['ela_iliyopokelewa'] = $row->ela_iliyopokelewa;
                        $data2['jumla'] = $row->jumla;
                        $data2['activity'] = $row->activity;

                    
            
                        $user = User::where('id', $row->added_by)->first();
                        $data2['mwandishi'] = $user->name;
            
                        
                        // $created_at = CustomerPacel::where('id', $row->pacel_id)->value('created_at');

                        $data2['created_at'] = $row->created_at;

                        $data2['pacel_hist_id'] = $row->id;

                        
                        $history[] = $data2;


                    // }

                        

                 }

                 $data['history'] = $history;


                 $items[] = $data;


            }
    
                $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$items];
                return response()->json($response,200);
                
            } 
        }
        else{


            $pacels = PacelHistory::where('car_id', $id)->whereDate('created_at', $date)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($item) {
                return $item->added_by;
            });
            
            // dd($pacels);



            if ($pacels->isEmpty()) {
                $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
                return response()->json($response, 200);
            } else {
                foreach ($pacels as $key => $pacel) {
                    // $data = $pacel;


                    $data['user_id'] = $key;

                    foreach ($pacel as $row) {
                        $data = $row;

                        $carP = Car::find($row->car_id);

                        if(!empty($carP)){

                            $data['car_id'] = intval($carP->id);
            
                            $data['carNumber'] = $carP->carNumber;
                        }
                        else{

                            $data['car_id'] = null;
            
                            $data['carNumber'] = null;
                        }

                        $data['user_id'] = $key;

                        $data['mteja'] = $row->mteja;
                        $data['id'] = $row->pacel_id;
                        $data['delivery'] = $row->delivery;


                        $data['mpokeaji'] = $row->mpokeaji;
                        $data['hashtag'] = $row->hashtag;

                        $user = User::where('id', $row->added_by)->first();
                        $data['mwandishi'] = $user->name;
                        // $data['activity'] = $row->activity;
                        $idadi = CustomerPacel::where('id', $row->pacel_id)->value('idadi');
                        $data['idadi'] = $idadi;

                        $idadi_stoo = CustomerPacel::where('id', $row->pacel_id)->value('idadi_stoo');

                        $data['idadi_stoo'] = $idadi_stoo;



                        // $data['idadi_kupakia'] =  PacelHistory::where('car_id', $id)->whereDate('created_at',$date)->where('pacel_id', $row->pacel_id)->where('added_by', $row->added_by)->sum('idadi_kupakia');


                        // $data['idadi_shusha'] = PacelHistory::where('car_id', $id)->whereDate('created_at',$date)->where('pacel_id', $row->pacel_id)->where('added_by', $row->added_by)->sum('idadi_shusha');


                        $data['idadi_kupakia'] =  $row->idadi_kupakia;


                        $data['idadi_shusha'] = $row->idadi_shusha;

                     

                        $userPacels[]=$data;
                    }

                    $staffData["mwandishi"]=$key;
                    $staffData["pacels"]=$userPacels;
                    $farmers[] = $staffData;
                }

                $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$farmers];
                return response()->json($response, 200);
    }

        }
       

    }
    
 
    public function store_pacel(Request $request)
    {
        //

        $this->validate($request,[
            'name'=>'required',
            'mteja'=>'required',
            'mpokeaji'=>'required',
            'idadi'=>'required',
            'id'=>'required',
            
        ]); 
        
        // dd($request->input('mpokeaji'));
        
        $usr = User::find($request->input('id'));
       
       if(!empty($usr)){
           
           
            $added_by = $usr->added_by;
            
            $activity = "kusajiliwa";

            // Get company_id from user
            $companyId = $usr->company_id;
            
            $data['mteja'] = $request->input('mteja');
            $data['mpokeaji'] = $request->input('mpokeaji');
            $data['added_by'] = $request->input('id');
            $data['company_id'] = $companyId; // Set company_id
            $customer = Customer::create($data);
            
            
     

                    $items = array(
                        'mteja' => $request->input('mteja'),
                        'mpokeaji' => $request->input('mpokeaji'),
                        'activity' => $activity,
                        'name' => $request->input('name'),
                        'idadi' => $request->input('idadi'),
                        'idadi_stoo' => $request->input('idadi'),
                        'bei' => $request->input('bei'),
                        'receipt' =>$request->input('receipt'),
                        'mzigo_unapotoka' =>   $request->input('mzigo_unapotoka'),
                        'mzigo_unapokwenda' =>  $request->input('mzigo_unapokwenda'),
                         'jumla' => $request->input('jumla'),
                         'customer_id' => $customer->id,
                           'ela_iliyopokelewa' =>  $request->input('ela_iliyopokelewa'),
                           'added_by' => $request->input('id'),
                           'company_id' => $companyId); // Set company_id
                       
                        $cp   =   CustomerPacel::create($items);

                     
                    PacelHistory::create([
                        'pacel_id' => $cp->id,
                        'mteja' => $request->input('mteja'),
                        'mpokeaji' => $request->input('mpokeaji'),
                        'activity' => $activity,
                        'name' => $request->input('name'),
                        'idadi' => $request->input('idadi'),
                        'idadi_stoo' => $request->input('idadi'),
                        'receipt' =>$request->input('receipt'),
                        'bei' => $request->input('bei'),
                        'mzigo_unapotoka' =>   $request->input('mzigo_unapotoka'),
                        'mzigo_unapokwenda' =>  $request->input('mzigo_unapokwenda'),
                        'jumla' => $request->input('jumla'),
                        'ela_iliyopokelewa' =>  $request->input('ela_iliyopokelewa'),
                        'status' => 'active',
                        'added_by' => $request->input('id'),
                        'company_id' => $companyId // Set company_id
                       
                        ]);  


                        $count = $this->generateUniqueCode();

                        $delivery = $count.$cp->id."-".$request->input('id');

                        // dd($delivery);

                        CustomerPacel::where('id', $cp->id)->update(['delivery' => $delivery]);

                        PacelHistory::where('pacel_id', $cp->id)->update(['delivery' => $delivery]);
    
         
    
         
    
    
            if($cp)
            {

                $response=['success'=>true,'error'=>false, 'message' => 'Pacel  Created successful', 'pacel' => $cp];
                return response()->json($response, 200);
            }
            else
            {
                
                $response=['success'=>false,'error'=>true,'message'=>'Failed to  Create Pacel Successfully'];
                return response()->json($response,200);
            }
        
       }
       else{
                $response=['success'=>false,'error'=>true,'message'=>'No User found by that id'];
                return response()->json($response,200);
       }
    }
    
    
    public function generateUniqueCode()
    {
        do {
            $code = random_int(100000, 999999);
        } while (CustomerPacel::where("delivery", "=", $code)->first());
  
        return $code;
    }



    public function pacel_history(int $id){

        $pacels = PacelHistory::where('pacel_id', $id)->get();

        if($pacels->isEmpty()){

            $response=['success'=>false,'error'=>true,'message'=>'No Pacel in that car found'];
            return response()->json($response,200);

           
        }

        else{

            foreach($pacels as $row){
                $carP = Car::find($row->car_id);

                if(!empty($carP)){

                    $data['car_id'] = $carP->id;
    
                    $data['carNumber'] = $carP->carNumber;
                }
                else{

                    $data['car_id'] = null;
    
                    $data['carNumber'] = null;
                }

                

                $user = User::where('id', $row->added_by)->first();
                $data['mwandishi'] = $user->name;
                $data['activity'] = $row->activity;

                $data['delivery'] = $row->delivery;

                $data['idadi'] = $row->idadi;

                $data['hashtag'] = $row->hashtag;


                $data['bei'] = $row->bei;



                $createdAt = Carbon::parse($row->created_at);

                $data['time'] = $createdAt->toTimeString();

                $data = ParcelStatusLabel::enrichPayload($data, $row, $carP ?? null);

                $farmers[] = $data;

            }

            

            $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$farmers];
            return response()->json($response,200);
        }
    }

    /**
     * Mark a car as arrived (status = 1) for the current company.
     * Inputs:
     * - car_id (required)
     * - user_id (required indirectly, used by getCompanyId to resolve company)
     */
    public function car_arrive(Request $request)
    {
        $request->validate([
            'car_id' => 'required|integer',
        ]);

        $companyId = $this->getCompanyId($request);

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Company not found for this user. Please provide a valid user_id.',
            ], 200);
        }

        $car = Car::where('id', $request->car_id)
            ->where('company_id', $companyId)
            ->first();

        if (!$car) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Car not found for this company',
            ], 200);
        }

        // Set status to 1 = arrived
        $car->status = '1';
        $car->save();

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'Car marked as arrived successfully',
            'car_id' => $car->id,
            'status' => $car->status,
        ], 200);
    }

    /**
     * Mark a car as starting a trip (status = 0, in transit) for the current company.
     * Inputs:
     * - car_id (required)
     * - user_id (required indirectly via getCompanyId)
     */
    public function car_start_trip(Request $request)
    {
        $request->validate([
            'car_id' => 'required|integer',
        ]);

        $companyId = $this->getCompanyId($request);

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Company not found for this user. Please provide a valid user_id.',
            ], 200);
        }

        $car = Car::where('id', $request->car_id)
            ->where('company_id', $companyId)
            ->first();

        if (!$car) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Car not found for this company',
            ], 200);
        }

        // Set status to 0 = in transit / starting trip
        $car->status = '0';
        $car->save();

        // Mark loaded parcels on this car as in transit
        CustomerPacel::where('car_id', $car->id)
            ->where('company_id', $companyId)
            ->where('activity', 'kupakia')
            ->update(['activity' => 'ipo njiani']);

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'Car marked as starting trip successfully',
            'car_id' => $car->id,
            'status' => $car->status,
        ], 200);
    }

    public function delivery_search(String $delivery, Request $request){

        $companyId = $this->getCompanyId($request);
        $query = CustomerPacel::where('delivery', $delivery);
        
        // Filter by company_id if available
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        $pacels = $query->get();
        // $mizigo = $pacels->count();

        if($pacels->isEmpty()){

            $response=['success'=>false,'error'=>true,'message'=>'No Pacels found by that delivery number'];
            return response()->json($response,200);

            
        }
        
        else{

            foreach($pacels as $row){

                
                $data['id'] = $row->id;
                $data['name'] = $row->name;
                $data['idadi'] = $row->idadi;

                $data['idadi_stoo'] = $row->idadi_stoo;


                $data['mteja'] = $row->mteja;

                $data['mpokeaji'] = $row->mpokeaji;

                $data['hashtag'] = $row->hashtag;

                $data['delivery'] = $row->delivery;

                $data['bei'] = $row->bei;




                // $customer = Customer::where('id', $row->pacel_id)->get();

                // foreach($customer as $row2){

                $data['activity'] = $row->activity;

                // }

                $data['mzigo_unapotoka'] = $row->mzigo_unapotoka;
                $data['created_at'] = $row->created_at
                    ? Carbon::parse($row->created_at)->setTimezone('Africa/Nairobi')->toDateTimeString()
                    : null;
                $data['mzigo_unapokwenda'] = $row->mzigo_unapokwenda;
                $data['jumla'] = $row->jumla;
                $data['ela_iliyopokelewa'] = $row->ela_iliyopokelewa;

                $data = ParcelStatusLabel::enrichPayload($data, $row);

                $farmers[] = $data;
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','pacels'=>$farmers];
            return response()->json($response,200);
        }

    }
    
    public function money_recieved_car_new(int $id, String $startDate, String $endDate){
        
        if($endDate == "0000-00-00"){

            $nowDate = Carbon::now()->format('Y-m-d');
            
        }
        else{
            $nowDate = $endDate;
        }
        
        $total = PacelHistory::where('car_id', $id)
                                ->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $nowDate)
                               ->where('activity','kupakia')
                               ->where('idadi_kupakia', '!=', '0')->sum('jumla');
   
        if($total){

            $total_format = number_format($total,2);

            $response=['success'=>true,'error'=>false,'message'=>'successfully','total'=>$total_format];
            return response()->json($response,200);
        }
        else{
            $total_format = number_format('0',2);

            $response=['success'=>true,'error'=>false,'message'=>'No Pacels found by that delivery number', 'total' => $total_format];
            return response()->json($response,200);
        }
    }


    public function money_recieved_car(int $id, String $date){
        $total = PacelHistory::where('car_id', $id)->whereDate('created_at', $date)
                               ->where('activity','kupakia')
                               ->where('idadi_kupakia', '!=', '0')->sum('jumla');
   
        if($total){

            $total_format = number_format($total,2);

            $response=['success'=>true,'error'=>false,'message'=>'successfully','total'=>$total_format];
            return response()->json($response,200);
        }
        else{
            $total_format = number_format('0',2);

            $response=['success'=>true,'error'=>false,'message'=>'No Pacels found by that delivery number', 'total' => $total_format];
            return response()->json($response,200);
        }
    }

    public function arrived_car_date(String $date){

        $carsDate =  DriverRoute::whereDate('created_at',$date)
                                    ->get()
                                    ->unique('car_id');
                                    
                                    
                                    
                                    // ->where('status', '!=', '2')

        if($carsDate->isEmpty()){

            $response=['success'=>false,'error'=>true,'message'=>'No Car found by that date'];
            return response()->json($response,200);
                        
                                    
        }
        else{
            
            foreach($carsDate as $row){

                
                // $data  = $row;

                $car_id = $row->car_id;

                if(empty($car_id)){

                    $data['carNumber'] = 'null';
                    
                }
                else{
                    $data['carNumber'] = Car::where('id', $car_id)->value('carNumber');
                }

                $data['id'] = intval($row->car_id);

                // $data['closeDate'] = $row->driver_id;



                // $data['carNumber'] = $row->carNumber;
                //driver_id
                // $driver =  DriverRoute::where('car_id', $car_id)->where('');


                $driver_id = $row->driver_id;
                if(empty($driver_id)){
                    // $data['driver'] = 'null';

                    $data['driver_id'] = null;

                    $data['driver'] = 'null';

                    $data['from'] = null;

                    $data['to'] = null;

                    $data['start_date'] = null;

                }
                else{
                    $driver =  Driver::find($driver_id);

                    $data['driver_id'] = intval($driver->id);

                    $data['driver'] = $driver->name;

                    // $driverRoute = DriverRoute::where('car_id', $row->car_id)->where('driver_id', $row->driver_id)->orderBy('created_at', 'DESC')->first();

                    // if($row->status == '2'){

                        $data['from'] = $row->from;

                        $data['to'] = $row->to;
                   
                        $data['start_date'] = $row->start_date;

                    

                }


                if( $row->status == '1'){

                $data['status'] = "inasubiri";

                }
                elseif( $row->status == '2'){

                $data['status'] = "inapakiwa";

                }
                elseif( $row->status == '3'){

                    $data['status'] = "imefungwa";
    
                }
                else{

                    $data['status'] = "null";
    
                }




                $farmers[] = $data;
            }

            $response=['success'=>true,'error'=>false,'message'=>'successfully','carsDate'=>$farmers];
            return response()->json($response,200);
        }                          
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
    }

    //kufunga gari baada ya kupakia
    public function car_close(int $id, Request $request){

        // Get company_id from request
        $companyId = $this->getCompanyId($request);

        $today = Carbon::now()->format('Y-m-d');
        
        // First verify the car exists and belongs to the company
        $carQuery = Car::where('id', $id);
        if ($companyId) {
            $carQuery->where('company_id', $companyId);
        }
        $carExists = $carQuery->first();
        
        if (!$carExists) {
            $response = ['success' => false, 'error' => true, 'message' => 'Car not found or does not belong to your company'];
            return response()->json($response, 404);
        }
        
        // Filter by company_id when updating car
        $car = $carQuery->update(['status' => '3', 'closeDate' => $today]);

        // $car_history =  DriverRoute::where('car_id', $id)->latest()->first()->update(['status' => '3', 'closeDate' => $today]);
        $carHistoryQuery = DriverRoute::where('car_id', $id);
        if ($companyId) {
            $carHistoryQuery->where('company_id', $companyId);
        }
        $car_history = $carHistoryQuery->latest()->first();

        if (!empty($car_history)) {
            // Close all matching driver routes for this car/driver/date/from/to
            $carToClosedQuery = DriverRoute::where('car_id', $car_history->car_id)
                ->where('driver_id', $car_history->driver_id)
                ->whereDate('start_date', $car_history->start_date)
                ->where('from', $car_history->from)
                ->where('to', $car_history->to);
            if ($companyId) {
                $carToClosedQuery->where('company_id', $companyId);
            }
            $car_to_closed = $carToClosedQuery->get();

            foreach ($car_to_closed as $row) {
                $row->update(['status' => '3', 'closeDate' => $today]);
            }

            // From driver_routes we have driver_id; mark that driver as available (status = 1)
            if (!empty($car_history->driver_id)) {
                $driverQuery = Driver::where('id', $car_history->driver_id);
                if ($companyId) {
                    $driverQuery->where('company_id', $companyId);
                }
                $driverQuery->update(['status' => 1]);
            }
        }
    // else{

    // }


    // $car_history =  CarHistory::where('id', $request->input('car_id'))->update(['driver_id' => $request->input('driver_id'), 'status' => '2']);


    if($car){

        $response=['success'=>true,'error'=>false,'message'=>'successfully','car'=>$car];
        return response()->json($response,200);

       
    }
    else{
        $response=['success'=>false,'error'=>true,'message'=> 'Car Not closed'];
        return response()->json($response,200);

    }
    }

    public function driver_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'user_id' => 'nullable|integer',
        ]);

        $companyId = $this->getCompanyId($request);
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Company not found. Please log in again (user_id required).',
            ], 200);
        }

        Log::debug('driver_store request', [
            'company_id' => $companyId,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'user_id' => $request->input('user_id'),
        ]);

        try {
            $driver = new Driver();

            do {
                $driverId = str_pad((string) mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (Driver::withoutTenantScope()->where('driver_id', $driverId)->exists());

            $driver->driver_id = $driverId;
            $driver->name = $request->input('name');
            $email = trim((string) $request->input('email', ''));
            $driver->email = $email !== '' ? $email : null;
            $driver->phone = $request->input('phone');
            $driver->status = 1;
            $driver->company_id = $companyId;
            if (\Schema::hasColumn('drivers', 'assigned_date')) {
                $driver->assigned_date = $driver->assigned_date ?: now()->format('Y-m-d');
            }
            if (\Schema::hasColumn('drivers', 'is_approved')) {
                $driver->is_approved = $driver->is_approved ?? 0;
            }
            $driver->save();

            $response = [
                'success' => true,
                'error' => false,
                'message' => 'New Driver registered successful. Driver ID: ' . $driverId,
                'drivers_id' => $driver->id,
                'driver_id' => $driverId,
            ];
            Log::debug('driver_store response', array_merge($response, [
                'company_id' => $companyId,
            ]));

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            Log::error('driver_store failed', [
                'company_id' => $companyId,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Failed to register new Driver: ' . $e->getMessage(),
            ], 200);
        }
    }

    public function car_store(Request $request)
    {
        
        $this->validate($request,[
            'carNumber'=>'required',

        ]); 
        
        // Get company_id from user_id
        $companyId = $this->getCompanyId($request);
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);
      
        $farmer= new Car();

        $farmer->carNumber=$request->input('carNumber');
        $farmer->status =5;
        if ($companyId) {
            $farmer->company_id = $companyId;
        }
        $farmer->save();
        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Car registered successful'];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to register new  Car'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function car_selection(Request $request)
    {
        
        $this->validate($request,[
            'user_id'=>'required',
            'car_id'=>'required',
            'driver_id'=>'required',
            'from'=>'required',
            'to'=>'required',

        ]); 
        
        //$data=$this->request();
        //$data['user_id'] =auth()->user()->id;
        //$farmer= Farmer::create($data);

        // Get company_id from user_id
        $companyId = $this->getCompanyId($request);
        
        $assigned_date = Carbon::now()->format('Y-m-d');


        $driver =  Driver::where('id', $request->input('driver_id'))->update(['status' => '2', 'assigned_date' => $assigned_date]);

        $car =  Car::where('id', $request->input('car_id'))->update(['driver_id' => $request->input('driver_id'), 'status' => '2']);

      
        $farmer= new DriverRoute();

        if(!empty($request->start_date)){

            $farmer->user_id=$request->input('user_id');
            $farmer->car_id=$request->input('car_id');
            $farmer->driver_id=$request->input('driver_id');
            $farmer->from=$request->input('from');
            $farmer->to=$request->input('to');
            $farmer->status= '2';
            $farmer->start_date=$request->input('start_date');
            if ($companyId) {
                $farmer->company_id = $companyId; // Set company_id
            }

            $farmer->save();

        }
        else{

            $today = Carbon::now()->format('Y-m-d');


            $farmer->user_id=$request->input('user_id');
            $farmer->car_id=$request->input('car_id');
            $farmer->driver_id=$request->input('driver_id');
            $farmer->from=$request->input('from');
            $farmer->to=$request->input('to');
            $farmer->status= '2';
            $farmer->start_date = $today;
            if ($companyId) {
                $farmer->company_id = $companyId; // Set company_id
            }


            $farmer->save();

        }
        



        // $car_history = new CarHistory();

        // $car_history->user_id=$request->input('user_id');
        // $car_history->car_id=$request->input('car_id');
        // $car_history->driver_id=$request->input('driver_id');
        // $car_history->status= '2';
        // $car_history->startPacking= $assigned_date;

        // $car_history->save();

        if($farmer)
        {
            $response=['success'=>true,'error'=>false,'message'=>'New Car and Driver  selected and Route given  successful', 'farmer' => $farmer];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to select Car and Driver successful'];
            return response()->json($response,200);
        }

        //return view('manage-farmer');
    }

    public function packing_store(Request $request)
    {
        
        $this->validate($request,[
            'user_id'=>'required',
            'car_id'=>'required',
            'pacel_id'=>'required',
            'idadi'=>'required',
            'price'=>'nullable',
            'activity'=>'required',
            'pacel_hist_id'=>'nullable|integer',
            'hashtag'=>'nullable|string',
        ]); 

        // Normalize activity so kupakia / kushusha / kubadili branches match reliably (client casing)
        $request->merge([
            'activity' => strtolower(trim((string) $request->input('activity'))),
            'price' => $request->input('price') === null || $request->input('price') === ''
                ? 0
                : $request->input('price'),
        ]);
        
		// Optimize with transaction: kupakia (pack) and kushusha (return qty from idadi_kupakia to idadi_stoo on customer_pacels only)
		$packedHistory = null;
		try{
		DB::transaction(function () use ($request, &$packedHistory) {
			$pacelId = (int) $request->input('pacel_id');
			$carId = (int) $request->input('car_id');
			$userId = (int) $request->input('user_id');
			$idadi = (int) $request->input('idadi');
			$price = (float) $request->input('price');
			$hashtag = (string) $request->input('hashtag');
			$activity = (string) $request->input('activity');

			// Get company_id from user
			$user = User::find($userId);
			$companyId = $user ? $user->company_id : null;
			
			if ($activity === 'kupakia') {
				$cp = CustomerPacel::with(['destinations.dropMikoa'])->select([
					'id', 'mteja', 'mpokeaji', 'name', 'ela_iliyopokelewa', 'mzigo_unapotoka', 'mzigo_unapokwenda', 'drop_mikoa_id', 'cargo_route_id', 'customer_id', 'delivery', 'status',
				])->findOrFail($pacelId);

				$routeQuery = DriverRoute::query()
					->join('cars', 'cars.id', '=', 'driver_routes.car_id')
					->where('driver_routes.car_id', $carId)
					->where(function ($q) {
						$q->whereNull('driver_routes.closeDate')
							->orWhere('driver_routes.closeDate', '0000-00-00');
					});
				if ($companyId) {
					$routeQuery->where('cars.company_id', $companyId);
				}
				$activeRoute = $routeQuery->orderBy('driver_routes.created_at', 'desc')
					->select('driver_routes.id', 'driver_routes.to', 'driver_routes.from')
					->first();

				if (!$activeRoute) {
					Log::warning('packing_store kupakia: no open driver route for car', [
						'pacel_id' => $pacelId,
						'car_id' => $carId,
						'company_id' => $companyId,
						'user_id' => $userId,
					]);
					throw new \RuntimeException(
						'Cannot pack: this vehicle has no open trip (route). Use a car with an active route whose destination matches the parcel.'
					);
				}

				$routeToRaw = trim((string) ($activeRoute->to ?? ''));
				if ($routeToRaw === '') {
					Log::warning('packing_store kupakia: active route has empty destination', [
						'driver_route_id' => $activeRoute->id,
						'pacel_id' => $pacelId,
						'car_id' => $carId,
						'company_id' => $companyId,
					]);
					throw new \RuntimeException(
						'Cannot pack: the vehicle\'s active route has no destination set.'
					);
				}

				$allowedDestinations = $this->cargoRouteService->allowedPackDestinationsForDriverRoute(
                    $activeRoute,
                    $companyId
                );

                if ($cp->destinations->isNotEmpty()) {
                    foreach ($cp->destinations as $destLine) {
                        $destName = $destLine->dropMikoa->regional_name ?? '';
                        if ($destName === '' || !$this->cargoRouteService->parcelDestinationMatchesLabels($destName, $allowedDestinations)) {
                            throw new \RuntimeException(
                                'Cannot pack: parcel split destination does not match this vehicle route stops.'
                            );
                        }
                    }
                } else {
                    $parcelDestNorm = $this->normalizePackingDestinationLabel($cp->mzigo_unapokwenda ?? '');
                    if ($parcelDestNorm === '' || !$this->cargoRouteService->parcelDestinationMatchesLabels($cp->mzigo_unapokwenda ?? '', $allowedDestinations)) {
                        $dropMikoaName = null;
                        if (!empty($cp->drop_mikoa_id)) {
                            $dropMikoaName = Mikoa::where('id', $cp->drop_mikoa_id)->value('regional_name');
                        }
                        $matchesByDropId = $dropMikoaName
                            && $this->cargoRouteService->parcelDestinationMatchesLabels($dropMikoaName, $allowedDestinations);

                        if (!$matchesByDropId) {
                            Log::warning('packing_store kupakia: parcel destination does not match route stops', [
                                'pacel_id' => $pacelId,
                                'parcel_destination' => $cp->mzigo_unapokwenda,
                                'parcel_destination_normalized' => $parcelDestNorm,
                                'car_id' => $carId,
                                'driver_route_id' => $activeRoute->id,
                                'route_destination' => $activeRoute->to,
                                'allowed_destinations' => $allowedDestinations,
                                'company_id' => $companyId,
                                'user_id' => $userId,
                            ]);
                            throw new \RuntimeException(
                                'Cannot pack: parcel drop-off does not match this vehicle route stops. '
                                . 'Parcel drop-off (mzigo_unapokwenda): "' . ($cp->mzigo_unapokwenda ?? '') . '". '
                                . 'Allowed stops: ' . implode(', ', $allowedDestinations) . '.'
                            );
                        }
                    }
                }

				// 1) Create load record (packing onto vehicle only)
				PackageLoad::create([
					'user_id' => $userId,
					'car_id' => $carId,
					'pacel_id' => $pacelId,
					'idadi' => $idadi,
					'price' => $price,
					'company_id' => $companyId, // Set company_id
				]);

				// 2) Update CustomerPacel atomically when kupakia
				$affected = CustomerPacel::where('id', $pacelId)
					->where('idadi_stoo', '>=', $idadi)
					->update([
						'activity' => $activity,
						'idadi_kupakia' => DB::raw('idadi_kupakia + ' . $idadi),
						'idadi_stoo' => DB::raw('idadi_stoo - ' . $idadi),
						'bei' => $price,
						'hashtag' => $hashtag,
						'car_id' => $carId,
						// Keep total cost on full registered qty (not only this pack qty)
						'jumla' => DB::raw('idadi * ' . $price),
					]);

				if ($affected === 0) {
					throw new \RuntimeException('Insufficient stock for the requested quantity');
				}

				$cp->refresh();
				$cp->load('destinations');
				$this->cargoRouteService->allocatePackToDestinations($cp, $idadi);

				// 3) Upsert into PacelHistory under lock
				$existing = PacelHistory::where([
					'pacel_id' => $pacelId,
					'car_id' => $carId,
					'hashtag' => $hashtag,
					'bei' => $price,
				])->lockForUpdate()->first();

				if ($existing) {
					$newKupakia = $existing->idadi_kupakia + $idadi;
					$existing->update([
						'idadi_kupakia' => $newKupakia,
						'jumla' => $existing->bei * $newKupakia,
					]);
					$packedHistory = $existing->fresh();
				} else {
					$created = PacelHistory::create([
						'pacel_id' => $cp->id,
						'mteja' => $cp->mteja,
						'mpokeaji' => $cp->mpokeaji,
						'name' => $cp->name,
						'idadi' => $idadi,
						'bei' => $price,
						'hashtag' => $hashtag,
						'ela_iliyopokelewa' => $cp->ela_iliyopokelewa,
						'jumla' => $idadi * $price,
						'activity' => $activity,
						'mzigo_unapotoka' => $cp->mzigo_unapotoka,
						'mzigo_unapokwenda' => $cp->mzigo_unapokwenda,
						'car_id' => $carId,
						'customer_id' => $cp->customer_id,
						'driver_id' => $request->input('driver_id'),
						'idadi_kupakia' => $idadi,
						'delivery' => $cp->delivery,
						'status' => $cp->status,
						'added_by' => $userId,
						'company_id' => $companyId, // Set company_id
					]);
					$packedHistory = $created;
				}

				// 4) Upsert into ParcelCarAssignment (update same parcel+car row after unpack/repack)
				$assignment = ParcelCarAssignment::where([
					'customer_id' => $cp->customer_id,
					'car_id' => $carId,
					'pacel_id' => $pacelId,
				])->lockForUpdate()->first();

				if ($assignment) {
					$assignment->update([
						'idadi' => (int) $assignment->idadi + $idadi,
					]);
				} else {
					ParcelCarAssignment::create([
						'customer_id' => $cp->customer_id,
						'car_id' => $carId,
						'pacel_id' => $pacelId,
						'idadi' => $idadi,
						'company_id' => $companyId,
					]);
				}
			} elseif ($activity === 'kushusha') {
				// Punguza: return qty from truck → store (idadi_kupakia → idadi_stoo)
				$cpRow = CustomerPacel::with('destinations')->where('id', $pacelId)->lockForUpdate()->first();
				if (!$cpRow) {
					throw new \RuntimeException('Parcel not found');
				}

				$availableOnTruck = $this->cargoRouteService->packedQuantityOnTruck($cpRow);
				if ($availableOnTruck < $idadi) {
					throw new \RuntimeException(
						'Idadi inazidi kilichopakiwa kwenye gari (' . $availableOnTruck . ')'
					);
				}

				// Sync header if destination lines hold the packed qty
				if ((int) $cpRow->idadi_kupakia < $availableOnTruck) {
					$cpRow->idadi_kupakia = $availableOnTruck;
					$cpRow->save();
				}

				$newKupakia = max(0, (int) $cpRow->idadi_kupakia - $idadi);
				$cpRow->update([
					'activity' => $newKupakia > 0 ? 'kupakia' : 'kushusha',
					'idadi_kupakia' => $newKupakia,
					'idadi_stoo' => DB::raw('COALESCE(idadi_stoo, 0) + ' . $idadi),
				]);
				$cpRow->refresh();
				$cpRow->load('destinations');
				$this->cargoRouteService->deallocatePackFromDestinations($cpRow, $idadi);

				// Soft-reduce package_load rows (legacy packs may have none)
				$loadSum = (int) PackageLoad::where('pacel_id', $pacelId)
					->where('car_id', $carId)
					->sum('idadi');
				if ($loadSum > 0) {
					$remainingLoad = min($idadi, $loadSum);
					while ($remainingLoad > 0) {
						$loadRow = PackageLoad::where('pacel_id', $pacelId)
							->where('car_id', $carId)
							->where('idadi', '>', 0)
							->orderByDesc('id')
							->lockForUpdate()
							->first();
						if (!$loadRow) {
							break;
						}
						$take = min((int) $loadRow->idadi, $remainingLoad);
						$loadRow->update(['idadi' => (int) $loadRow->idadi - $take]);
						$remainingLoad -= $take;
					}
				}

				// Resolve pack history: prefer pacel_hist_id, then soft match, then any with stock
				$histRow = null;
				$histId = (int) $request->input('pacel_hist_id', 0);
				if ($histId > 0) {
					$histRow = PacelHistory::where('id', $histId)
						->where('pacel_id', $pacelId)
						->where('car_id', $carId)
						->lockForUpdate()
						->first();
				}

				if (!$histRow) {
					$histQuery = PacelHistory::where('pacel_id', $pacelId)
						->where('car_id', $carId)
						->where('idadi_kupakia', '>', 0)
						->orderByDesc('id');

					$htag = trim((string) $hashtag);
					if ($htag !== '') {
						$histQuery->where(function ($q) use ($htag) {
							$q->where('hashtag', $htag)
								->orWhereNull('hashtag')
								->orWhere('hashtag', '');
						});
					}

					$priceNorm = (float) str_replace(',', '', (string) $price);
					if ($priceNorm > 0) {
						$histQuery->where(function ($q) use ($priceNorm, $price) {
							$q->where('bei', $price)
								->orWhere('bei', $priceNorm)
								->orWhere('bei', (string) $priceNorm)
								->orWhereRaw('CAST(REPLACE(bei, ",", "") AS DECIMAL(18,2)) = ?', [$priceNorm]);
						});
					}

					$histRow = $histQuery->lockForUpdate()->first();
				}

				if (!$histRow) {
					$histRow = PacelHistory::where('pacel_id', $pacelId)
						->where('car_id', $carId)
						->where('idadi_kupakia', '>', 0)
						->orderByDesc('id')
						->lockForUpdate()
						->first();
				}

				if (!$histRow) {
					// Last resort: any kupakia history for this parcel+car
					$histRow = PacelHistory::where('pacel_id', $pacelId)
						->where('car_id', $carId)
						->whereIn('activity', ['kupakia', 'ipo njiani'])
						->orderByDesc('id')
						->lockForUpdate()
						->first();
				}

				if ($histRow) {
					$histAvailable = (int) $histRow->idadi_kupakia;
					$histTake = $histAvailable > 0 ? min($idadi, $histAvailable) : 0;
					if ($histTake > 0) {
						$newHistKupakia = $histAvailable - $histTake;
						$beiNum = (float) str_replace(',', '', (string) $histRow->bei);
						$histRow->update([
							'idadi_kupakia' => $newHistKupakia,
							'jumla' => (string) ($beiNum * max($newHistKupakia, 0)),
						]);
					}
					$packedHistory = $histRow->fresh();
				} else {
					// No history row — still allow punguza (parcel + destinations already updated)
					$packedHistory = PacelHistory::where('pacel_id', $pacelId)
						->where('car_id', $carId)
						->orderByDesc('id')
						->first();
				}

				// Reduce ParcelCarAssignment
				$assignment = ParcelCarAssignment::where([
					'customer_id' => $cpRow->customer_id,
					'car_id' => $carId,
					'pacel_id' => $pacelId,
				])->lockForUpdate()->first();
				if ($assignment) {
					$newAssign = (int) $assignment->idadi - $idadi;
					if ($newAssign <= 0) {
						$assignment->delete();
					} else {
						$assignment->update(['idadi' => $newAssign]);
					}
				}
			}
		});
		}catch(\Throwable $e){
			return response()->json(['success'=>false,'error'=>true,'message'=>'Failed to pack: '.$e->getMessage()],422);
		}

		// Dispatch SMS (same as former car_close): send when packing onto vehicle (kupakia)
		if ($request->input('activity') === 'kupakia') {
			try {
				$userIdSms = (int) $request->input('user_id');
				$carIdSms = (int) $request->input('car_id');
				$pacelIdSms = (int) $request->input('pacel_id');
				$userSms = User::find($userIdSms);
				$companyIdSms = $userSms ? $userSms->company_id : null;
				$carSms = Car::find($carIdSms);
				$parcelRowSms = CustomerPacel::find($pacelIdSms);
				if ($carSms && $parcelRowSms && $parcelRowSms->customer_id) {
					$parcelsSmsQuery = CustomerPacel::where('car_id', $carIdSms)
						->where('customer_id', $parcelRowSms->customer_id)
						->whereIn('activity', ['kupakia', 'ipo njiani']);
					if ($companyIdSms) {
						$parcelsSmsQuery->where('company_id', $companyIdSms);
					}
					$parcelsSms = $parcelsSmsQuery->get();
					$customerSms = Customer::find($parcelRowSms->customer_id);
					if ($customerSms && $customerSms->customer_phone) {
						$totalPackagesSms = $parcelsSms->sum('idadi');
						$this->smsService->sendDispatchSms(
							$customerSms->customer_phone,
							$carSms->carNumber,
							$totalPackagesSms,
							$customerSms->id,
							$companyIdSms
						);
					}
				}
			} catch (\Exception $e) {
				\Log::error('Failed to send dispatch SMS after packing (kupakia): ' . $e->getMessage());
			}
		}

		$pacel = $packedHistory;

		//update on customer pacel table (other activities continue)
		$pacelOld =  CustomerPacel::find($request->pacel_id);

		// $pacelOld->idadi_stoo = $pacelOld->idadi;
		if($request->input('activity') == "kupakia"){
			// handled in transaction above
		}
        elseif($request->input('activity') == "kushusha"){
            // customer_pacels only: idadi_kupakia reduced, idadi_stoo increased (handled in transaction)
        }
        elseif($request->input('activity') == "kubadili"){

        $pacelOld->activity = $request->input('activity');

        // $pacelOld->idadi_kupakia =$pacelOld->idadi_kupakia +;


        // $pacelOld->idadi_stoo = $pacelOld->idadi_stoo  - $request->input('idadi');


        $pacelOld->bei = $request->input('price');

        $pacelOld->hashtag = $request->input('hashtag');


        

        // $pacelOld->car_id = $request->input('car_id');

        // $pacelOld->driver_id = $request->input('driver_id');

        $pacelOld->jumla = $pacelOld->idadi_stoo * $request->input('price');

        $pacelOld->update();

        //install on pacel 

        $cp_id = CustomerPacel::find($request->pacel_id);


        $pacel = new  PacelHistory();


        $pacel->pacel_id = $cp_id->id;

        $pacel->mteja = $cp_id->mteja;

        $pacel->mpokeaji = $cp_id->mpokeaji;

        $pacel->name = $cp_id->name;

        $pacel->idadi = $cp_id->idadi; 

        $pacel->bei = $request->input('price');

        $pacel->hashtag = $request->input('hashtag');


        $pacel->ela_iliyopokelewa = $cp_id->ela_iliyopokelewa;

        $pacel->jumla = $cp_id->idadi * $request->input('price');

        $pacel->activity = $request->input('activity');

        $pacel->mzigo_unapotoka = $cp_id->mzigo_unapotoka;

        $pacel->mzigo_unapokwenda = $cp_id->mzigo_unapokwenda;

        $pacel->car_id = $cp_id->car_id;

        $pacel->customer_id = $cp_id->customer_id;

        $pacel->driver_id = $request->input('driver_id');

        // $pacel->idadi_kupakia = $cp_id->idadi_kupakia;

        // $pacel->idadi_shusha = $cp_id->idadi_shusha;

        $pacel->delivery = $cp_id->delivery;

        $pacel->status = $cp_id->status;

        $pacel->added_by = $request->input('user_id');

        $pacel->save();

        // update mzigo
        $pacel23 = PacelHistory::find($request->input('pacel_hist_id'));

        $data23['bei'] = $request->input('price');

        $data23['hashtag'] = $request->input('hashtag');

        $data23['jumla'] = $pacel23->idadi_kupakia * $request->input('price');

        $pacel23->update($data23);





        }
        else{

        $pacelOld->idadi_shusha = $pacelOld->idadi_shusha + $request->input('idadi');

        $pacelOld->idadi_kupakia = $pacelOld->idadi_kupakia - $request->input('idadi');


        $pacelOld->bei = $request->input('price');

        $pacelOld->hashtag = $request->input('hashtag');


        $pacelOld->activity = $request->input('activity');

        $pacelOld->idadi_stoo = $pacelOld->idadi_stoo  +  $request->input('idadi');


        $pacelOld->car_id = $request->input('car_id');

        // $pacelOld->driver_id = $request->input('driver_id');

        $pacelOld->jumla = $request->input('idadi') * $request->input('price');

        $pacelOld->update();

         //install on pacel 

         $cp_id = CustomerPacel::find($request->pacel_id);


         $pacel = new  PacelHistory();
 
 
         $pacel->pacel_id = $cp_id->id;
 
         $pacel->mteja = $cp_id->mteja;
 
         $pacel->mpokeaji = $cp_id->mpokeaji;
 
         $pacel->name = $cp_id->name;
 
         $pacel->idadi = $cp_id->idadi;
 
         $pacel->bei = $request->input('price');

         $pacel->hashtag = $request->input('hashtag');

 
         $pacel->ela_iliyopokelewa = $cp_id->ela_iliyopokelewa;
 
         $pacel->jumla = $cp_id->jumla;
 
         $pacel->activity = $request->input('activity');
 
         $pacel->mzigo_unapotoka = $cp_id->mzigo_unapotoka;
 
         $pacel->mzigo_unapokwenda = $cp_id->mzigo_unapokwenda;
 
         $pacel->car_id = $request->input('car_id');
 
         $pacel->customer_id = $cp_id->customer_id;
 
         $pacel->driver_id = $request->input('driver_id');
 
        //  $pacel->idadi_kupakia = $cp_id->idadi_kupakia;
 
         $pacel->idadi_shusha = $request->input('idadi');
 
         $pacel->delivery = $cp_id->delivery;
 
         $pacel->status = $cp_id->status;
 
         $pacel->added_by = $request->input('user_id');

 
         $pacel->save();

         // update mzigo wkt kushusha
        $pacel23 = PacelHistory::find($request->input('pacel_hist_id'));

        // $pacel23->bei = $request->input('price');

        // $pacel23->hashtag = $request->input('hashtag');
        $idadi_kupakia2 = $pacel23->idadi_kupakia - $request->input('idadi');

        $data123['idadi_kupakia'] = $pacel23->idadi_kupakia - $request->input('idadi');

        $data123['jumla'] = $request->input('price') * $idadi_kupakia2;

        $pacel23->update($data123);

        }

        

        if($pacel)
        {
            $response=['success'=>true,'error'=>false,'message'=>'Package packing  successful', 'pacel'=>$pacel];
            return response()->json($response,200);
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to pack Package successful'];
            return response()->json($response,400);
        }

        //return view('manage-farmer');
    }

    public function unpacking_store(Request $request)
    {
        $this->validate($request,[
            'user_id'=>'required',
            'car_id'=>'required',
            'pacel_id'=>'required',
            'idadi'=>'required',
            'price'=>'required'
        ]); 
        
        try {
            $updatedPacel = DB::transaction(function () use ($request) {
                $pacelId = $request->input('pacel_id');
                $carId = $request->input('car_id');
                $idadi = $request->input('idadi');
                $price = $request->input('price');
                $userId = $request->input('user_id');

                $pacelOld = CustomerPacel::findOrFail($pacelId);

                // Update CustomerPacel
                $pacelOld->idadi_shusha += $idadi;
                $pacelOld->idadi_kupakia -= $idadi;
                $pacelOld->idadi_stoo += $idadi;
                $pacelOld->bei = $price;
                $pacelOld->activity = $request->input('activity');
                $pacelOld->car_id = $carId;
                $pacelOld->jumla = $idadi * $price;
                $pacelOld->save();

                // Create PacelHistory
                $pacelHistory = new PacelHistory();
                // Copy attributes from pacelOld (this was the original logic's intent)
                $pacelHistory->fill($pacelOld->toArray());
                $pacelHistory->pacel_id = $pacelOld->id;
                $pacelHistory->added_by = $userId;
                $pacelHistory->save();

                // Update ParcelCarAssignment
                $assignment = ParcelCarAssignment::where([
                    'customer_id' => $pacelOld->customer_id,
                    'car_id' => $carId,
                    'pacel_id' => $pacelId,
                ])->first();

                if ($assignment) {
                    $newQuantity = $assignment->idadi - $idadi;
                    if ($newQuantity <= 0) {
                        $assignment->delete();
                    } else {
                        $assignment->update(['idadi' => $newQuantity]);
                    }
                }

                return $pacelOld;
            });

            return response()->json([
                'success' => true, 
                'error' => false, 
                'message' => 'Package unpacking successful', 
                'pacel' => $updatedPacel
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => true, 
                'message' => 'Failed to unpack Package: ' . $e->getMessage()
            ], 200);
        }
    }

    

    public function driver_assign(int $driver_id, int $car_id)
    {
        
      
        // $assigned_date = Carbon::now()->format('Y-m-d');

        $driver =  Driver::where('id', $driver_id)->update(['status' => '2']);

        $car =  Car::where('id', $car_id)->update(['driver_id' => $driver_id]);

        if($driver)
        {
            if($car){
                $response=['success'=>true,'error'=>false,'message'=>'Driver Assigned car successful'];
                return response()->json($response,200);
            }
            else{
                $response=['success'=>false,'error'=>true,'message'=>'Car Failed to be Assigned successful cause of car id'];
                return response()->json($response,200);
            }
            
        }
        else
        {
            $response=['success'=>false,'error'=>true,'message'=>'Failed to Driver Assigned Car'];
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

    /**
     * Get the sub-list of packing assignments for a customer.
     * Shows which parcels are packed in which cars and in what quantities.
     */
    public function customer_packing_sublist(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
        ]);

        $customerId = $request->input('customer_id');
        $companyId = $this->getCompanyId($request);

        $query = ParcelCarAssignment::with(['car:id,carNumber', 'parcel:id,name,idadi'])
            ->where('customer_id', $customerId);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $assignments = $query->get()->map(function($assignment) {
            return [
                'id' => $assignment->id,
                'car_id' => $assignment->car_id,
                'car_number' => $assignment->car ? $assignment->car->carNumber : 'N/A',
                'pacel_id' => $assignment->pacel_id,
                'pacel_name' => $assignment->parcel ? $assignment->parcel->name : 'N/A',
                'total_quantity' => $assignment->parcel ? $assignment->parcel->idadi : 0,
                'packed_quantity' => $assignment->idadi,
                'created_at' => $assignment->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'successfully',
            'customer_id' => $customerId,
            'assignments' => $assignments
        ], 200);
    }
}
