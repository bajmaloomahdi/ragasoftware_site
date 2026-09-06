<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Crud\ResourceController;
use App\Models\BlogTag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogTagController extends ResourceController
{
    protected string $model = BlogTag::class;

    protected string $page = 'Blog/Tags';

    protected string $routeKey = 'blog-tags';

    protected array $searchable = ['name'];

    protected string $defaultSort = 'name';

    protected array $sortable = ['name', 'created_at', 'id'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('blog_tags', 'slug')->ignore($record?->id)],
        ];
    }
}
