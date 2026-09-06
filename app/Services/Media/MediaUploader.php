<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

/**
 * Stores an uploaded file into the "uploads" disk (which is public/uploads,
 * so no storage:link symlink is needed) and generates responsive raster
 * variants + WebP siblings for images.
 *
 * The database only ever holds metadata + relative paths — never file blobs.
 */
class MediaUploader
{
    private ImageManager $images;

    public function __construct()
    {
        $this->images = new ImageManager(new Driver());
    }

    public function store(UploadedFile $file, ?int $folderId = null, ?int $userId = null): Media
    {
        $disk = config('cms.media.disk', 'uploads');
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $base = now()->format('Y/m').'/'.Str::ulid();
        $path = "{$base}.{$ext}";

        Storage::disk($disk)->putFileAs(dirname($path), $file, basename($path));

        $media = new Media([
            'folder_id' => $folderId,
            'disk' => $disk,
            'path' => $path,
            'filename' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType() ?: $file->getMimeType(),
            'extension' => $ext,
            'size' => $file->getSize(),
            'uploaded_by' => $userId,
            'variants' => [],
        ]);

        if ($media->isImage() && ! $media->isSvg()) {
            [$width, $height, $variants] = $this->makeImageVariants($disk, $base, $ext, $path);
            $media->width = $width;
            $media->height = $height;
            $media->variants = $variants;
        }

        $media->save();

        return $media;
    }

    /**
     * @return array{0:int,1:int,2:array<string,mixed>}
     */
    private function makeImageVariants(string $disk, string $base, string $ext, string $originalPath): array
    {
        $diskDriver = Storage::disk($disk);
        $absOriginal = $diskDriver->path($originalPath);

        $probe = $this->images->decodePath($absOriginal);
        $ownWidth = $probe->width();
        $ownHeight = $probe->height();

        $webpQuality = (int) config('cms.media.webp_quality', 78);
        $jpegQuality = (int) config('cms.media.jpeg_quality', 82);

        $variants = ['webp' => []];

        // Full-size WebP sibling of the original
        $fullWebp = "{$base}.webp";
        $diskDriver->put($fullWebp, (string) $probe->encode(new WebpEncoder(quality: $webpQuality)));
        $variants['webp']['full'] = $fullWebp;
        unset($probe);

        foreach (config('cms.media.variants', []) as $name => $targetWidth) {
            if ($ownWidth <= $targetWidth && $name !== 'thumb') {
                continue; // never upscale (thumb always produced for the grid)
            }

            // decode fresh per variant — v4 Image is stateful and not safely cloneable
            $img = $this->images->decodePath($absOriginal)->scaleDown(width: $targetWidth);

            $rasterPath = "{$base}-{$name}.{$ext}";
            $img->save($diskDriver->path($rasterPath), quality: $jpegQuality);
            $variants[$name] = $rasterPath;

            $webpPath = "{$base}-{$name}.webp";
            $diskDriver->put($webpPath, (string) $img->encode(new WebpEncoder(quality: $webpQuality)));
            $variants['webp'][$name] = $webpPath;
            unset($img);
        }

        return [$ownWidth, $ownHeight, $variants];
    }

    /** Removes the original + every generated variant from disk. */
    public function delete(Media $media): void
    {
        $disk = Storage::disk($media->disk);
        $paths = [$media->path];

        foreach ((array) $media->variants as $key => $value) {
            if ($key === 'webp' && is_array($value)) {
                $paths = array_merge($paths, array_values($value));
            } elseif (is_string($value)) {
                $paths[] = $value;
            }
        }

        $disk->delete(array_values(array_unique($paths)));
    }
}
