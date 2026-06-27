<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Hapus method __construct() yang menggunakan middleware()
    // Atau jika ingin tetap pakai, gunakan cara ini:
    
    // Display list of users
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }
    
    // Show create user form
    public function create()
    {
        return view('admin.users.create');
    }
    
    // Store new user
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users|alpha_dash',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'avatar' => 'nullable|image|max:2048',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $data = $request->only(['username', 'name', 'email', 'role']);
        $data['password'] = Hash::make($request->password);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();
            $avatar->move(public_path('uploads/avatars'), $filename);
            $data['avatar'] = 'uploads/avatars/' . $filename;
        }
        
        $user = User::create($data);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $user->name . '" created successfully!');
    }
    
    // Show edit user form
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }
    
    // Update user
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|alpha_dash|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'avatar' => 'nullable|image|max:2048',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $data = $request->only(['username', 'name', 'email', 'role']);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();
            $avatar->move(public_path('uploads/avatars'), $filename);
            $data['avatar'] = 'uploads/avatars/' . $filename;
        }
        
        $user->update($data);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $user->name . '" updated successfully!');
    }
    
    // Reset user password
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Password for "' . $user->name . '" has been reset!');
    }
    
    // Delete user (soft delete)
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }
        
        $userName = $user->name;
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $userName . '" deleted successfully!');
    }
    
    // Show trashed users
    public function trashed()
    {
        $users = User::onlyTrashed()->paginate(15);
        return view('admin.users.trashed', compact('users'));
    }
    
    // Restore user
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $user->name . '" restored successfully!');
    }
    
    // Force delete user
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $userName = $user->name;
        
        // Delete avatar if exists
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }
        
        $user->forceDelete();
        
        return redirect()->route('admin.users.trashed')
            ->with('success', 'User "' . $userName . '" permanently deleted!');
    }
}