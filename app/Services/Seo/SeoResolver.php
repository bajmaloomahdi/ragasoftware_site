<?php

namespace App\Services\Seo;

use App\Models\Media;
use App\Models\SeoMeta;
use App\Services\Settings\SettingsRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Produces a {@see SeoData} for a page by merging, in priority order:
 *   1. the per-entity seo_meta row (admin-edited)
 *   2. the editable site SEO settings (defaults / title template)
 *   3. model fallbacks (title→meta_title, summary/excerpt→description, cover→OG)
 */
class SeoResolver
{
    public function __construct(private readonly SettingsRepository $settings) {}

    /**
     * @param  Model|null  $model  a model using HasSeoMeta (Page, Product, …)
     * @param  array<string,mixed>  $overrides  ['title' => ..., 'description' => ..., 'ogType' => ..., 'jsonLd' => [...]]
     */
    public function for(?Model $model = null, array $overrides = []): SeoData
    {
        /** @var SeoMeta|null $meta */
        $meta = $model && method_exists($model, 'seo') ? $model->seo : null;
        $fallbacks = $model && method_exists($model, 'seoFallbacks') ? $model->seoFallbacks() : [];

        // A hand-written seo_meta.meta_title is treated as the final title;
        // everything else (an override, or the model's own name) runs through
        // the ":title | Site" template.
        if (filled($meta?->meta_title)) {
            $rawTitle = $meta->meta_title;
            $title = $meta->meta_title;
        } else {
            $rawTitle = $overrides['title'] ?? ($fallbacks['meta_title'] ?? null);
            $title = $this->applyTemplate($rawTitle);
        }

        $description = Str::limit(strip_tags((string) (
            $overrides['description']
            ?? $meta?->meta_description
            ?? ($fallbacks['meta_description'] ?? null)
            ?? $this->settings->get('seo.default_description', '')
        )), 160, '');

        $canonical = $meta?->canonical_url ?: url()->current();

        $robots = $meta?->no_index
            ? 'noindex, nofollow'
            : ($meta?->meta_robots ?: config('seo.defaults.meta_robots', 'index, follow'));

        $ogImage = $this->image($meta?->og_media_id)
            ?? $this->fallbackImage($model)
            ?? $this->image($this->settings->get('seo.default_og_media_id'));

        return new SeoData(
            title: $title,
            description: $description,
            canonical: $canonical,
            robots: $robots,
            ogTitle: $meta?->og_title ?: $title,
            ogDescription: $meta?->og_description ?: $description,
            ogImage: $ogImage,
            ogType: $overrides['ogType'] ?? 'website',
            twitterCard: $meta?->twitter_card ?: 'summary_large_image',
            jsonLd: array_merge($this->globalJsonLd(), $overrides['jsonLd'] ?? []),
        );
    }

    private function applyTemplate(?string $title): string
    {
        $default = $this->settings->get('seo.default_title', config('seo.defaults.title'));

        if (! $title) {
            return $default;
        }

        $template = $this->settings->get('seo.title_template', config('seo.defaults.title_template', ':title'));

        return trim(str_replace(':title', $title, $template));
    }

    private function image(mixed $mediaId): ?string
    {
        if (! $mediaId) {
            return null;
        }

        return media($mediaId)?->url;
    }

    private function fallbackImage(?Model $model): ?string
    {
        foreach (['heroImage', 'cover', 'image'] as $rel) {
            if ($model && method_exists($model, $rel) && $model->{$rel}) {
                return $model->{$rel}->url;
            }
        }

        return null;
    }

    /** Organization + WebSite — emitted on every page. */
    public function globalJsonLd(): array
    {
        $name = $this->settings->get('general.company_name', config('app.name'));
        $logo = $this->image($this->settings->get('general.logo_media_id'));

        return [
            'organization' => array_filter([
                '@context' => 'https://schema.org',
                '@type' => $this->settings->get('seo.organization_type', 'Organization'),
                'name' => $name,
                'legalName' => $this->settings->get('general.company_legal_name'),
                'url' => url('/'),
                'logo' => $logo,
                'email' => $this->settings->get('contact.email'),
                'telephone' => $this->settings->get('contact.phone'),
                'sameAs' => $this->settings->socialLinks()->pluck('url')->all() ?: null,
            ]),
            'website' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $name,
                'url' => url('/'),
            ],
        ];
    }
}
