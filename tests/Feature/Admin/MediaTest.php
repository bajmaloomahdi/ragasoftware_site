<?php

namespace Tests\Feature\Admin;

use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_generates_responsive_and_webp_variants(): void
    {
        Storage::fake('uploads');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.media.store'), [
                'files' => [UploadedFile::fake()->image('hero.jpg', 1600, 1000)],
            ])
            ->assertRedirect();

        $media = Media::sole();

        $this->assertSame('image/jpeg', $media->mime_type);
        $this->assertSame(1600, $media->width);
        $this->assertArrayHasKey('thumb', $media->variants);
        $this->assertArrayHasKey('lg', $media->variants);
        $this->assertArrayHasKey('full', $media->variants['webp']);

        Storage::disk('uploads')->assertExists($media->path);
        Storage::disk('uploads')->assertExists($media->variants['thumb']);
        Storage::disk('uploads')->assertExists($media->variants['webp']['thumb']);
    }

    public function test_upload_rejects_disallowed_type(): void
    {
        Storage::fake('uploads');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.media.store'), [
                'files' => [UploadedFile::fake()->create('malware.exe', 10, 'application/octet-stream')],
            ])
            ->assertSessionHasErrors('files.0');

        $this->assertSame(0, Media::count());
    }

    public function test_deleting_media_removes_files(): void
    {
        Storage::fake('uploads');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.media.store'), [
            'files' => [UploadedFile::fake()->image('a.png', 900, 600)],
        ]);

        $media = Media::sole();
        $path = $media->path;

        $this->actingAs($admin)->delete(route('admin.media.destroy', $media))->assertRedirect();

        $this->assertSame(0, Media::count());
        Storage::disk('uploads')->assertMissing($path);
    }

    public function test_editor_without_delete_permission_cannot_delete(): void
    {
        Storage::fake('uploads');
        $editor = $this->editor();

        $this->actingAs($editor)->post(route('admin.media.store'), [
            'files' => [UploadedFile::fake()->image('a.png', 400, 300)],
        ])->assertRedirect();

        $media = Media::sole();

        $this->actingAs($editor)->delete(route('admin.media.destroy', $media))->assertForbidden();
        $this->assertSame(1, Media::count());
    }
}
