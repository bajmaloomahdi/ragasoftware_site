<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait SavesSeoMeta
{
    protected function seoRules(): array
    {
        return [
            'seo' => ['nullable', 'array'],
            'seo.meta_title' => ['nullable', 'string', 'max:70'],
            'seo.meta_description' => ['nullable', 'string', 'max:200'],
            'seo.focus_keyword' => ['nullable', 'string', 'max:120'],
            'seo.canonical_url' => ['nullable', 'string', 'max:255'],
            'seo.meta_robots' => ['nullable', 'string', 'max:60'],
            'seo.no_index' => ['boolean'],
            'seo.og_title' => ['nullable', 'string', 'max:120'],
            'seo.og_description' => ['nullable', 'string', 'max:200'],
            'seo.og_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'seo.twitter_card' => ['nullable', 'string', 'max:30'],
            'seo.twitter_title' => ['nullable', 'string', 'max:120'],
            'seo.twitter_description' => ['nullable', 'string', 'max:200'],
            'seo.twitter_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'seo.schema_type' => ['nullable', 'string', 'max:40'],
        ];
    }

    /** @param Model&\App\Models\Concerns\HasSeoMeta $model */
    protected function persistSeo(Model $model, Request $request): void
    {
        $payload = $request->input('seo');

        if (! is_array($payload)) {
            return;
        }

        $nullable = [
            'meta_title', 'meta_description', 'focus_keyword', 'canonical_url', 'meta_robots',
            'og_title', 'og_description', 'og_media_id', 'twitter_card', 'twitter_title',
            'twitter_description', 'twitter_media_id', 'schema_type',
        ];

        $attributes = ['no_index' => ! empty($payload['no_index'])];
        foreach ($nullable as $key) {
            $attributes[$key] = ($payload[$key] ?? '') !== '' ? $payload[$key] : null;
        }

        $model->seo()->updateOrCreate([], $attributes);
    }
}
