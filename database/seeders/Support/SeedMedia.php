<?php

namespace Database\Seeders\Support;

use App\Models\Media;

/**
 * Generates lightweight, bright branded SVG placeholders under
 * public/uploads/seed/ and registers them as Media rows, so seeded content
 * has real (committed, tiny) images without shipping binary assets or running
 * the uploader. Palette matches the site's Indigo → Violet design system.
 */
class SeedMedia
{
    private const INDIGO = '#6366f1';

    private const VIOLET = '#a855f7';

    private const INK = '#14161d';

    private const FONT = 'Vazirmatn, Tahoma, sans-serif';

    public static function make(string $key, string $label, string $variant = 'card'): Media
    {
        if ($existing = Media::where('original_name', $key.'.svg')->first()) {
            return $existing;
        }

        [$w, $h] = match ($variant) {
            'mockup' => [1200, 750],
            'cover' => [1200, 675],
            'square' => [600, 600],
            'logo' => [240, 96],
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
        $safe = htmlspecialchars($label, ENT_QUOTES);
        $id = substr(md5($label.$variant), 0, 6);
        $font = self::FONT;
        $indigo = self::INDIGO;
        $violet = self::VIOLET;
        $ink = self::INK;

        if ($variant === 'logo') {
            $iw = $w - 2;
            $ih = $h - 2;
            $ty = intdiv($h, 2) + 7;
            $textLen = $w - 90; // fit name between the mark and the right edge

            return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}" role="img" aria-label="{$safe}">
              <defs><linearGradient id="m{$id}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$indigo}"/><stop offset="1" stop-color="{$violet}"/></linearGradient></defs>
              <rect x="1" y="1" width="{$iw}" height="{$ih}" rx="16" fill="#ffffff" stroke="#e8e6f1"/>
              <rect x="18" y="26" width="44" height="44" rx="12" fill="url(#m{$id})"/>
              <text x="74" y="{$ty}" fill="{$ink}" font-family="{$font}" font-size="22" font-weight="800" textLength="{$textLen}" lengthAdjust="spacingAndGlyphs">{$safe}</text>
            </svg>
            SVG;
        }

        if ($variant === 'square') {
            $initial = htmlspecialchars(mb_substr($label, 0, 1), ENT_QUOTES);
            $cx = intdiv($w, 2);
            $cy = intdiv($h, 2) - 20;
            $r = intdiv($w, 6);
            $fs = intdiv($w, 4);

            return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}" role="img" aria-label="{$safe}">
              <defs><linearGradient id="a{$id}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#eef1ff"/><stop offset="1" stop-color="#f5e8ff"/></linearGradient></defs>
              <rect width="{$w}" height="{$h}" fill="url(#a{$id})"/>
              <circle cx="{$cx}" cy="{$cy}" r="{$r}" fill="#ffffff" opacity="0.8"/>
              <text x="50%" y="58%" text-anchor="middle" fill="{$indigo}" font-family="{$font}" font-size="{$fs}" font-weight="800">{$initial}</text>
            </svg>
            SVG;
        }

        if ($variant === 'og') {
            return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}" role="img" aria-label="{$safe}">
              <defs><linearGradient id="o{$id}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$indigo}"/><stop offset="1" stop-color="{$violet}"/></linearGradient></defs>
              <rect width="{$w}" height="{$h}" fill="url(#o{$id})"/>
              <circle cx="{$w}" cy="0" r="260" fill="#ffffff" opacity="0.10"/>
              <circle cx="0" cy="{$h}" r="220" fill="#ffffff" opacity="0.08"/>
              <text x="8%" y="46%" fill="#ffffff" font-family="{$font}" font-size="52" font-weight="800">{$safe}</text>
              <text x="8%" y="55%" fill="#ffffff" opacity="0.75" font-family="{$font}" font-size="24" font-weight="600">RagaSoftware</text>
            </svg>
            SVG;
        }

        // Dashboard mockup / cover: light "app UI" on off-white
        $panelH = $variant === 'mockup' ? intval($h * 0.62) : intval($h * 0.5);
        $gx = intval($w * 0.08);
        $gy = intval($h * 0.16);
        $pw = intval($w * 0.84);
        $sideH = $panelH - 76;
        $chartY = $panelH - 120;
        $rightX = $pw - 220;
        $rightH = $panelH - 90;
        $rightBadgeX = $pw - 200;
        $rightBadgeY = $panelH - 70;
        $labelY = intval($h * 0.92);

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}" role="img" aria-label="{$safe}">
          <defs>
            <linearGradient id="bg{$id}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f2f3ff"/><stop offset="1" stop-color="#faf4ff"/></linearGradient>
            <linearGradient id="ac{$id}" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="{$indigo}"/><stop offset="1" stop-color="{$violet}"/></linearGradient>
          </defs>
          <rect width="{$w}" height="{$h}" fill="url(#bg{$id})"/>
          <g transform="translate({$gx} {$gy})">
            <rect width="{$pw}" height="{$panelH}" rx="18" fill="#ffffff" stroke="#e8e6f1"/>
            <circle cx="26" cy="26" r="5" fill="#dcd9e8"/><circle cx="44" cy="26" r="5" fill="#dcd9e8"/><circle cx="62" cy="26" r="5" fill="#dcd9e8"/>
            <rect x="20" y="52" width="150" height="{$sideH}" rx="12" fill="#f4f3f9"/>
            <rect x="190" y="60" width="220" height="14" rx="7" fill="url(#ac{$id})"/>
            <rect x="190" y="86" width="140" height="10" rx="5" fill="#e8e6f1"/>
            <rect x="190" y="{$chartY}" width="90" height="90" rx="10" fill="#eef1ff"/>
            <rect x="292" y="{$chartY}" width="90" height="90" rx="10" fill="#f5e8ff"/>
            <rect x="394" y="{$chartY}" width="90" height="90" rx="10" fill="#eef1ff"/>
            <rect x="{$rightX}" y="60" width="200" height="{$rightH}" rx="12" fill="#faf9fd"/>
            <rect x="{$rightBadgeX}" y="{$rightBadgeY}" width="40" height="40" rx="8" fill="url(#ac{$id})"/>
          </g>
          <text x="8%" y="{$labelY}" fill="{$ink}" font-family="{$font}" font-size="26" font-weight="700">{$safe}</text>
        </svg>
        SVG;
    }
}
