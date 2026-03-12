<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Login' }}</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f4f5f6;margin:0;padding:0;}
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
        .card{background:#fff;border:1px solid #eaebed;border-radius:16px;max-width:520px;width:100%;padding:22px;box-shadow:0 14px 28px rgba(0,0,0,0.06);text-align:center;}
        h2{margin:0 0 8px;font-size:22px;}
        p{margin:0 0 16px;color:#6b7280;line-height:1.4;}
        a.btn{display:inline-block;background:#0d5b90;color:#fff;text-decoration:none;font-weight:800;border-radius:12px;padding:12px 16px;}
        .muted{margin-top:12px;font-size:12px;color:#9ca3af;}
    </style>
</head>

<body>
    @php
        $deeplink = $deeplink ?? null;
    @endphp

    <div class="wrap">
        <div class="card">
            <h2>{{ $title ?? '' }}</h2>
            <p>{{ $sub_title ?? '' }}</p>

            @if ($deeplink)
                <a class="btn" href="{{ $deeplink }}">{{ __('Open in App') }}</a>
                <div class="muted">{{ __('If nothing happens, tap the button above.') }}</div>
                <script>
                    (function () {
                        var link = @json($deeplink);
                        if (!link) return;
                        setTimeout(function () {
                            try {
                                if (window.top) {
                                    window.top.location.href = link;
                                    return;
                                }
                            } catch (e) {}
                            try {
                                if (window.parent) {
                                    window.parent.location.href = link;
                                    return;
                                }
                            } catch (e) {}
                            window.location.href = link;
                        }, 700);
                    })();
                </script>
            @endif
        </div>
    </div>
</body>

</html>

