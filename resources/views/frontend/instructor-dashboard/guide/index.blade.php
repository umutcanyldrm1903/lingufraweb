@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="sp-doc">
        <div class="sp-doc__head">
            <h2>{{ __('User Guide') }}</h2>
            <p>{{ __('A quick guide to manage lessons, students, and materials.') }}</p>
        </div>

        <div class="sp-doc__section">
            <h4>{{ __('Quick Start') }}</h4>
            <ol>
                <li>{{ __('Open Schedule and add your available time slots.') }}</li>
                <li>{{ __('Go to Students and create a live lesson for an assigned student.') }}</li>
                <li>{{ __('Share homework or library materials when needed.') }}</li>
                <li>{{ __('Track attendance and lesson status in Lessons and Reports.') }}</li>
            </ol>
        </div>

        <div class="sp-doc__section">
            <h4>{{ __('Main Sections') }}</h4>
            <ul>
                <li>{{ __('Schedule: set weekly availability and view upcoming lessons.') }}</li>
                <li>{{ __('Lessons: see lesson history and statuses.') }}</li>
                <li>{{ __('Students: manage assignments and open student panels.') }}</li>
                <li>{{ __('Homeworks: assign tasks and review submissions.') }}</li>
                <li>{{ __('Library: upload materials per student.') }}</li>
                <li>{{ __('Reports: monthly and overall lesson metrics.') }}</li>
            </ul>
        </div>

        <div class="sp-doc__section">
            <h4>{{ __('Tips') }}</h4>
            <ul>
                <li>{{ __('Always check your Zoom settings before creating a live lesson.') }}</li>
                <li>{{ __('Use clear titles and due dates for homeworks.') }}</li>
                <li>{{ __('Keep library items categorized for faster access.') }}</li>
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
        .sp-doc__section ul,.sp-doc__section ol{margin:0;padding-left:18px;color:#4b5563;font-weight:700;display:grid;gap:6px;}
    </style>
@endpush
