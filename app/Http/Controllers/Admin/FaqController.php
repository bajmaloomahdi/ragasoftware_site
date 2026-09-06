<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Crud\ResourceController;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FaqController extends ResourceController
{
    protected string $model = Faq::class;

    protected string $page = 'Faqs';

    protected string $routeKey = 'faqs';

    protected array $searchable = ['question', 'answer'];

    protected array $with = ['category:id,name'];

    protected function indexExtra(Request $request): array
    {
        return ['categories' => FaqCategory::orderBy('sort_order')->get(['id', 'name'])];
    }

    protected function prepare(array $data, Request $request, ?Model $record): array
    {
        if (isset($data['answer'])) {
            $data['answer'] = clean($data['answer']);
        }

        return $data;
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'category_id' => ['nullable', 'exists:faq_categories,id'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
