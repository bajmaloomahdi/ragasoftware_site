<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_area_sends_noindex_header(): void
    {
        $this->get('/admin/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_user_can_log_in(): void
    {
        $this->seedRolesAndPermissions();
        $user = User::factory()->create(['password' => Hash::make('secret-pass')]);
        $user->assignRole('admin');

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'secret-pass',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $this->seedRolesAndPermissions();
        $user = User::factory()->create([
            'password' => Hash::make('secret-pass'),
            'is_active' => false,
        ]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'secret-pass',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_editor_cannot_reach_settings_only_routes_are_gated(): void
    {
        $editor = $this->editor();

        $this->assertFalse($editor->can('settings.manage'));
        $this->assertTrue($editor->can('blog.manage'));
    }
}
