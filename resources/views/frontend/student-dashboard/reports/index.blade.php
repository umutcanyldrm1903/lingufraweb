@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $reports = isset($reports) ? collect($reports) : collect();
        $startDate = $startDate ?? (string) request()->query('start_date', '');
        $endDate = $endDate ?? (string) request()->query('end_date', '');
    @endphp

    <div class="sp-reports">
        <h2 class="sp-page-title">{{ __('My Reports') }}</h2>

        <div class="sp-reports__filters">
            <form method="GET" action="{{ route('student.reports.index') }}" class="sp-filter">
                <div class="sp-filter__row">
                    <div class="sp-date">
                        <input class="sp-input datepicker" type="text" name="start_date" value="{{ $startDate }}"
                            placeholder="{{ __('Start Date') }}" aria-label="{{ __('Start Date') }}">
                        <i class="far fa-calendar-alt sp-date__icon" aria-hidden="true"></i>
                    </div>
                    <div class="sp-date">
                        <input class="sp-input datepicker" type="text" name="end_date" value="{{ $endDate }}"
                            placeholder="{{ __('End Date') }}" aria-label="{{ __('End Date') }}">
                        <i class="far fa-calendar-alt sp-date__icon" aria-hidden="true"></i>
                    </div>

                    <button type="submit" class="sp-btn sp-btn-brand sp-filter__btn">
                        <i class="fas fa-search"></i>
                        {{ __('Search') }}
                    </button>

                    <a href="{{ route('student.reports.index') }}" class="sp-btn sp-btn-outline-brand sp-filter__btn">
                        {{ __('Clear') }}
                    </a>
                </div>
            </form>
        </div>

        @if ($reports->isEmpty())
            <div class="sp-alert sp-alert-info">
                <i class="fas fa-info-circle"></i>
                <span>{{ __('No reports found') }}</span>
            </div>
        @else
            <div class="sp-reports__list">
                @foreach ($reports as $report)
                    <article class="sp-report-card">
                        <div class="sp-report-card__top">
                            <div>
                                <h4 class="sp-report-card__title">{{ $report->title }}</h4>
                                <p class="sp-report-card__meta">
                                    <span>{{ $report->date_label }}</span>
                                    <span>{{ __('Instructor') }}: {{ $report->instructor_name }}</span>
                                </p>
                            </div>
                            <span class="sp-report-card__status">{{ \Illuminate\Support\Str::headline($report->status) }}</span>
                        </div>

                        <div class="sp-report-card__body">
                            {{ $report->summary }}
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .sp-page-title{margin:6px 0 18px;text-align:center;font-weight:1000;color:var(--student-brand);}
        .sp-reports__filters{background:#fff;border:1px solid #eef2f7;border-radius:16px;padding:16px;box-shadow:0 10px 24px rgba(0,0,0,0.04);max-width:920px;margin:0 auto 14px;}
        .sp-filter__row{display:flex;gap:12px;align-items:center;justify-content:center;flex-wrap:wrap;}
        .sp-date{position:relative;}
        .sp-date__icon{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#6b7280;pointer-events:none;}
        .sp-input{height:44px;border-radius:12px;border:1px solid #e5e7eb;padding:10px 38px 10px 12px;font-weight:800;min-width:220px;}
        .sp-input:focus{outline:none;box-shadow:0 0 0 4px rgba(246,161,5,.18);border-color:rgba(246,161,5,.8);}
        .sp-btn-brand{background:var(--student-brand);border:1px solid var(--student-brand);color:#111827;}
        .sp-btn-brand:hover{opacity:.92;color:#111827;}
        .sp-btn-outline-brand{background:#fff;border:1px solid var(--student-brand);color:#111827;}
        .sp-btn-outline-brand:hover{background:#fff7e6;color:#111827;}
        .sp-filter__btn{height:44px;display:inline-flex;align-items:center;gap:8px;}
        .sp-alert{display:flex;gap:10px;align-items:center;max-width:920px;margin:0 auto;border-radius:12px;padding:12px 14px;font-weight:900;}
        .sp-alert-info{background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;}

        .sp-reports__list{display:grid;gap:14px;max-width:920px;margin:0 auto;}
        .sp-report-card{background:#fff;border:1px solid #eef2f7;border-radius:18px;padding:18px;box-shadow:0 12px 28px rgba(15,23,42,0.06);}
        .sp-report-card__top{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;margin-bottom:10px;}
        .sp-report-card__title{margin:0;font-weight:1000;color:#111827;}
        .sp-report-card__meta{margin:6px 0 0;display:flex;gap:12px;flex-wrap:wrap;color:#6b7280;font-weight:800;}
        .sp-report-card__status{display:inline-flex;align-items:center;justify-content:center;padding:8px 12px;border-radius:999px;background:#fff7e6;border:1px solid rgba(246,161,5,.35);font-size:12px;font-weight:1000;color:#111827;text-transform:uppercase;letter-spacing:.05em;}
        .sp-report-card__body{color:#374151;font-weight:800;line-height:1.7;white-space:pre-wrap;}

        @media(max-width:575.98px){
            .sp-input{min-width:100%;}
            .sp-filter__btn{width:100%;justify-content:center;}
        }
    </style>
@endpush
