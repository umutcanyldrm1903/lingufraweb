<!DOCTYPE html>
<html lang="{{ str_starts_with(strtolower((string) $outreachMessage->campaign->language), 'tr') ? 'tr' : 'en' }}">
<head>
    <meta charset="utf-8">
    <title>{{ $outreachMessage->subject }}</title>
</head>
<body style="margin:0;padding:24px;background:#f5f5f5;font-family:Arial,sans-serif;color:#111827;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;padding:32px;border:1px solid #e5e7eb;">
        {!! $outreachMessage->body_html !!}
    </div>
</body>
</html>
