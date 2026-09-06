<?php

namespace Tests\Feature\Public;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Product;
use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RealisticContentSeeder::class);
    }

    public function test_homepage_has_one_h1_and_json_ld(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertStringContainsString('"@type":"Organization"', str_replace(' ', '', $html));
        $this->assertStringContainsString('"@type":"WebSite"', str_replace(' ', '', $html));
    }

    public function test_content_is_present_without_javascript(): void
    {
        $html = $this->get('/')->getContent();

        // real marketing copy, server-rendered
        $this->assertStringContainsString('سازمان‌تان را یکپارچه', $html);
        $this->assertStringContainsString('چرا راگا سافت‌ور', $html);
    }

    public function test_sitemap_lists_published_urls_only(): void
    {
        $draft = Product::published()->first();
        $draft->update(['status' => 'draft']);

        $xml = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=utf-8')
            ->getContent();

        $this->assertStringContainsString('<loc>'.url('/blog').'</loc>', $xml);
        $this->assertStringNotContainsString($draft->publicPath(), $xml);
    }

    public function test_robots_disallows_admin_and_points_to_sitemap(): void
    {
        $body = $this->get('/robots.txt')->assertOk()->getContent();

        $this->assertStringContainsString('Disallow: /admin', $body);
        $this->assertStringContainsString('Sitemap: '.url('/sitemap.xml'), $body);
    }

    public function test_admin_pages_carry_noindex(): void
    {
        $this->get('/admin/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_draft_page_is_404_on_the_public_site(): void
    {
        Page::create([
            'title' => 'صفحه مخفی', 'slug' => 'hidden-page', 'template' => 'default',
            'status' => 'draft',
        ]);

        $this->get('/hidden-page')->assertNotFound();
    }

    public function test_renamed_slug_creates_a_301_redirect(): void
    {
        $product = Product::published()->first();
        $old = $product->publicPath();

        $product->update(['slug' => $product->slug.'-v2']);

        $this->assertDatabaseHas('redirects', ['from_path' => $old, 'status_code' => 301]);

        $this->get($old)->assertRedirect($product->publicPath());
    }

    public function test_scheduled_post_publishes_via_command(): void
    {
        $post = BlogPost::withoutGlobalScopes()->first();
        $post->update(['status' => 'scheduled', 'published_at' => now()->subMinute()]);

        $this->artisan('content:publish-scheduled')->assertSuccessful();

        $this->assertSame('published', $post->fresh()->status);
    }
}
