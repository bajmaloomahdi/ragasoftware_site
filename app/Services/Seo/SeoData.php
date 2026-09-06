<?php

namespace App\Services\Seo;

/** Immutable, view-ready SEO values for one page. */
readonly class SeoData
{
    public function __construct(
        public string $title,
        public string $description,
        public string $canonical,
        public string $robots,
        public ?string $ogTitle = null,
        public ?string $ogDescription = null,
        public ?string $ogImage = null,
        public string $ogType = 'website',
        public string $twitterCard = 'summary_large_image',
        public array $jsonLd = [],
    ) {}
}
