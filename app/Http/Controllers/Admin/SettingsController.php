<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\Settings\SettingsRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function edit(SettingsRepository $settings): Response
    {
        $schema = config('site.fields');
        $values = [];
        $mediaPreviews = [];

        foreach ($schema as $field) {
            $value = $settings->get($field['key'], $field['default'] ?? null);
            $values[$field['key']] = $value;

            if ($field['type'] === 'media' && $value) {
                $mediaPreviews[$field['key']] = media($value)?->thumb_url;
            }
        }

        return Inertia::render('Settings/Edit', [
            'groups' => config('site.groups'),
            'schema' => $schema,
            'values' => $values,
            'mediaPreviews' => $mediaPreviews,
        ]);
    }

    public function update(Request $request, SettingsRepository $settings): RedirectResponse
    {
        $schema = collect(config('site.fields'))->keyBy('key');

        $incoming = $request->validate([
            'values' => ['required', 'array'],
        ])['values'];

        foreach ($incoming as $key => $value) {
            $field = $schema->get($key);
            if (! $field) {
                continue; // ignore unknown keys
            }

            SiteSetting::updateOrCreate(
                ['key' => $key],
                [
                    'group' => $field['group'],
                    'type' => $field['type'],
                    'label' => $field['label'],
                    'value' => is_bool($value)
                        ? ($value ? '1' : '0')
                        : (is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value),
                ],
            );
        }

        $settings->flush();

        return back()->with('success', 'تنظیمات ذخیره شد.');
    }
}
