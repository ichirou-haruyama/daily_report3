<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ホーム</title>
</head>

<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:#ffffff;">
    <div style="max-width:720px;width:100%;text-align:center;">
        <img src="{{ asset('images/home/main.jpg') }}" alt="ホーム画像"
            style="max-width:100%;height:auto;margin:0 auto 16px;display:block;" />
        <a href="{{ route('reports.create') }}"
            style="display:inline-block;background:#059669;color:#ffffff;padding:12px 20px;border-radius:8px;text-decoration:none;font-weight:600;">
            日報入力
        </a>
    </div>
</body>

</html>
