@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $homeworks = $homeworks ?? collect();
        $archivedHomeworks = $archivedHomeworks ?? collect();
    @endphp

    <div class="sp-homeworks">
        <div class="sp-homeworks__card">
            <h3 class="sp-homeworks__title">{{ __('Homeworks') }}</h3>

            <ul class="nav nav-tabs sp-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-hw-active" data-bs-toggle="tab" data-bs-target="#hw-active"
                        type="button" role="tab" aria-controls="hw-active" aria-selected="true">
                        {{ __('Homeworks') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-hw-archived" data-bs-toggle="tab" data-bs-target="#hw-archived"
                        type="button" role="tab" aria-controls="hw-archived" aria-selected="false">
                        {{ __('Archived') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content sp-tabs__content">
                <div class="tab-pane fade show active" id="hw-active" role="tabpanel" aria-labelledby="tab-hw-active">
                    @if ($homeworks->isEmpty())
                        <div class="sp-empty-state">
                            <div class="sp-empty-state__icon" aria-hidden="true">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="sp-empty-state__text">{{ __('No homeworks found!') }}</div>
                        </div>
                    @else
                        <div class="sp-homeworks__list">
                            @foreach ($homeworks as $hw)
                                <div class="sp-hw-card">
                                    <div class="sp-hw-card__head">
                                        <div>
                                            <strong>{{ $hw->title }}</strong>
                                            <span>{{ $hw->due_at?->format('d M Y, H:i') ?? __('No deadline') }}</span>
                                        </div>
                                        <span class="sp-hw-card__status">{{ ucfirst(str_replace('_', ' ', $hw->status ?? 'open')) }}</span>
                                    </div>

                                    @if ($hw->description)
                                        <p class="sp-hw-card__desc">{{ $hw->description }}</p>
                                    @endif

                                    <div class="sp-hw-card__actions">
                                        @if ($hw->attachment_path)
                                            <a href="{{ asset($hw->attachment_path) }}" target="_blank" class="sp-hw-card__link">
                                                {{ __('Homework File') }}
                                            </a>
                                        @endif
                                        @if ($hw->submission?->submission_path)
                                            <a href="{{ asset($hw->submission->submission_path) }}" target="_blank" class="sp-hw-card__link">
                                                {{ __('My Submission') }}
                                            </a>
                                        @endif
                                    </div>

                                    @if (!$hw->submission?->submission_path)
                                        <form method="POST" action="{{ route('student.homeworks.submit', $hw) }}" enctype="multipart/form-data" class="sp-hw-form">
                                            @csrf
                                            <div class="row g-2 align-items-center">
                                                <div class="col-md-5">
                                                    <input type="file" name="submission" class="form-control" required>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" name="note" class="form-control" placeholder="{{ __('Note (optional)') }}">
                                                </div>
                                                <div class="col-md-2 d-grid">
                                                    <button type="submit" class="sp-hw-submit">{{ __('Submit') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="tab-pane fade" id="hw-archived" role="tabpanel" aria-labelledby="tab-hw-archived">
                    @if ($archivedHomeworks->isEmpty())
                        <div class="sp-empty-state">
                            <div class="sp-empty-state__icon" aria-hidden="true">
                                <i class="fas fa-archive"></i>
                            </div>
                            <div class="sp-empty-state__text">{{ __('No archived homeworks found!') }}</div>
                        </div>
                    @else
                        <div class="sp-homeworks__list">
                            @foreach ($archivedHomeworks as $hw)
                                <div class="sp-hw-card sp-hw-card--archived">
                                    <div class="sp-hw-card__head">
                                        <div>
                                            <strong>{{ $hw->title }}</strong>
                                            <span>{{ $hw->due_at?->format('d M Y, H:i') ?? __('No deadline') }}</span>
                                        </div>
                                        <span class="sp-hw-card__status">{{ __('Archived') }}</span>
                                    </div>
                                    <div class="sp-hw-card__actions">
                                        @if ($hw->attachment_path)
                                            <a href="{{ asset($hw->attachment_path) }}" target="_blank" class="sp-hw-card__link">
                                                {{ __('Homework File') }}
                                            </a>
                                        @endif
                                        @if ($hw->submission?->submission_path)
                                            <a href="{{ asset($hw->submission->submission_path) }}" target="_blank" class="sp-hw-card__link">
                                                {{ __('My Submission') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-homeworks__card{max-width:980px;margin:0 auto;border:1px solid #eef2f7;border-radius:18px;padding:18px;box-shadow:0 10px 24px rgba(0,0,0,0.04);}
        .sp-homeworks__title{margin:0 0 10px;font-weight:1000;color:#111827;}
        .sp-tabs{border-bottom:1px solid #eef2f7;margin-bottom:18px;gap:10px;}
        .sp-tabs .nav-link{border:0;color:#6b7280;font-weight:1000;padding:10px 2px;border-bottom:2px solid transparent;}
        .sp-tabs .nav-link.active{color:var(--student-brand);border-bottom-color:var(--student-brand);background:transparent;}
        .sp-tabs .nav-link:hover{color:var(--student-brand);}

        .sp-homeworks__list{display:grid;gap:14px;}
        .sp-hw-card{border:1px solid #eef2f7;border-radius:16px;padding:14px;background:#fff;box-shadow:0 10px 24px rgba(15,23,42,0.06);display:grid;gap:12px;}
        .sp-hw-card--archived{opacity:0.7;}
        .sp-hw-card__head{display:flex;align-items:center;justify-content:space-between;gap:10px;}
        .sp-hw-card__head strong{display:block;font-weight:900;color:#111827;}
        .sp-hw-card__head span{display:block;color:#6b7280;font-weight:700;font-size:12px;}
        .sp-hw-card__status{font-weight:900;color:#111827;background:#f9fafb;border:1px solid #e5e7eb;border-radius:999px;padding:6px 10px;font-size:12px;}
        .sp-hw-card__desc{margin:0;color:#4b5563;font-weight:700;}
        .sp-hw-card__actions{display:flex;gap:10px;flex-wrap:wrap;}
        .sp-hw-card__link{font-weight:800;color:var(--student-brand);text-decoration:none;}
        .sp-hw-card__link:hover{text-decoration:underline;color:var(--student-brand);}

        .sp-hw-form{border-top:1px dashed #e5e7eb;padding-top:12px;}
        .sp-hw-submit{border-radius:12px;padding:10px 12px;font-weight:1000;background:var(--student-brand);border:1px solid var(--student-brand);color:#111827;}
        .sp-hw-submit:hover{opacity:.92;}

        .sp-empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:10px;min-height:320px;padding:18px 10px;}
        .sp-empty-state__icon{width:92px;height:92px;border-radius:50%;border:3px solid rgba(246,161,5,.55);display:grid;place-items:center;color:var(--student-brand);font-size:34px;box-shadow:0 14px 28px rgba(0,0,0,0.06);background:#fff;}
        .sp-empty-state__text{font-weight:1000;color:#111827;}
    </style>
@endpush
