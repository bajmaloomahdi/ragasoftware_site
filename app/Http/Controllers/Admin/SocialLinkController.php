<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Crud\ResourceController;
use App\Models\SocialLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SocialLinkController extends ResourceController
{
    protected string $model = SocialLink::class;

    protected string $page = 'Settings/Social';

    protected string $routeKey = 'social-links';

    protected array $searchable = ['platform', 'label', 'url'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'platform' => ['required', 'string', 'max:50'],
            'label' => ['nullable', 'string', 'max:80'],
            'url' => ['required', 'url', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
