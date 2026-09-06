<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Media;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'pages' => Page::count(),
                'products' => Product::count(),
                'services' => Service::count(),
                'projects' => Project::count(),
                'posts' => BlogPost::count(),
                'draftPosts' => BlogPost::where('status', 'draft')->count(),
                'media' => Media::count(),
                'newMessages' => ContactMessage::where('status', 'new')->count(),
            ],
            'recentMessages' => ContactMessage::latest()->take(5)->get([
                'id', 'name', 'subject', 'status', 'created_at',
            ]),
            'recentPosts' => BlogPost::withoutGlobalScopes()->latest('updated_at')->take(5)->get([
                'id', 'title', 'status', 'published_at', 'updated_at',
            ]),
        ]);
    }
}
