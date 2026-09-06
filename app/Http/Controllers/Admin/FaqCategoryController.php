<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Crud\ResourceController;
use App\Models\FaqCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FaqCategoryController extends ResourceController
{
    protected string $model = FaqCategory::class;

    protected string $page = 'Faqs/Categories';

    protected string $routeKey = 'faq-categories';

    protected array $searchable = ['name'];

    protected array $with = ['faqs:id,category_id'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('faq_categories', 'slug')->ignore($record?->id)],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
