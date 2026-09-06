<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function seedRolesAndPermissions(): void
    {
        $this->seed(RolePermissionSeeder::class);
    }

    protected function admin(): User
    {
        $this->seedRolesAndPermissions();

        return User::factory()->create()->assignRole('super-admin');
    }

    protected function editor(): User
    {
        $this->seedRolesAndPermissions();

        return User::factory()->create()->assignRole('editor');
    }
}
