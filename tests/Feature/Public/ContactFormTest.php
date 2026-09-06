<?php

namespace Tests\Feature\Public;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RealisticContentSeeder::class);
    }

    public function test_contact_form_stores_a_message(): void
    {
        $this->post('/contact', [
            'name' => 'رضا محمدی',
            'phone' => '09120000000',
            'email' => 'reza@example.com',
            'company' => 'شرکت نمونه',
            'subject' => 'درخواست دمو',
            'message' => 'لطفاً برای دموی ERP با من تماس بگیرید.',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'reza@example.com',
            'status' => 'new',
        ]);
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post('/contact', [
            'name' => 'Bot',
            'message' => 'spam',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_contact_form_requires_name_and_message(): void
    {
        $this->post('/contact', [])->assertSessionHasErrors(['name', 'message']);
    }

    public function test_contact_form_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact', ['name' => "u{$i}", 'message' => 'hi']);
        }

        $this->post('/contact', ['name' => 'over', 'message' => 'limit'])->assertStatus(429);
    }

    public function test_no_marketing_copy_is_hardcoded_in_public_blade(): void
    {
        // The homepage hero copy must come from the DB, not a Blade file.
        $needle = 'سازمان‌تان را یکپارچه و هوشمند مدیریت کنید';

        $hits = [];
        foreach (Route::getRoutes()->getRoutes() as $r) {
            //
        }

        $files = collect(\Illuminate\Support\Facades\File::allFiles(resource_path('views')))
            ->filter(fn ($f) => str_ends_with($f->getFilename(), '.blade.php'))
            ->filter(fn ($f) => str_contains($f->getContents(), $needle));

        $this->assertCount(0, $files, 'Hero copy found hardcoded in: '.$files->map->getRelativePathname()->implode(', '));

        // …but it IS rendered on the page (from the seeded page_sections row).
        $this->get('/')->assertSee($needle, false);
    }
}
