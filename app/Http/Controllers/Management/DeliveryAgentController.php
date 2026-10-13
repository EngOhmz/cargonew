<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Mikoa;
use App\Models\Role;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DeliveryAgentController extends Controller
{
    /**
     * Default source region for all delivery agents: first mikoa for company (by name).
     */
    protected function defaultSourceMikoaIdForCompany(?int $companyId): ?int
    {
        if (!$companyId) {
            return null;
        }

        return Mikoa::where('company_id', $companyId)->orderBy('regional_name')->value('id');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DeliveryAgent::with(['sourceMikoa', 'destinationMikoa'])
                ->where('company_id', auth()->user()->company_id)
                ->orderBy('created_at', 'desc');

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('source_region', function ($row) {
                    return $row->sourceMikoa ? e($row->sourceMikoa->regional_name) : '<span class="text-muted">—</span>';
                })
                ->addColumn('destination_region', function ($row) {
                    return $row->destinationMikoa ? e($row->destinationMikoa->regional_name) : '<span class="text-muted">—</span>';
                })
                ->addColumn('status_label', function ($row) {
                    if ($row->is_approved) {
                        return '<span class="badge badge-success">Approved</span>';
                    }

                    return '<span class="badge badge-warning">Pending Approve</span>';
                })
                ->addColumn('plain_password', function ($row) {
                    if ($row->is_approved) {
                        $user = User::where('email', $row->username)->first();

                        return $user ? $user->plain_password : '-';
                    }

                    return '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item text-teal dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-cog6"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">';

                    if (!$row->is_approved) {
                        $btn .= '<a href="' . route('delivery_agents.approve', $row->id) . '" class="dropdown-item">
                                    <i class="icon-check mr-2"></i> Approve
                                 </a>';
                    }

                    $btn .= '<a href="' . route('delivery_agents.edit', $row->id) . '" class="dropdown-item">
                                <i class="icon-pencil mr-2"></i> Edit
                             </a>
                             <a href="#" class="dropdown-item text-danger">
                                <i class="icon-trash mr-2"></i> Delete
                             </a>
                                    </div>
                                </div>
                            </div>';

                    return $btn;
                })
                ->rawColumns(['status_label', 'action', 'source_region', 'destination_region'])
                ->make(true);
        }

        return view('management.delivery_agents.index');
    }

    public function create()
    {
        $mikoa = Mikoa::where('company_id', auth()->user()->company_id)
            ->orderBy('regional_name')
            ->get();

        return view('management.delivery_agents.create', compact('mikoa'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:delivery_agents,username',
            'phone' => 'required|string|max:20',
            'destination_mikoa_id' => [
                'nullable',
                'integer',
                Rule::exists('mikoa', 'id')->where('company_id', $companyId),
            ],
        ]);

        $sourceId = $this->defaultSourceMikoaIdForCompany($companyId);

        DeliveryAgent::create([
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->phone,
            'company_id' => $companyId,
            'source_mikoa_id' => $sourceId,
            'destination_mikoa_id' => $request->filled('destination_mikoa_id') ? (int) $request->destination_mikoa_id : null,
            'added_by' => auth()->user()->id,
            'is_approved' => false,
        ]);

        return redirect()->route('delivery_agents.index')->with('success', 'Delivery Agent registered successfully and pending approval.');
    }

    public function edit($id)
    {
        $agent = DeliveryAgent::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $mikoa = Mikoa::where('company_id', auth()->user()->company_id)
            ->orderBy('regional_name')
            ->get();

        return view('management.delivery_agents.edit', compact('agent', 'mikoa'));
    }

    public function update(Request $request, $id)
    {
        $companyId = auth()->user()->company_id;
        $agent = DeliveryAgent::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('delivery_agents', 'username')->ignore($agent->id)],
            'phone' => 'required|string|max:20',
            'destination_mikoa_id' => [
                'nullable',
                'integer',
                Rule::exists('mikoa', 'id')->where('company_id', $companyId),
            ],
        ]);

        $sourceId = $agent->source_mikoa_id ?? $this->defaultSourceMikoaIdForCompany($companyId);

        $agent->update([
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->phone,
            'source_mikoa_id' => $sourceId,
            'destination_mikoa_id' => $request->filled('destination_mikoa_id') ? (int) $request->destination_mikoa_id : null,
        ]);

        return redirect()->route('delivery_agents.index')->with('success', 'Delivery Agent updated successfully.');
    }

    public function approve($id)
    {
        $agent = DeliveryAgent::where('company_id', auth()->user()->company_id)->findOrFail($id);

        DB::beginTransaction();
        try {
            $plainPassword = mt_rand(100000, 999999);

            $user = User::updateOrCreate(
                ['email' => $agent->username],
                [
                    'name' => $agent->name,
                    'phone' => $agent->phone,
                    'company_id' => $agent->company_id,
                    'password' => Hash::make($plainPassword),
                    'plain_password' => $plainPassword,
                    'is_approved' => true,
                    'is_delivery_agent' => true,
                    'status' => 1,
                    'added_by' => auth()->user()->id,
                ]
            );

            $deliveryRole = Role::where('company_id', $agent->company_id)
                ->where('slug', 'delivery-agent')
                ->first();

            if (!$deliveryRole) {
                $deliveryRole = Role::create([
                    'slug' => 'delivery-agent',
                    'company_id' => $agent->company_id,
                    'added_by' => auth()->user()->id,
                ]);
            }

            if (!$user->roles()->where('roles.id', $deliveryRole->id)->exists()) {
                $user->roles()->attach($deliveryRole->id);
            }

            $agent->update(['is_approved' => true]);

            DB::commit();

            return redirect()->route('delivery_agents.index')->with('success', 'Delivery Agent approved. Password: ' . $plainPassword);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('delivery_agents.index')->with('error', 'Error approving agent: ' . $e->getMessage());
        }
    }
}
