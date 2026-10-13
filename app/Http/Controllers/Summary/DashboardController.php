<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerPacel;
use App\Models\Management\Car;
use App\Models\DriverRoute;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        // Get logged-in user
        $user = auth()->user();
        
        // Check if user has company_id assigned
        if (!$user->company_id) {
            return redirect()->back()->with('error', 'User does not have a company assigned. Please contact administrator.');
        }
        
        // Get company_id from logged-in user
        // All users with the same company_id will see the same dashboard data
        $companyId = $user->company_id;
        
        $todayDate = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startDate = Carbon::parse('2025-10-19');
        
        // Customers metrics - from 2025-10-19 to current date, filtered by company_id
        $totalCustomers = Customer::where('company_id', $companyId)->where('created_at', '>=', $startDate)->where('created_at', '<=', now())->count();
        $todayCustomers = Customer::where('company_id', $companyId)->whereDate('created_at', $todayDate)->count();
        $weekCustomers = Customer::where('company_id', $companyId)->where('created_at', '>=', $startOfWeek)->count();
        $monthCustomers = Customer::where('company_id', $companyId)->where('created_at', '>=', $startOfMonth)->count();

        // Mizigo (parcels) metrics - sum of idadi from 2025-10-19 to current date, filtered by company_id
        $totalMizigo = CustomerPacel::where('company_id', $companyId)->where('created_at', '>=', $startDate)->where('created_at', '<=', now())->sum('idadi');
        $todayMizigo = CustomerPacel::where('company_id', $companyId)->whereDate('created_at', $todayDate)->sum('idadi');
        $weekMizigo = CustomerPacel::where('company_id', $companyId)->where('created_at', '>=', $startOfWeek)->sum('idadi');
        $monthMizigo = CustomerPacel::where('company_id', $companyId)->where('created_at', '>=', $startOfMonth)->sum('idadi');

        // Revenue metrics (using 'jumla' as total and 'ela_iliyopokelewa' as paid)
        $totalRevenue = CustomerPacel::where('company_id', $companyId)->where('created_at', '>=', $startDate)->where('created_at', '<=', now())->sum('jumla');
        $todayRevenue = CustomerPacel::where('company_id', $companyId)->whereDate('created_at', $todayDate)->sum('jumla');
        $pendingRevenue = CustomerPacel::where('company_id', $companyId)->where('created_at', '>=', $startDate)->selectRaw('SUM(jumla - ela_iliyopokelewa) as pending')->first()->pending ?? 0;

        // Activity Distribution (kusajiliwa vs kupakia)
        $activityStats = CustomerPacel::where('company_id', $companyId)
            ->whereDate('created_at', $todayDate)
            ->select('activity', DB::raw('count(*) as count'))
            ->groupBy('activity')
            ->get()
            ->pluck('count', 'activity');

        // Cars metrics and lists - filtered by company_id
        $activeCarsCount = Car::where('company_id', $companyId)->where('status', 2)->count();
        $arrivedCarsCount = Car::where('company_id', $companyId)->where('status', 1)->count();
        $inTransitCount = Car::where('company_id', $companyId)->where('status', 0)->count();
        $closedCarsCount = Car::where('company_id', $companyId)->where('status', 3)->count();
        $pendingCarsCount = Car::where('company_id', $companyId)->where('status', 5)->count();

        // Trend data for charts (Last 10 days for better view)
        $days = [];
        $parcelTrends = [];
        $revenueTrends = [];
        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = $date->format('d M');
            
            $parcelTrends[] = CustomerPacel::where('company_id', $companyId)
                ->whereDate('created_at', $date)
                ->sum('idadi');
                
            $revenueTrends[] = CustomerPacel::where('company_id', $companyId)
                ->whereDate('created_at', $date)
                ->sum('jumla');
        }

        // --- Restoring the detailed lists for the dashboard ---
        
        // Latest 5 by updated_at for tables (not restricted to today) - filtered by company_id
        $activeCarsToday = Car::where('company_id', $companyId)
            ->where('status', 2)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();
        $arrivedCarsToday = Car::where('company_id', $companyId)
            ->where('status', 1)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();
        $inTransitCars = Car::where('company_id', $companyId)
            ->where('status', 0)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        $latestToday = Customer::where('company_id', $companyId)
            ->whereDate('created_at', $todayDate)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // Open routes for this company (closeDate null or '0000-00-00')
        $openRoutes = DriverRoute::query()
            ->join('cars', 'cars.id', '=', 'driver_routes.car_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'driver_routes.driver_id')
            ->where('cars.company_id', $companyId)
            ->where(function ($q) {
                $q->whereNull('driver_routes.closeDate')
                  ->orWhere('driver_routes.closeDate', '0000-00-00');
            })
            ->select(
                'driver_routes.id',
                'driver_routes.car_id',
                'driver_routes.from',
                'driver_routes.to',
                'driver_routes.status',
                'cars.carNumber as car_name',
                'cars.status as car_status',
                'drivers.name as driver_name'
            )
            ->orderBy('driver_routes.created_at', 'DESC')
            ->get();

        // Summary of kushusha today from drop_off_parcels
        $kushushaStats = DB::table('drop_off_parcels')
            ->whereDate('created_at', $todayDate)
            ->where('company_id', $companyId)
            ->selectRaw('COALESCE(SUM(idadi_kushusha), 0) as total_kushusha, COUNT(DISTINCT customer_pacel_id) as parcels_count')
            ->first();

        // Today's drop-off points with car and driver and total kushusha per point
        $todayDropPoints = DB::table('drop_off_parcels')
            ->join('drop_off_points', 'drop_off_points.id', '=', 'drop_off_parcels.drop_off_point_id')
            ->leftJoin('cars', 'cars.id', '=', 'drop_off_parcels.car_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'drop_off_parcels.driver_id')
            ->whereDate('drop_off_parcels.created_at', $todayDate)
            ->where('drop_off_parcels.company_id', $companyId)
            ->select(
                'drop_off_points.drop_off_point',
                'cars.carNumber as car_name',
                'drivers.name as driver_name',
                DB::raw('SUM(drop_off_parcels.idadi_kushusha) as total_kushusha')
            )
            ->groupBy('drop_off_points.drop_off_point', 'cars.carNumber', 'drivers.name')
            ->orderBy('drop_off_points.drop_off_point')
            ->get();

        return view('summary.dashboard', [
            'totalCustomers' => $totalCustomers,
            'todayCustomers' => $todayCustomers,
            'weekCustomers' => $weekCustomers,
            'monthCustomers' => $monthCustomers,
            'totalMizigo' => $totalMizigo,
            'todayMizigo' => $todayMizigo,
            'weekMizigo' => $weekMizigo,
            'monthMizigo' => $monthMizigo,
            'totalRevenue' => $totalRevenue,
            'todayRevenue' => $todayRevenue,
            'pendingRevenue' => $pendingRevenue,
            'activityStats' => $activityStats,
            'activeCarsCount' => $activeCarsCount,
            'arrivedCarsCount' => $arrivedCarsCount,
            'inTransitCount' => $inTransitCount,
            'closedCarsCount' => $closedCarsCount,
            'pendingCarsCount' => $pendingCarsCount,
            'activeCars' => $activeCarsToday,
            'arrivedCars' => $arrivedCarsToday,
            'inTransitCars' => $inTransitCars,
            'latestToday' => $latestToday,
            'openRoutes' => $openRoutes,
            'totalKushushaToday' => $kushushaStats->total_kushusha ?? 0,
            'parcelsDroppedToday' => $kushushaStats->parcels_count ?? 0,
            'todayDropPoints' => $todayDropPoints,
            'todayDate' => $todayDate,
            'chartDays' => $days,
            'parcelTrends' => $parcelTrends,
            'revenueTrends' => $revenueTrends,
        ]);
    }
}



