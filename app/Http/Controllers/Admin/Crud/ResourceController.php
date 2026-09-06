<?php

namespace App\Http\Controllers\Admin\Crud;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Shared list / create / update / delete / toggle behaviour for the simpler
 * CMS entities (customers, team, FAQ, categories, tags, …). Complex entities
 * (pages, products, blog posts) have their own controllers.
 *
 * A subclass sets a handful of properties and implements rules().
 */
abstract class ResourceController extends Controller
{
    /** @var class-string<Model> */
    protected string $model;

    /** Inertia page folder, e.g. "Customers" → Pages/Customers/Index.tsx */
    protected string $page;

    /** Route + name key, e.g. "customers" → admin.customers.* */
    protected string $routeKey;

    /** @var list<string> */
    protected array $searchable = ['name'];

    /** @var list<string> */
    protected array $sortable = ['sort_order', 'created_at', 'id'];

    protected string $defaultSort = 'sort_order';

    protected string $defaultDirection = 'asc';

    /** @var list<string> */
    protected array $with = [];

    protected int $perPage = 20;

    /** Extra props passed to the Index page (selects, etc.). */
    protected function indexExtra(Request $request): array
    {
        return [];
    }

    /** @return array<string, mixed> validation rules */
    abstract protected function rules(Request $request, ?Model $record): array;

    /** Mutate validated data before persisting. */
    protected function prepare(array $data, Request $request, ?Model $record): array
    {
        return $data;
    }

    /** Hook after create/update (sync relations, media, …). */
    protected function afterSave(Model $record, Request $request, bool $creating): void {}

    protected function baseQuery(): Builder
    {
        return $this->model::query()->with($this->with);
    }

    public function index(Request $request): Response
    {
        $sort = in_array($request->string('sort')->toString(), $this->sortable, true)
            ? $request->string('sort')->toString()
            : $this->defaultSort;
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc'
            : ($request->string('direction')->toString() === 'asc' ? 'asc' : $this->defaultDirection);

        $rows = $this->baseQuery()
            ->when($request->string('search')->toString(), function (Builder $q, string $s) {
                $q->where(function (Builder $q) use ($s) {
                    foreach ($this->searchable as $col) {
                        $q->orWhere($col, 'like', "%{$s}%");
                    }
                });
            })
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')))
            ->when($request->filled('active'), fn (Builder $q) => $q->where('is_active', $request->boolean('active')))
            ->orderBy($sort, $direction)
            ->paginate($this->perPage)
            ->withQueryString();

        return Inertia::render("{$this->page}/Index", array_merge([
            'rows' => $rows,
            'filters' => $request->only('search', 'status', 'active', 'sort', 'direction'),
        ], $this->indexExtra($request)));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->prepare(
            $request->validate($this->rules($request, null)),
            $request,
            null,
        );

        $record = $this->model::create($data);
        $this->afterSave($record, $request, true);

        return back()->with('success', 'با موفقیت ایجاد شد.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $record = $this->model::findOrFail($id);

        $data = $this->prepare(
            $request->validate($this->rules($request, $record)),
            $request,
            $record,
        );

        $record->update($data);
        $this->afterSave($record, $request, false);

        return back()->with('success', 'تغییرات ذخیره شد.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->model::findOrFail($id)->delete();

        return back()->with('success', 'حذف شد.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $record = $this->model::findOrFail($id);
        $column = $record->isFillable('is_active') ? 'is_active' : 'is_featured';
        $record->update([$column => ! $record->{$column}]);

        return back()->with('success', 'وضعیت تغییر کرد.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];

        foreach ($ids as $position => $id) {
            $this->model::where('id', $id)->update(['sort_order' => $position]);
        }

        return back();
    }
}
