<?php

use Illuminate\Support\Facades\Schedule;

// Promote scheduled content and regenerate the sitemap.
Schedule::command('content:publish-scheduled')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('sitemap:generate')->hourly();
