@php($editing = isset($user))
@php
    $roleHelp = [
        'super_admin' => 'Full access: users, settings, inbox, projects, marketplace, providers, orders, and activity logs.',
        'manager' => 'Operations access: inbox, inquiries, projects, catalog, providers, reviews, and orders.',
        'sales' => 'Client desk access: inbox, inquiries, projects, and reviews.',
        'content' => 'Content access: services, products, providers, regions, categories, and reviews.',
        'provider' => 'Provider account only. No admin dashboard unless admin access is checked.',
    ];
@endphp

<form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="admin-card grid gap-5 p-6">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif
    <div class="grid gap-5 md:grid-cols-2">
        <label>
            <span class="mb-2 block text-sm font-bold">Name</span>
            <input class="vant-input min-h-14 w-full" name="name" value="{{ old('name', $user->name ?? '') }}" required>
        </label>
        <label>
            <span class="mb-2 block text-sm font-bold">Email</span>
            <input class="vant-input min-h-14 w-full" type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
        </label>
    </div>
    <div class="grid gap-5 md:grid-cols-3">
        <label>
            <span class="mb-2 block text-sm font-bold">Phone</span>
            <input class="vant-input min-h-14 w-full" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
        </label>
        <label>
            <span class="mb-2 block text-sm font-bold">Role and permissions</span>
            <select class="vant-input min-h-14 w-full" name="role" required>
                @foreach ([
                    'super_admin' => 'Super Admin',
                    'manager' => 'Operations Manager',
                    'sales' => 'Sales / Client Desk',
                    'content' => 'Content Manager',
                    'provider' => 'Provider Account',
                ] as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $user->role ?? (($user->is_admin ?? false) ? 'super_admin' : 'provider')) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="mb-2 block text-sm font-bold">Account Type</span>
            <select class="vant-input min-h-14 w-full" name="account_type" required>
                @foreach (['admin' => 'Admin', 'provider' => 'Provider'] as $value => $label)
                <option value="{{ $value }}" @selected(old('account_type', isset($user) && $user->is_admin ? 'admin' : ($user->account_type ?? 'provider')) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="mb-2 block text-sm font-bold">Linked Provider</span>
            <select class="vant-input min-h-14 w-full" name="provider_id">
                <option value="">None</option>
                @foreach ($providers as $provider)
                    <option value="{{ $provider->id }}" @selected(old('provider_id', $user->provider_id ?? null) == $provider->id)>{{ $provider->name }}</option>
                @endforeach
            </select>
        </label>
    </div>
    <div class="grid gap-3 md:grid-cols-5">
        @foreach ($roleHelp as $role => $description)
            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3">
                <p class="text-xs font-black uppercase tracking-wide text-zinc-900">{{ str_replace('_', ' ', $role) }}</p>
                <p class="mt-1 text-xs leading-5 text-zinc-600">{{ $description }}</p>
            </div>
        @endforeach
    </div>
    <div class="grid gap-5 md:grid-cols-2">
        <label>
            <span class="mb-2 block text-sm font-bold">Password {{ $editing ? '(leave blank to keep)' : '' }}</span>
            <input class="vant-input min-h-14 w-full" type="password" name="password" @required(! $editing)>
        </label>
        <label>
            <span class="mb-2 block text-sm font-bold">Confirm Password</span>
            <input class="vant-input min-h-14 w-full" type="password" name="password_confirmation" @required(! $editing)>
        </label>
    </div>
    <label class="flex min-h-14 items-center gap-3 rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm font-bold">
        <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin ?? false))>
        Allow this person to access the admin dashboard
    </label>
    <div class="flex gap-3">
        <button class="vant-button" type="submit">{{ $editing ? 'Update User' : 'Create User' }}</button>
        <a href="{{ route('admin.users.index') }}" class="vant-button-outline">Cancel</a>
    </div>
</form>