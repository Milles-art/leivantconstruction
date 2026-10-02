<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('provider')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'providers' => Provider::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => ['required', Rule::in(['admin', 'provider'])],
            'role' => ['required', Rule::in(['super_admin', 'manager', 'content', 'sales', 'provider'])],
            'provider_id' => ['nullable', 'exists:providers,id'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $validated['account_type'] = $validated['role'] === 'provider' ? 'provider' : 'admin';
        $validated['is_admin'] = $validated['role'] !== 'provider' || $request->boolean('is_admin');

        // Privileged fields are not mass-assignable (see User::$fillable).
        $user = User::query()->create($request->only(['name', 'email', 'phone', 'password']));
        $user->forceFill([
            'account_type' => $validated['account_type'],
            'role' => $validated['role'],
            'provider_id' => $validated['provider_id'] ?? null,
            'is_admin' => $validated['is_admin'],
        ])->save();
        ActivityLog::record('user.created', 'Created user '.$user->email, $user);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'providers' => Provider::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'account_type' => ['required', Rule::in(['admin', 'provider'])],
            'role' => ['required', Rule::in(['super_admin', 'manager', 'content', 'sales', 'provider'])],
            'provider_id' => ['nullable', 'exists:providers,id'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $validated['account_type'] = $validated['role'] === 'provider' ? 'provider' : 'admin';
        $validated['is_admin'] = $validated['role'] !== 'provider' || $request->boolean('is_admin');

        if ($user->id === auth()->id() && ! $validated['is_admin']) {
            return back()->with('error', 'You cannot remove your own admin access.');
        }

        if (! $validated['is_admin'] && User::query()->where('is_admin', true)->whereKeyNot($user->id)->count() === 0) {
            return back()->with('error', 'At least one admin user must remain.');
        }

        // Privileged fields are not mass-assignable (see User::$fillable).
        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'account_type' => $validated['account_type'],
            'role' => $validated['role'],
            'provider_id' => $validated['provider_id'] ?? null,
            'is_admin' => $validated['is_admin'],
        ]);
        if (! blank($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }
        $user->save();
        ActivityLog::record('user.updated', 'Updated user '.$user->email, $user);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

}
