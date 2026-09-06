<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Crud\ResourceController;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogCategoryController extends ResourceController
{
    protected string $model = BlogCategory::class;

    protected string $page = 'Blog/Categories';

    protected string $routeKey = 'blog-categories';

    protected array $searchable = ['name'];

    protected array $with = ['posts:id,category_id'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('blog_categories', 'slug')->ignore($record?->id)],
            'parent_id' => ['nullable', 'exists:blog_categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
