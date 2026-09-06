<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaFolder;
use App\Services\Media\MediaUploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function __construct(private readonly MediaUploader $uploader) {}

    public function index(Request $request): Response|JsonResponse
    {
        $folderId = $request->integer('folder') ?: null;

        $media = Media::query()
            ->with('folder:id,name')
            ->when($folderId, fn ($q) => $q->where('folder_id', $folderId))
            ->when(! $folderId && $request->boolean('root_only', false), fn ($q) => $q->whereNull('folder_id'))
            ->when($request->string('search')->toString(), fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('original_name', 'like', "%{$s}%")
                    ->orWhere('title', 'like', "%{$s}%")
                    ->orWhere('alt_text', 'like', "%{$s}%");
            }))
            ->when($request->string('type')->toString() === 'image', fn ($q) => $q->where('mime_type', 'like', 'image/%'))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        $folders = MediaFolder::orderBy('path')->get(['id', 'parent_id', 'name', 'slug', 'path']);

        // The MediaPicker modal fetches JSON from this same endpoint.
        if ($request->wantsJson() || $request->boolean('json')) {
            return response()->json(['media' => $media, 'folders' => $folders]);
        }

        return Inertia::render('Media/Index', [
            'media' => $media,
            'folders' => $folders,
            'filters' => $request->only('folder', 'search', 'type'),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $maxKb = (int) config('cms.media.max_upload_kb', 4096);
        $mimes = implode(',', array_merge(
            config('cms.media.image_mimes', []),
            config('cms.media.document_mimes', []),
        ));

        $data = $request->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*' => ['file', "max:{$maxKb}", "mimes:{$mimes}"],
            'folder_id' => ['nullable', 'exists:media_folders,id'],
        ]);

        $created = collect($data['files'])->map(
            fn ($file) => $this->uploader->store($file, $data['folder_id'] ?? null, $request->user()->id),
        );

        if ($request->wantsJson()) {
            return response()->json(['media' => $created]);
        }

        return back()->with('success', $created->count().' فایل بارگذاری شد.');
    }

    public function update(Request $request, Media $medium): RedirectResponse
    {
        $medium->update($request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
            'folder_id' => ['nullable', 'exists:media_folders,id'],
        ]));

        return back()->with('success', 'اطلاعات فایل به‌روزرسانی شد.');
    }

    public function destroy(Media $medium): RedirectResponse
    {
        $this->uploader->delete($medium);
        $medium->delete();

        return back()->with('success', 'فایل حذف شد.');
    }

    public function storeFolder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:media_folders,id'],
        ]);

        MediaFolder::create($data);

        return back()->with('success', 'پوشه ساخته شد.');
    }

    public function destroyFolder(MediaFolder $folder): RedirectResponse
    {
        $folder->media()->update(['folder_id' => null]);
        $folder->children()->update(['parent_id' => $folder->parent_id]);
        $folder->delete();

        return back()->with('success', 'پوشه حذف شد.');
    }
}
