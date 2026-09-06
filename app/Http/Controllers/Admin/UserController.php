<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->with('roles:id,name')
            ->when($request->string('search')->toString(), fn (Builder $q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'rows' => $users,
            'filters' => $request->only('search'),
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_active' => ['boolean'],
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);
        $user->syncRoles($data['roles'] ?? []);

        return back()->with('success', 'کاربر ایجاد شد.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_active' => ['boolean'],
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? $user->is_active,
        ]);
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        // never let the last super-admin lock themselves out
        if (! ($user->is($request->user()) && ! in_array('super-admin', $data['roles'] ?? [], true))) {
            $user->syncRoles($data['roles'] ?? []);
        }

        return back()->with('success', 'کاربر به‌روزرسانی شد.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 403, 'حذف حساب خودتان ممکن نیست.');

        $user->delete();

        return back()->with('success', 'کاربر حذف شد.');
    }

    // ---- Roles ----

    public function roles(): Response
    {
        return Inertia::render('Users/Roles', [
            'roles' => Role::with('permissions:id,name')->orderBy('name')->get()
                ->map(fn (Role $r) => [
                    'id' => $r->id,
                    'name' => $r->name,
                    'permissions' => $r->permissions->pluck('name'),
                    'locked' => in_array($r->name, ['super-admin'], true),
                ]),
            'groups' => Permissions::groups(),
        ]);
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::in(Permissions::all())],
        ]);

        Role::create(['name' => $data['name'], 'guard_name' => 'web'])
            ->syncPermissions($data['permissions'] ?? []);

        return back()->with('success', 'نقش ایجاد شد.');
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->name === 'super-admin', 403, 'نقش سوپرادمین قابل تغییر نیست.');

        $data = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::in(Permissions::all())],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return back()->with('success', 'دسترسی‌های نقش ذخیره شد.');
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        abort_if(in_array($role->name, ['super-admin', 'admin'], true), 403);
        abort_if($role->users()->exists(), 422, 'ابتدا کاربران این نقش را جابه‌جا کنید.');

        $role->delete();

        return back()->with('success', 'نقش حذف شد.');
    }
}
