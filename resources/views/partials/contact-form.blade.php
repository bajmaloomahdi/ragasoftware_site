@php
    $labels = [
        'name' => 'نام و نام‌خانوادگی', 'phone' => 'شماره تماس', 'email' => 'ایمیل',
        'company' => 'نام شرکت', 'subject' => 'موضوع', 'message' => 'پیام شما',
    ];
    $input = 'w-full rounded-xl border border-paper-300 bg-white px-4 py-3 text-sm text-ink-800 outline-none transition placeholder:text-ink-300 focus:border-brand-400 focus:ring-4 focus:ring-brand-100';
@endphp

@if(session('success'))
    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

<form method="POST" action="{{ route('contact.store') }}" class="space-y-4 rounded-3xl border border-paper-200 bg-white p-6 sm:p-8" style="box-shadow: var(--shadow-soft);">
    @csrf
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach(['name', 'phone'] as $field)
            <div>
                <label for="c-{{ $field }}" class="mb-1.5 block text-sm font-medium text-ink-700">{{ $labels[$field] }}@if($field==='name') <span class="text-brand-500">*</span>@endif</label>
                <input type="{{ $field === 'phone' ? 'tel' : 'text' }}" id="c-{{ $field }}" name="{{ $field }}" value="{{ old($field) }}"
                       @if($field==='name') required @endif @if($field==='phone') dir="ltr" @endif class="{{ $input }}">
                @error($field)<p class="mt-1 text-xs text-brand-600">{{ $message }}</p>@enderror
            </div>
        @endforeach
        <div>
            <label for="c-email" class="mb-1.5 block text-sm font-medium text-ink-700">{{ $labels['email'] }}</label>
            <input type="email" id="c-email" name="email" value="{{ old('email') }}" dir="ltr" class="{{ $input }}">
            @error('email')<p class="mt-1 text-xs text-brand-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="c-company" class="mb-1.5 block text-sm font-medium text-ink-700">{{ $labels['company'] }}</label>
            <input type="text" id="c-company" name="company" value="{{ old('company') }}" class="{{ $input }}">
        </div>
    </div>

    <div>
        <label for="c-subject" class="mb-1.5 block text-sm font-medium text-ink-700">{{ $labels['subject'] }}</label>
        <input type="text" id="c-subject" name="subject" value="{{ old('subject') }}" class="{{ $input }}">
    </div>

    <div>
        <label for="c-message" class="mb-1.5 block text-sm font-medium text-ink-700">{{ $labels['message'] }} <span class="text-brand-500">*</span></label>
        <textarea id="c-message" name="message" rows="5" required class="{{ $input }}">{{ old('message') }}</textarea>
        @error('message')<p class="mt-1 text-xs text-brand-600">{{ $message }}</p>@enderror
    </div>

    {{-- honeypot --}}
    <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

    <button type="submit" class="btn-primary w-full sm:w-auto">
        ارسال پیام
        <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M9 5l7 7-7 7"/></svg>
    </button>
</form>
