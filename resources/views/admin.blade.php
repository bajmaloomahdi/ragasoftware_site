<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name') }}</title>

    @routes
    @viteReactRefresh
    @vite(['resources/css/admin.css', 'resources/js/admin.tsx'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
