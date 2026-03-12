<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
</head>

<body>
    <div class="w-100 h-100 position-absolute">
        <div class="row d-flex justify-content-center align-items-center h-100 w-100">
            <div class="text-center p-4">
                <img src="{{ asset('uploads/website-images/' . $image) }}">
                <h4 class="mt-2">{{ $title }}</h4>
                <p>{{ $sub_title }}</p>

                @php
                    $invoiceId = $invoiceId ?? '';
                    $result = $result ?? '';
                    $deeplink = $invoiceId ? 'lingufranca://payment?invoice_id=' . urlencode($invoiceId) . '&result=' . urlencode($result) : null;
                @endphp

                @if ($deeplink)
                    <a class="btn btn-primary mt-3 px-4 py-2" href="{{ $deeplink }}">
                        {{ __('Open in App') }}
                    </a>
                    <div class="mt-2 text-muted" style="font-size: 12px;">
                        {{ __('Invoice') }}: {{ $invoiceId }}
                    </div>
                    <script>
                        (function () {
                            var link = @json($deeplink);
                            if (!link) return;
                            setTimeout(function () {
                                // Iyzipay checkout runs inside an iframe. Prefer top navigation so the
                                // mobile WebView can intercept the deep-link reliably.
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
                            }, 900);
                        })();
                    </script>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
