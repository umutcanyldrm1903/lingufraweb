@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $students = $students ?? collect();
        $items = $items ?? collect();
    @endphp

    <div class="sp-library">
        <div class="sp-library__head">
            <div>
                <h2 class="sp-library__title">{{ __('Library') }}</h2>
                <p class="sp-library__subtitle">{{ __('Upload materials for your assigned students.') }}</p>
            </div>
        </div>

        <div class="sp-library__panel">
            <h4>{{ __('Upload Material') }}</h4>
            @if ($students->isEmpty())
                <div class="alert alert-warning">{{ __('You do not have assigned students yet.') }}</div>
            @else
                <form method="POST" action="{{ route('instructor.library.store') }}" enctype="multipart/form-data" class="sp-library__form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Student') }} <code>*</code></label>
                            <select name="student_id" class="form-select" required>
                                <option value="">{{ __('Select student') }}</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->user_id }}">{{ $student->user?->name ?? $student->user_id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Category') }} <code>*</code></label>
                            <input type="text" name="category" class="form-control" placeholder="{{ __('Vocabulary / Grammar / Exam') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Title') }} <code>*</code></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('File') }} <code>*</code></label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">
                        <button type="submit" class="sp-btn sp-btn-dark">{{ __('Upload') }}</button>
                    </div>
                </form>
            @endif
        </div>

        <div class="sp-library__panel">
            <h4>{{ __('Uploaded Materials') }}</h4>
            @if ($items->isEmpty())
                <div class="sp-empty-state">
                    <div class="sp-empty-state__icon" aria-hidden="true">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="sp-empty-state__text">{{ __('No materials yet.') }}</div>
                </div>
            @else
                <div class="sp-library__list">
                    @foreach ($items as $item)
                        <div class="sp-library__row">
                            <div class="sp-library__info">
                                <strong>{{ $item->title }}</strong>
                                <span>{{ $item->category }}</span>
                                <small>{{ $item->student?->name ?? __('Student') }}</small>
                            </div>
                            <div class="sp-library__actions">
                                <a href="{{ asset($item->file_path) }}" target="_blank" class="sp-btn sp-btn-outline sp-btn-sm">
                                    {{ __('View') }}
                                </a>
                                <form method="POST" action="{{ route('instructor.library.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sp-btn sp-btn-light sp-btn-sm">{{ __('Remove') }}</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-library__head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:16px;}
        .sp-library__title{margin:0;font-weight:1000;color:#111827;}
        .sp-library__subtitle{margin:6px 0 0;color:#6b7280;font-weight:700;}
        .sp-library__panel{border:1px solid #eef2f7;border-radius:16px;padding:16px;background:#fff;box-shadow:0 10px 24px rgba(15,23,42,0.06);margin-bottom:16px;}
        .sp-library__panel h4{margin:0 0 12px;font-weight:900;color:#111827;}
        .sp-library__form textarea{resize:vertical;}

        .sp-library__list{display:grid;gap:10px;}
        .sp-library__row{display:flex;align-items:center;justify-content:space-between;gap:12px;border:1px solid #f3f4f6;border-radius:14px;padding:12px 14px;}
        .sp-library__info{display:grid;gap:4px;}
        .sp-library__info strong{font-weight:900;color:#111827;}
        .sp-library__info span{color:#6b7280;font-weight:800;font-size:12px;text-transform:uppercase;letter-spacing:.04em;}
        .sp-library__info small{color:#94a3b8;font-weight:700;}
        .sp-library__actions{display:flex;gap:8px;flex-wrap:wrap;}
        .sp-btn-sm{padding:6px 10px;font-size:12px;}

        .sp-empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:10px;min-height:220px;padding:18px 10px;}
        .sp-empty-state__icon{width:92px;height:92px;border-radius:50%;border:3px solid rgba(246,161,5,.55);display:grid;place-items:center;color:var(--student-brand);font-size:34px;box-shadow:0 14px 28px rgba(0,0,0,0.06);background:#fff;}
        .sp-empty-state__text{font-weight:1000;color:#111827;}
    </style>
@endpush
