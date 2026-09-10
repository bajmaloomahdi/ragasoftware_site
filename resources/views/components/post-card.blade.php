@props(['post'])

<article class="card card-hover group relative flex flex-col overflow-hidden p-0">
    <div class="overflow-hidden border-b border-paper-200 bg-paper-50">
        <x-picture :media="$post->cover" ratio="16/9" class="w-full object-cover transition duration-500 group-hover:scale-[1.04]" />
    </div>
    <div class="flex flex-1 flex-col p-6">
        <div class="mb-3 flex items-center gap-2 text-xs text-ink-500">
            @if($post->category)
                <span class="rounded-full bg-brand-50 px-2.5 py-1 font-semibold text-brand-700">{{ $post->category->name }}</span>
            @endif
            <time datetime="{{ jdate_iso($post->published_at) }}">{{ jdate($post->published_at) }}</time>
        </div>
        <h3 class="text-base font-bold leading-7 text-ink-900 transition group-hover:text-brand-700">
            <a href="{{ route('blog.show', $post->slug) }}" class="after:absolute after:inset-0">{{ $post->title }}</a>
        </h3>
        <p class="mt-2 flex-1 text-sm leading-7 text-ink-500">{{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}</p>
        <div class="mt-4 flex items-center gap-2 text-xs text-ink-400">
            @if($post->author)<span>{{ $post->author->name }}</span><span aria-hidden="true">·</span>@endif
            <span>{{ $post->reading_minutes ?? 3 }} دقیقه مطالعه</span>
        </div>
    </div>
</article>
