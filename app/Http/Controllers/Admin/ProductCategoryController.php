<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Crud\ResourceController;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductCategoryController extends ResourceController
{
    protected string $model = ProductCategory::class;

    protected string $page = 'Products/Categories';

    protected string $routeKey = 'product-categories';

    protected array $searchable = ['name'];

    protected array $with = ['products:id,category_id'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('product_categories', 'slug')->ignore($record?->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
