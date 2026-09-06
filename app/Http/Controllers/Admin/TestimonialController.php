<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Crud\ResourceController;
use App\Models\Customer;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TestimonialController extends ResourceController
{
    protected string $model = Testimonial::class;

    protected string $page = 'Testimonials';

    protected string $routeKey = 'testimonials';

    protected array $searchable = ['author_name', 'author_title', 'body'];

    protected array $with = ['customer:id,name'];

    protected function indexExtra(Request $request): array
    {
        return ['customers' => Customer::orderBy('name')->get(['id', 'name'])];
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'author_name' => ['required', 'string', 'max:150'],
            'author_title' => ['nullable', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:2000'],
            'avatar_media_id' => ['nullable', 'exists:media,id'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
