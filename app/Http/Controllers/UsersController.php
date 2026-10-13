<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Role;
use App\Models\Designation;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles')
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('manage.users.index', compact('users'));
    }

    public function users_all()
    {
        $users = User::with('roles')
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('manage.users.all_users', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = $this->getAssignableRoles();

        if ($roles->isEmpty()) {
            return redirect(route('roles.index'))
                ->with('error', 'Create a role and assign permissions before adding users.');
        }

        return view('manage.users.add', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $request->validate([
            'name' => 'required|max:255|min:3|string',
            'email' => 'required|string|min:3|unique:users',
            'phone' => 'required|not_in:0|min:9',
            'password' => 'required|min:6|confirmed',
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
        ]);

        $role = $this->resolveAssignableRole((int) $request->role_id);
        if (!$role) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The selected role is not available.');
        }

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'phone' => $request['phone'],
            'added_by' => auth()->user()->id,
            'company_id' => $companyId,
            'status' => 1,
            'is_active' => 1,
        ]);

        $user->roles()->sync([$role->id]);

        return redirect(route('users.index'))
            ->with('success', 'User created and assigned role: ' . $role->slug);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = User::all();

        return view('manage.users.index2', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roles = $this->getAssignableRoles();
        $users = User::with('roles')
            ->where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();
        $user = collect([$users]);
        $userRole = $users->roles->first();
        $userRoleId = $userRole ? $userRole->id : null;

        return view('manage.users.edit', compact('user', 'roles', 'userRoleId'));
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
        $companyId = auth()->user()->company_id;

        $user = User::where('id', $id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|max:255|min:3|string',
            'email' => 'required|string|min:3|unique:users,email,' . $user->id,
            'phone' => 'required|not_in:0|min:9',
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
        ]);

        $role = $this->resolveAssignableRole((int) $request->role_id);
        if (!$role) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The selected role is not available.');
        }

        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->phone = $request['phone'];
        $user->save();

        $user->roles()->sync([$role->id]);

        return redirect(route('users.index'))
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        if ((int) $user->id === (int) auth()->id()) {
            return redirect(route('users.index'))
                ->with('error', 'You cannot delete your own account.');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect(route('users.index'))->with('success', 'User deleted successfully');
    }

    public function findDepartment(Request $request)
    {
        $district = Designation::where('department_id', $request->id)->get();

        return response()->json($district);
    }

    /**
     * Roles the current user may assign when creating/editing users.
     */
    private function getAssignableRoles()
    {
        $query = Role::where('company_id', auth()->user()->company_id);

        if (!auth()->user()->hasRole('superadmin')) {
            $query->where('slug', '!=', 'superadmin');
        }

        return $query->orderBy('slug')->get();
    }

    /**
     * Resolve and authorize a role for assignment.
     */
    private function resolveAssignableRole(int $roleId): ?Role
    {
        $role = Role::where('id', $roleId)
            ->where('company_id', auth()->user()->company_id)
            ->first();

        if (!$role) {
            return null;
        }

        if ($role->slug === 'superadmin' && !auth()->user()->hasRole('superadmin')) {
            return null;
        }

        return $role;
    }
}
