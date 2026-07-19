<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Http\Requests\UserRequest;
use App\Services\AccountDeletionService;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'customer'));

        if ($request->has('trashed')) {
            $query->onlyTrashed();
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'customer')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->role);

        // Sync with Customers table
        \App\Models\Customer::where('phone', $user->phone)->update(['user_id' => $user->id]);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'کاربر جدید با موفقیت ایجاد شد.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        
        // Remove role from data to prevent it from being passed to update()
        // as we use syncRoles() for roles management via Spatie
        unset($data['role']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        
        // Sync roles (multiple roles)
        if ($request->has('role') && ! $user->isSuperAdmin()) {
            $user->syncRoles($request->role);
        }

        // Sync with Customers table - if phone changed, we need to handle it
        // First, clear user_id for customers who had the old phone (if it changed)
        // Then, set user_id for customers who have the new phone
        \App\Models\Customer::where('user_id', $user->id)->update(['user_id' => null]);
        \App\Models\Customer::where('phone', $user->phone)->update(['user_id' => $user->id]);

        return redirect()->route('super-admin.users.show', $user)
            ->with('success', 'اطلاعات کاربر با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting super admin or current user
        if ($user->isSuperAdmin() || $user->id === Auth::id()) {
            return redirect()->route('super-admin.users.index')
                ->with('error', 'نمی‌توانید این کاربر را حذف کنید.');
        }

        app(AccountDeletionService::class)->softDeleteUser($user);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'کاربر و سوابق مرتبط با موفقیت حذف شد.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('super-admin.users.index')
                ->with('error', 'شما نمی‌توانید وضعیت خود را تغییر دهید.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'وضعیت کاربر با موفقیت تغییر کرد.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('super-admin.users.index', ['trashed' => 1])
            ->with('success', 'کاربر با موفقیت بازیابی شد.');
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Prevent deleting super admin or current user
        if ($user->isSuperAdmin() || $user->id === Auth::id()) {
            return redirect()->route('super-admin.users.index', ['trashed' => 1])
                ->with('error', 'نمی‌توانید این کاربر را برای همیشه حذف کنید.');
        }

        app(AccountDeletionService::class)->forceDeleteUser($user);

        return redirect()->route('super-admin.users.index', ['trashed' => 1])
            ->with('success', 'کاربر و سوابق مرتبط برای همیشه از دیتابیس حذف شد.');
    }
}
