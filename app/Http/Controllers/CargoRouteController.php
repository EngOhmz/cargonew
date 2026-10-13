<?php

namespace App\Http\Controllers;

use App\Models\CargoRoute;
use App\Models\CargoRouteStop;
use App\Models\Mikoa;
use App\Services\CargoRouteService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CargoRouteController extends Controller
{
    protected CargoRouteService $cargoRouteService;

    public function __construct(CargoRouteService $cargoRouteService)
    {
        $this->middleware('auth');
        $this->cargoRouteService = $cargoRouteService;
    }

    public function index()
    {
        $companyId = auth()->user()->company_id;

        $mikoa = Mikoa::where('company_id', $companyId)
            ->orderBy('regional_name')
            ->get();

        $routes = CargoRoute::with(['fromMikoa', 'toMikoa', 'stops.mikoa'])
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->get();

        foreach ($routes as $route) {
            $this->cargoRouteService->syncFinalStop($route);
            $route->load('stops.mikoa');
        }

        return view('cargo_routes.index', compact('mikoa', 'routes'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $request->validate([
            'from_mikoa_id' => [
                'required',
                Rule::exists('mikoa', 'id')->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                }),
            ],
            'to_mikoa_id' => [
                'required',
                'different:from_mikoa_id',
                Rule::exists('mikoa', 'id')->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                }),
            ],
        ], [
            'to_mikoa_id.different' => 'Mkoa wa kwenda lazima utofautiane na mkoa wa kutoka.',
        ]);

        $exists = CargoRoute::where('company_id', $companyId)
            ->where('from_mikoa_id', $request->from_mikoa_id)
            ->where('to_mikoa_id', $request->to_mikoa_id)
            ->exists();

        if ($exists) {
            return redirect()->route('cargo_routes.index')
                ->withErrors(['duplicate' => 'Route hii tayari ipo.'])
                ->withInput();
        }

        $route = CargoRoute::create([
            'company_id' => $companyId,
            'from_mikoa_id' => $request->from_mikoa_id,
            'to_mikoa_id' => $request->to_mikoa_id,
        ]);

        $this->cargoRouteService->syncFinalStop($route);

        return redirect()->route('cargo_routes.index')->with('success', 'Route imeongezwa.');
    }

    public function storeStop(Request $request, $routeId)
    {
        $companyId = auth()->user()->company_id;

        if (!$this->cargoRouteService->allowsEnrouteDrops()) {
            return redirect()->route('cargo_routes.index')
                ->withErrors(['stop' => 'Madekenya haishushi mizigo njiani. Mizigo yote inaenda mwisho wa safari.']);
        }

        $route = CargoRoute::where('company_id', $companyId)->findOrFail($routeId);

        $request->validate([
            'mikoa_id' => [
                'required',
                Rule::exists('mikoa', 'id')->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                }),
                Rule::notIn([$route->from_mikoa_id]),
            ],
            'stop_order' => 'nullable|integer|min:1|max:98',
        ], [
            'mikoa_id.not_in' => 'Kituo cha njiani hakiwezi kuwa mkoa wa kutoka.',
        ]);

        if ((int) $request->mikoa_id === (int) $route->to_mikoa_id) {
            return redirect()->route('cargo_routes.index')
                ->withErrors(['stop' => 'Kituo cha mwisho tayari kimeundwa na route. Tumia mkoa wa njiani tu.'])
                ->withInput();
        }

        $exists = CargoRouteStop::where('cargo_route_id', $route->id)
            ->where('mikoa_id', $request->mikoa_id)
            ->exists();

        if ($exists) {
            return redirect()->route('cargo_routes.index')
                ->withErrors(['stop' => 'Kituo hiki tayari kipo kwenye route.'])
                ->withInput();
        }

        $order = $request->filled('stop_order')
            ? (int) $request->stop_order
            : ((int) CargoRouteStop::where('cargo_route_id', $route->id)->max('stop_order') + 1);

        CargoRouteStop::create([
            'cargo_route_id' => $route->id,
            'mikoa_id' => $request->mikoa_id,
            'stop_order' => min($order, 98),
            'is_final' => false,
        ]);

        $this->cargoRouteService->syncFinalStop($route);

        return redirect()->route('cargo_routes.index')->with('success', 'Kituo cha njiani kimeongezwa.');
    }

    public function destroyStop($routeId, $stopId)
    {
        $companyId = auth()->user()->company_id;

        $route = CargoRoute::where('company_id', $companyId)->findOrFail($routeId);

        $stop = CargoRouteStop::where('cargo_route_id', $route->id)
            ->where('id', $stopId)
            ->firstOrFail();

        if ($stop->is_final) {
            return redirect()->route('cargo_routes.index')
                ->withErrors(['stop' => 'Huwezi kufuta kituo cha mwisho cha route.']);
        }

        $stop->delete();

        return redirect()->route('cargo_routes.index')->with('success', 'Kituo kimefutwa.');
    }

    public function destroy($id)
    {
        $companyId = auth()->user()->company_id;

        $route = CargoRoute::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if ($route) {
            CargoRouteStop::where('cargo_route_id', $route->id)->delete();
            $route->delete();
        }

        return redirect()->route('cargo_routes.index')->with('success', 'Route imefutwa.');
    }
}
