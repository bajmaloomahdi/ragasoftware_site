<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $site->get('general.company_name', 'راگا سافت‌ور') }} | در حال به‌روزرسانی</title>
    <style>
        /* Self-contained on purpose — no build/Vite assets, so this page
           keeps working even if the compiled front-end is broken. */
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            font-family: Tahoma, 'Segoe UI', system-ui, sans-serif;
            color: #1e293b;
            text-align: center;
            padding: 2rem;
            box-sizing: border-box;
        }
        .wrap { max-width: 28rem; }
        img { max-width: 220px; height: auto; margin-bottom: 1.75rem; }
        p { font-size: 1.125rem; line-height: 2; margin: 0; }
    </style>
</head>
<body>
    <div class="wrap">
        @if($logo = media($site->get('general.logo_media_id')))
            <img src="{{ $logo->variantUrl('md') }}" alt="{{ $site->get('general.company_name', 'راگا سافت‌ور') }}">
        @endif
        <p>{{ $message }}</p>
    </div>
</body>
</html>
