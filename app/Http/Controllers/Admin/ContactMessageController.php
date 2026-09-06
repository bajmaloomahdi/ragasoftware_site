<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ContactMessageController extends Controller
{
    public function index(Request $request): Response
    {
        $rows = ContactMessage::query()
            ->with('repliedBy:id,name')
            ->when($request->string('search')->toString(), fn (Builder $q, $s) => $q->where(function (Builder $q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('company', 'like', "%{$s}%")->orWhere('subject', 'like', "%{$s}%");
            }))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Messages/Index', [
            'rows' => $rows,
            'filters' => $request->only('search', 'status'),
            'counts' => ContactMessage::selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    public function show(ContactMessage $message): RedirectResponse
    {
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }

        return back();
    }

    public function update(Request $request, ContactMessage $message): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(ContactMessage::STATUSES)],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['status'] === 'answered' && $message->status !== 'answered') {
            $data['replied_at'] = now();
            $data['replied_by'] = $request->user()->id;
        }

        $message->update($data);

        return back()->with('success', 'وضعیت پیام به‌روزرسانی شد.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('success', 'پیام حذف شد.');
    }
}
