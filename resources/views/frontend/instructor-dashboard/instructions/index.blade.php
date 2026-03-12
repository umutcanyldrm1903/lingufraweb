@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="sp-doc">
        <div class="sp-doc__head">
            <h2>{{ __('Instructions') }}</h2>
            <p>{{ __('Please follow these guidelines for a smooth lesson experience.') }}</p>
        </div>

        <div class="sp-doc__section">
            <h4>{{ __('1. Zoom & Live Lesson Rules') }}</h4>
            <ul>
                <li>{{ __('All live lessons must be held via Zoom on this platform.') }}</li>
                <li>{{ __('Do not share external meeting links or personal contact details.') }}</li>
                <li>{{ __('Start the session 5-10 minutes early to avoid delays.') }}</li>
            </ul>
        </div>

        <div class="sp-doc__section">
            <h4>{{ __('2. Attendance & Status') }}</h4>
            <ul>
                <li>{{ __('If the student joins late, the lesson is marked as late.') }}</li>
                <li>{{ __('If the student does not join, the lesson can be marked as no-show.') }}</li>
                <li>{{ __('Cancelled lessons are marked by teacher or student.') }}</li>
            </ul>
        </div>

        <div class="sp-doc__section">
            <h4>{{ __('3. Homework') }}</h4>
            <ul>
                <li>{{ __('Set clear titles and deadlines for each homework.') }}</li>
                <li>{{ __('Review submissions and provide feedback when possible.') }}</li>
            </ul>
        </div>

        <div class="sp-doc__section">
            <h4>{{ __('4. Library Materials') }}</h4>
            <ul>
                <li>{{ __('Upload only materials relevant to the assigned student.') }}</li>
                <li>{{ __('Use categories (Grammar, Vocabulary, Exam) to keep content organized.') }}</li>
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
