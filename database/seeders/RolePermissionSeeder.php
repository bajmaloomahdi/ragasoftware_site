<?php

namespace Database\Seeders;

use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permissions::all() as $name) {
            Permission::findOrCreate($name, 'web');
        }

        // super-admin: everything (also short-circuited by a Gate::before)
        Role::findOrCreate('super-admin', 'web')->syncPermissions(Permissions::all());

        // admin: full CMS control
        Role::findOrCreate('admin', 'web')->syncPermissions(Permissions::all());

        // editor: all content, no site config / users
        Role::findOrCreate('editor', 'web')->syncPermissions(Permissions::editorBundle());

        // author: blog + own media
        Role::findOrCreate('author', 'web')->syncPermissions(Permissions::authorBundle());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
