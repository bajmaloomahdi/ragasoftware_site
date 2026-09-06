@php
    $labels = [
        'name' => 'نام و نام‌خانوادگی', 'phone' => 'شماره تماس', 'email' => 'ایمیل',
        'company' => 'نام شرکت', 'subject' => 'موضوع', 'message' => 'پیام شما',
    ];
@endphp

@if(session('success'))
    <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('contact.store') }}" class="space-y-4 rounded-2xl border border-mist-200 bg-white p-6 shadow-sm">
    @csrf
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach(['name', 'phone'] as $field)
            <div>
                <label for="c-{{ $field }}" class="mb-1 block text-sm font-medium text-navy-800">{{ $labels[$field] }}@if($field==='name') <span class="text-red-500">*</span>@endif</label>
                <input type="{{ $field === 'phone' ? 'tel' : 'text' }}" id="c-{{ $field }}" name="{{ $field }}" value="{{ old($field) }}"
                       @if($field==='name') required @endif
                       @if($field==='phone') dir="ltr" @endif
                       class="w-full rounded-lg border border-mist-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                @error($field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        @endforeach
        <div>
            <label for="c-email" class="mb-1 block text-sm font-medium text-navy-800">{{ $labels['email'] }}</label>
            <input type="email" id="c-email" name="email" value="{{ old('email') }}" dir="ltr"
                   class="w-full rounded-lg border border-mist-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="c-company" class="mb-1 block text-sm font-medium text-navy-800">{{ $labels['company'] }}</label>
            <input type="text" id="c-company" name="company" value="{{ old('company') }}"
                   class="w-full rounded-lg border border-mist-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
        </div>
    </div>

    <div>
        <label for="c-subject" class="mb-1 block text-sm font-medium text-navy-800">{{ $labels['subject'] }}</label>
        <input type="text" id="c-subject" name="subject" value="{{ old('subject') }}"
               class="w-full rounded-lg border border-mist-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
    </div>

    <div>
        <label for="c-message" class="mb-1 block text-sm font-medium text-navy-800">{{ $labels['message'] }} <span class="text-red-500">*</span></label>
        <textarea id="c-message" name="message" rows="5" required
                  class="w-full rounded-lg border border-mist-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">{{ old('message') }}</textarea>
        @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- honeypot --}}
    <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

    <button type="submit" class="btn-primary w-full sm:w-auto">ارسال پیام</button>
</form>
