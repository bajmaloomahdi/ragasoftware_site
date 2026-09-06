<?php

namespace Database\Seeders\Support;

use App\Models\Media;
use Illuminate\Support\Str;

/**
 * Generates lightweight branded SVG placeholders under public/uploads/seed/
 * and registers them as Media rows, so seeded content has real (committed,
 * tiny) images without shipping binary assets or running the uploader.
 */
class SeedMedia
{
    public static function make(string $key, string $label, string $variant = 'card'): Media
    {
        // Keyed on original_name; safe to call repeatedly (idempotent seeder).
        if ($existing = Media::where('original_name', $key.'.svg')->first()) {
            return $existing;
        }

        [$w, $h] = match ($variant) {
            'mockup' => [1200, 750],
            'cover' => [1200, 675],
            'square' => [600, 600],
            'logo' => [220, 90],
            'og' => [1200, 630],
            default => [800, 600],
        };

        $svg = self::svg($label, $w, $h, $variant);
        $rel = "seed/{$key}.svg";
        $abs = public_path("uploads/{$rel}");
        @mkdir(dirname($abs), 0775, true);
        file_put_contents($abs, $svg);

        return Media::create([
            'disk' => 'uploads',
            'path' => $rel,
            'filename' => "{$key}.svg",
            'original_name' => "{$key}.svg",
            'mime_type' => 'image/svg+xml',
            'extension' => 'svg',
            'size' => strlen($svg),
            'width' => $w,
            'height' => $h,
            'alt_text' => $label,
            'title' => $label,
            'variants' => [],
        ]);
    }

    private static function svg(string $label, int $w, int $h, string $variant): string
    {
        $palettes = [
            ['#0b1b34', '#12294d', '#2563eb'],
            ['#12294d', '#1b3785', '#06b6d4'],
            ['#0b1b34', '#1d4fd0', '#38bdf8'],
        ];
        [$c1, $c2, $accent] = $palettes[abs(crc32($label)) % count($palettes)];
        $id = Str::random(6);
        $safe = htmlspecialchars($label, ENT_QUOTES);
        $fontSize = $variant === 'logo' ? 26 : ($variant === 'square' ? 34 : 44);

        $decor = $variant === 'logo' ? '' : <<<SVG
            <circle cx="{$w}" cy="0" r="220" fill="{$accent}" opacity="0.18"/>
            <circle cx="0" cy="{$h}" r="180" fill="#ffffff" opacity="0.06"/>
            <rect x="8%" y="62%" width="52%" height="8" rx="4" fill="#ffffff" opacity="0.25"/>
            <rect x="8%" y="70%" width="34%" height="8" rx="4" fill="#ffffff" opacity="0.15"/>
        SVG;

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}" role="img" aria-label="{$safe}">
  <defs><linearGradient id="g{$id}" x1="0" y1="0" x2="1" y2="1">
    <stop offset="0" stop-color="{$c1}"/><stop offset="1" stop-color="{$c2}"/>
  </linearGradient></defs>
  <rect width="{$w}" height="{$h}" fill="url(#g{$id})"/>
  {$decor}
  <text x="8%" y="38%" fill="#ffffff" font-family="Vazirmatn, Tahoma, sans-serif" font-size="{$fontSize}" font-weight="700">{$safe}</text>
  <text x="8%" y="46%" fill="{$accent}" font-family="Vazirmatn, Tahoma, sans-serif" font-size="18" font-weight="600">RagaSoftware</text>
</svg>
SVG;
    }
}
