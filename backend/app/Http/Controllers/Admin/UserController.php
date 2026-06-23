<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display users and admins separately (optional use in view)
     */
    public function index()
    {
        // Paginate users so the view can render links()
        $usersQuery = User::where('role', 'user')->latest();
        $adminsQuery = User::where('role', 'admin')->latest();

        if (auth()->check()) {
            $usersQuery->where('id', '!=', auth()->id());
            $adminsQuery->where('id', '!=', auth()->id());
        }

        $users = $usersQuery->paginate(15);
        $admins = $adminsQuery->get();

        return view('admin.users.index', compact('users', 'admins'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store NEW USER (FORCE ROLE = USER)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'avatar' => 'nullable|image',
            'is_active' => 'nullable',
            'role' => 'nullable|in:user,admin,staff',
        ]);

        // Allow assigning role only when current user is admin
        $requestedRole = $request->input('role', 'user');
        $role = auth()->user() && auth()->user()->role === 'user' ? $requestedRole : 'staff';

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Edit user
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user (ROLE PROTECTED)
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image',
            'password' => 'nullable|min:6',
            'is_active' => 'nullable',
            'role' => 'nullable|in:user,admin,staff',
        ]);

        // Preserve role unless current user is admin and provided a role
        $role = $user->role;
        if (auth()->user() && auth()->user()->role === 'user' && $request->filled('role')) {
            $role = $request->role;
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Delete user
     */
    public function destroy(string $id)
    {
        User::findOrFail($id)->delete();

        return back()->with('success', 'User deleted successfully');
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
        ]);

        User::whereIn('id', $request->users)->delete();

        return back()->with('success', 'Selected users deleted');
    }
}