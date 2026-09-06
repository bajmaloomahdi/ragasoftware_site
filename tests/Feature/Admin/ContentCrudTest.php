<?php

namespace Tests\Feature\Admin;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_is_created_with_features_and_seo(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'title' => 'سیستم ERP راگا',
            'status' => 'published',
            'summary' => 'راهکار یکپارچه مدیریت منابع سازمانی',
            'body' => '<p>متن <script>alert(1)</script>محصول</p>',
            'sort_order' => 0,
            'featured' => true,
            'features' => [
                ['title' => 'حسابداری', 'description' => 'دفتر کل و معین'],
                ['title' => 'انبار', 'description' => 'مدیریت موجودی'],
            ],
            'seo' => ['meta_title' => 'ERP سازمانی راگا', 'no_index' => false],
        ])->assertRedirect();

        $product = Product::sole();

        $this->assertSame('سیستم-erp-راگا', $product->slug);
        $this->assertStringNotContainsString('<script>', (string) $product->body);
        $this->assertCount(2, $product->features);
        $this->assertSame('ERP سازمانی راگا', $product->seo->meta_title);
        $this->assertNotNull($product->published_at);
    }

    public function test_page_stores_ordered_sections_and_sanitizes_unknown_keys(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'درباره ما',
            'template' => 'default',
            'status' => 'published',
            'show_in_sitemap' => true,
            'sort_order' => 0,
            'sections' => [
                ['type' => 'hero', 'name' => 'سربرگ', 'is_active' => true, 'sort_order' => 0, 'settings' => [
                    'heading' => 'درباره راگا سافت‌ور',
                    'bogus_key' => 'should be dropped',
                ]],
                ['type' => 'rich_text', 'name' => 'متن', 'is_active' => true, 'sort_order' => 1, 'settings' => [
                    'body' => '<p>سلام</p>',
                ]],
            ],
        ])->assertRedirect();

        $page = Page::where('slug', 'درباره-ما')->sole();
        $sections = $page->sections()->orderBy('sort_order')->get();

        $this->assertCount(2, $sections);
        $this->assertSame('hero', $sections[0]->type);
        $this->assertArrayNotHasKey('bogus_key', $sections[0]->settings);
        $this->assertSame('درباره راگا سافت‌ور', $sections[0]->settings['heading']);
    }

    public function test_scheduled_post_is_not_published_yet(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'مقاله زمان‌بندی‌شده',
            'status' => 'scheduled',
            'published_at' => now()->addWeek()->format('Y-m-d H:i:s'),
        ])->assertRedirect();

        $post = BlogPost::withoutGlobalScopes()->sole();

        $this->assertSame('scheduled', $post->status);
        $this->assertSame(0, BlogPost::published()->count());
    }

    public function test_editor_cannot_touch_settings(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)->get(route('admin.settings.edit'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.products.index'))->assertOk();
    }
}
