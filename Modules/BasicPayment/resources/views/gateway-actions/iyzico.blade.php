<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iyzico Checkout</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:24px;background:#f7f7fb;color:#1c1c1c;}
        .iyzico-wrap{max-width:680px;margin:40px auto;background:#fff;border-radius:16px;padding:24px;box-shadow:0 16px 36px rgba(0,0,0,0.08);}
        .iyzico-error{background:#ffecec;border:1px solid #f5b5b5;color:#8a1f1f;padding:12px 14px;border-radius:10px;}
        .iyzico-actions{margin-top:14px;}
        .iyzico-actions a{display:inline-block;background:#f39c12;color:#fff;padding:10px 14px;border-radius:10px;text-decoration:none;font-weight:700;}
    </style>
</head>
<body>
    <div class="iyzico-wrap">
        @if (!empty($iyzico_error))
            <div class="iyzico-error">{{ $iyzico_error }}</div>
            <div class="iyzico-actions">
                <a href="{{ route('payment-failed') }}">{{ __('Back') }}</a>
            </div>
        @else
            {!! $iyzico_checkout_form !!}
        @endif
    </div>
</body>
</html>
