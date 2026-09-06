<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SEED_ADMIN_EMAIL', 'admin@ragasoftware.ir');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('SEED_ADMIN_NAME', 'مدیر سایت'),
                'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'password')),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles(['super-admin']);
    }
}
