<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Services\Cms\SectionRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(SectionRenderer $renderer): View
    {
        $page = Page::published()->forCurrentLocale()->where('slug', 'contact')->with('seo')->first();

        return view('public.contact', [
            'page' => $page,
            'sectionsHtml' => $page ? $renderer->render($page) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
            // honeypot
            'website' => ['nullable', 'size:0'],
        ], [], [
            'name' => 'نام',
            'message' => 'پیام',
        ]);

        unset($data['website']);

        ContactMessage::create([
            ...$data,
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'source_path' => $request->headers->get('referer'),
        ]);

        return back()->with('success', 'پیام شما با موفقیت ارسال شد. به‌زودی با شما تماس می‌گیریم.');
    }
}
