<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Crud\ResourceController;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends ResourceController
{
    protected string $model = Customer::class;

    protected string $page = 'Customers';

    protected string $routeKey = 'customers';

    protected array $searchable = ['name', 'industry'];

    protected array $with = ['logo'];

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('customers', 'slug')->ignore($record?->id)],
            'logo_media_id' => ['nullable', 'exists:media,id'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'industry' => ['nullable', 'string', 'max:120'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
