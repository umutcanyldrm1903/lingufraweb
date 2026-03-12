@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $metrics = $metrics ?? [];
        $monthly = $monthly ?? collect();
        $studentsCount = $studentsCount ?? 0;
    @endphp

    <div class="sp-reports">
        <div class="sp-reports__head">
            <div>
                <h2 class="sp-reports__title">{{ __('Reports') }}</h2>
                <p class="sp-reports__subtitle">{{ __('Track your lesson performance and attendance.') }}</p>
            </div>
        </div>

        <div class="sp-reports__grid">
            <div class="sp-report-card">
                <h5>{{ __('Total Lessons') }}</h5>
                <strong>{{ $metrics['total_lessons'] ?? 0 }}</strong>
            </div>
            <div class="sp-report-card">
                <h5>{{ __('Upcoming Lessons') }}</h5>
                <strong>{{ $metrics['upcoming_lessons'] ?? 0 }}</strong>
            </div>
            <div class="sp-report-card">
                <h5>{{ __('Completed') }}</h5>
                <strong>{{ $metrics['completed'] ?? 0 }}</strong>
            </div>
            <div class="sp-report-card">
                <h5>{{ __('No Show') }}</h5>
                <strong>{{ $metrics['no_show'] ?? 0 }}</strong>
            </div>
            <div class="sp-report-card">
                <h5>{{ __('Late') }}</h5>
                <strong>{{ $metrics['late'] ?? 0 }}</strong>
            </div>
            <div class="sp-report-card">
                <h5>{{ __('Cancelled by Teacher') }}</h5>
                <strong>{{ $metrics['cancelled_by_teacher'] ?? 0 }}</strong>
            </div>
            <div class="sp-report-card">
                <h5>{{ __('Cancelled by Student') }}</h5>
                <strong>{{ $metrics['cancelled_by_student'] ?? 0 }}</strong>
            </div>
            <div class="sp-report-card">
                <h5>{{ __('Active Students') }}</h5>
                <strong>{{ $studentsCount }}</strong>
            </div>
        </div>

        <div class="sp-reports__panel">
            <h4>{{ __('Monthly Summary') }}</h4>
            @if ($monthly->isEmpty())
                <p class="text-muted mb-0">{{ __('No report data yet.') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('Month') }}</th>
                                <th>{{ __('Total Lessons') }}</th>
                                <th>{{ __('Completed') }}</th>
                                <th>{{ __('No Show') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthly as $month => $row)
                                <tr>
                                    <td>{{ $month }}</td>
                                    <td>{{ $row['total'] ?? 0 }}</td>
                                    <td>{{ $row['completed'] ?? 0 }}</td>
                                    <td>{{ $row['no_show'] ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-reports__head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:16px;}
        .sp-reports__title{margin:0;font-weight:1000;color:#111827;}
        .sp-reports__subtitle{margin:6px 0 0;color:#6b7280;font-weight:700;}
        .sp-reports__grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px;}
        .sp-report-card{border:1px solid #eef2f7;border-radius:16px;padding:14px;background:#fff;box-shadow:0 10px 24px rgba(15,23,42,0.06);display:grid;gap:8px;}
        .sp-report-card h5{margin:0;font-weight:800;color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:.08em;}
        .sp-report-card strong{font-size:22px;font-weight:1000;color:#111827;}

        .sp-reports__panel{border:1px solid #eef2f7;border-radius:16px;padding:16px;background:#fff;box-shadow:0 10px 24px rgba(15,23,42,0.06);}
        .sp-reports__panel h4{margin:0 0 12px;font-weight:900;color:#111827;}
        .sp-reports__panel th{font-weight:900;color:#111827;}

        @media (max-width: 1199px){
            .sp-reports__grid{grid-template-columns:repeat(3,minmax(0,1fr));}
        }
        @media (max-width: 991px){
            .sp-reports__grid{grid-template-columns:repeat(2,minmax(0,1fr));}
        }
        @media (max-width: 575px){
            .sp-reports__grid{grid-template-columns:1fr;}
        }
    </style>
@endpush
