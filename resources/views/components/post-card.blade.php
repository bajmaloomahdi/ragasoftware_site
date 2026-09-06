@props(['post'])

<article class="card card-hover group relative flex flex-col p-0 overflow-hidden">
    <a href="{{ route('blog.show', $post->slug) }}" class="block overflow-hidden">
        <x-picture :media="$post->cover" ratio="16/9" class="w-full object-cover transition duration-300 group-hover:scale-[1.03]" />
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="mb-2 flex items-center gap-2 text-xs text-navy-500">
            @if($post->category)
                <span class="rounded-full bg-brand-50 px-2 py-0.5 font-medium text-brand-600">{{ $post->category->name }}</span>
            @endif
            <time datetime="{{ jdate_iso($post->published_at) }}">{{ jdate($post->published_at) }}</time>
        </div>
        <h3 class="text-base font-bold leading-7 text-navy-900">
            <a href="{{ route('blog.show', $post->slug) }}" class="after:absolute after:inset-0">{{ $post->title }}</a>
        </h3>
        <p class="mt-2 flex-1 text-sm leading-7 text-navy-600">{{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}</p>
        <div class="mt-3 flex items-center gap-2 text-xs text-navy-400">
            @if($post->author)<span>{{ $post->author->name }}</span><span>·</span>@endif
            <span>{{ $post->reading_minutes ?? 3 }} دقیقه مطالعه</span>
        </div>
    </div>
</article>
