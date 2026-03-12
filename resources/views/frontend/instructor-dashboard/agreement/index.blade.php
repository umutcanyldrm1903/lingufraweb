@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="sp-doc">
        <div class="sp-doc__head">
            <h2>{{ __('Agreement') }}</h2>
            <p>{{ __('Please review and follow these basic terms while teaching.') }}</p>
        </div>

        <div class="sp-doc__section">
            <h4>{{ __('Professional Conduct') }}</h4>
            <ul>
                <li>{{ __('Start lessons on time and use the platform tools only.') }}</li>
                <li>{{ __('Respect student privacy and do not share personal contacts.') }}</li>
                <li>{{ __('Use clear and respectful communication at all times.') }}</li>
            </ul>
        </div>

        <div class="sp-doc__section">
            <h4>{{ __('Lesson Cancellation') }}</h4>
            <ul>
                <li>{{ __('If you must cancel, do it as early as possible.') }}</li>
                <li>{{ __('Student cancellations are handled with cancellation rights.') }}</li>
                <li>{{ __('No-show cases may affect the lesson status and reports.') }}</li>
            </ul>
        </div>

        <div class="sp-doc__section">
            <h4>{{ __('Content & Materials') }}</h4>
            <ul>
                <li>{{ __('Only share materials you are authorized to use.') }}</li>
                <li>{{ __('Upload materials per student via the Library section.') }}</li>
            </ul>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-doc{display:grid;gap:18px;}
        .sp-doc__head h2{margin:0;font-weight:1000;color:#111827;}
        .sp-doc__head p{margin:6px 0 0;color:#6b7280;font-weight:700;}
        .sp-doc__section{border:1px solid #eef2f7;border-radius:16px;padding:16px;background:#fff;box-shadow:0 10px 24px rgba(15,23,42,0.06);}
        .sp-doc__section h4{margin:0 0 10px;font-weight:900;color:#111827;}
        .sp-doc__section ul{margin:0;padding-left:18px;color:#4b5563;font-weight:700;display:grid;gap:6px;}
    </style>
@endpush
