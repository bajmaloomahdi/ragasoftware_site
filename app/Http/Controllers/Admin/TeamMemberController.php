<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Crud\ResourceController;
use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeamMemberController extends ResourceController
{
    protected string $model = TeamMember::class;

    protected string $page = 'Team';

    protected string $routeKey = 'team';

    protected array $searchable = ['name', 'role_title'];

    protected array $with = ['photo'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('team_members', 'slug')->ignore($record?->id)],
            'role_title' => ['nullable', 'string', 'max:150'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'photo_media_id' => ['nullable', 'exists:media,id'],
            'email' => ['nullable', 'email', 'max:150'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
