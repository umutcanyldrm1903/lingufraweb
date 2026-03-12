@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $activeHomeworks = $activeHomeworks ?? collect();
        $archivedHomeworks = $archivedHomeworks ?? collect();
        $students = $students ?? collect();
    @endphp

    <div class="sp-homeworks">
        <div class="sp-homeworks__head">
            <div>
                <h2 class="sp-homeworks__title">{{ __('Homeworks') }}</h2>
                <p class="sp-homeworks__subtitle">{{ __('Assign homework to your students and track submissions.') }}</p>
            </div>
            <button class="sp-btn sp-btn-dark" data-bs-toggle="modal" data-bs-target="#createHomeworkModal">
                + {{ __('Create Homework') }}
            </button>
        </div>

        <ul class="nav nav-tabs sp-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-active-hw" data-bs-toggle="tab" data-bs-target="#tab-active-hw-pane"
                    type="button" role="tab" aria-controls="tab-active-hw-pane" aria-selected="true">
                    {{ __('Homeworks') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-archived-hw" data-bs-toggle="tab" data-bs-target="#tab-archived-hw-pane"
                    type="button" role="tab" aria-controls="tab-archived-hw-pane" aria-selected="false">
                    {{ __('Archived') }}
                </button>
            </li>
        </ul>

        <div class="tab-content sp-tabs__content">
            <div class="tab-pane fade show active" id="tab-active-hw-pane" role="tabpanel" aria-labelledby="tab-active-hw">
                @if ($activeHomeworks->isEmpty())
                    <div class="sp-empty-state">
                        <div class="sp-empty-state__icon" aria-hidden="true">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="sp-empty-state__text">{{ __('No homework found.') }}</div>
                    </div>
                @else
                    <div class="sp-homeworks__list">
                        @foreach ($activeHomeworks as $hw)
                            <div class="sp-hw-row">
                                <div class="sp-hw-row__info">
                                    <div class="sp-hw-row__student">
                                        @if ($hw->student?->image)
                                            <img src="{{ asset($hw->student->image) }}" alt="{{ $hw->student?->name }}">
                                        @else
                                            <span class="sp-hw-row__avatar"><i class="fas fa-user"></i></span>
                                        @endif
                                        <div>
                                            <strong>{{ $hw->student?->name ?? __('Student') }}</strong>
                                            <span>{{ $hw->title }}</span>
                                        </div>
                                    </div>
                                    <div class="sp-hw-row__meta">
                                        <span>{{ __('Due') }}: {{ $hw->due_at?->format('d M Y, H:i') ?? __('No deadline') }}</span>
                                        <span class="sp-hw-row__status">{{ ucfirst(str_replace('_', ' ', $hw->status ?? 'open')) }}</span>
                                    </div>
                                </div>
                                <div class="sp-hw-row__actions">
                                    @if ($hw->attachment_path)
                                        <a class="sp-btn sp-btn-outline sp-btn-sm" href="{{ asset($hw->attachment_path) }}" target="_blank">
                                            {{ __('Homework File') }}
                                        </a>
                                    @endif
                                    @if ($hw->submission?->submission_path)
                                        <a class="sp-btn sp-btn-light sp-btn-sm" href="{{ asset($hw->submission->submission_path) }}" target="_blank">
                                            {{ __('Submission') }}
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('instructor.homeworks.archive', $hw) }}">
                                        @csrf
                                        <button type="submit" class="sp-btn sp-btn-light sp-btn-sm">{{ __('Archive') }}</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="tab-pane fade" id="tab-archived-hw-pane" role="tabpanel" aria-labelledby="tab-archived-hw">
                @if ($archivedHomeworks->isEmpty())
                    <div class="sp-empty-state">
                        <div class="sp-empty-state__icon" aria-hidden="true">
                            <i class="fas fa-archive"></i>
                        </div>
                        <div class="sp-empty-state__text">{{ __('No archived homework found.') }}</div>
                    </div>
                @else
                    <div class="sp-homeworks__list">
                        @foreach ($archivedHomeworks as $hw)
                            <div class="sp-hw-row sp-hw-row--archived">
                                <div class="sp-hw-row__info">
                                    <div class="sp-hw-row__student">
                                        @if ($hw->student?->image)
                                            <img src="{{ asset($hw->student->image) }}" alt="{{ $hw->student?->name }}">
                                        @else
                                            <span class="sp-hw-row__avatar"><i class="fas fa-user"></i></span>
                                        @endif
                                        <div>
                                            <strong>{{ $hw->student?->name ?? __('Student') }}</strong>
                                            <span>{{ $hw->title }}</span>
                                        </div>
                                    </div>
                                    <div class="sp-hw-row__meta">
                                        <span>{{ __('Due') }}: {{ $hw->due_at?->format('d M Y, H:i') ?? __('No deadline') }}</span>
                                        <span class="sp-hw-row__status">{{ __('Archived') }}</span>
                                    </div>
                                </div>
                                <div class="sp-hw-row__actions">
                                    @if ($hw->attachment_path)
                                        <a class="sp-btn sp-btn-outline sp-btn-sm" href="{{ asset($hw->attachment_path) }}" target="_blank">
                                            {{ __('Homework File') }}
                                        </a>
                                    @endif
                                    @if ($hw->submission?->submission_path)
                                        <a class="sp-btn sp-btn-light sp-btn-sm" href="{{ asset($hw->submission->submission_path) }}" target="_blank">
                                            {{ __('Submission') }}
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

    <div class="modal fade" id="createHomeworkModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content sp-modal">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Create Homework') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('instructor.homeworks.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Student') }} <code>*</code></label>
                                <select name="student_id" class="form-select" required>
                                    <option value="">{{ __('Select student') }}</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->user_id }}">{{ $student->user?->name ?? $student->user_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Deadline') }}</label>
                                <input type="datetime-local" name="due_at" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Title') }} <code>*</code></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Description') }}</label>
                                <textarea name="description" rows="4" class="form-control"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Attachment') }}</label>
                                <input type="file" name="attachment" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="sp-btn sp-btn-dark">{{ __('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-homeworks__head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:16px;}
        .sp-homeworks__title{margin:0;font-weight:1000;color:#111827;}
        .sp-homeworks__subtitle{margin:6px 0 0;color:#6b7280;font-weight:700;}

        .sp-tabs{border-bottom:1px solid #eef2f7;margin-bottom:18px;gap:10px;}
        .sp-tabs .nav-link{border:0;color:#6b7280;font-weight:1000;padding:10px 2px;border-bottom:2px solid transparent;}
        .sp-tabs .nav-link.active{color:var(--student-brand);border-bottom-color:var(--student-brand);background:transparent;}
        .sp-tabs .nav-link:hover{color:var(--student-brand);}

        .sp-homeworks__list{display:grid;gap:12px;}
        .sp-hw-row{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px;border:1px solid #eef2f7;border-radius:16px;background:#fff;box-shadow:0 10px 24px rgba(15,23,42,0.06);}
        .sp-hw-row--archived{opacity:0.7;}
        .sp-hw-row__info{display:grid;gap:10px;}
        .sp-hw-row__student{display:flex;align-items:center;gap:12px;}
        .sp-hw-row__student img{width:44px;height:44px;border-radius:12px;object-fit:cover;}
        .sp-hw-row__avatar{width:44px;height:44px;border-radius:12px;background:#f3f4f6;display:grid;place-items:center;color:#6b7280;}
        .sp-hw-row__student strong{display:block;font-weight:900;color:#111827;}
        .sp-hw-row__student span{display:block;color:#6b7280;font-weight:700;font-size:13px;}
        .sp-hw-row__meta{display:flex;gap:14px;flex-wrap:wrap;font-weight:800;color:#6b7280;font-size:12px;}
        .sp-hw-row__status{color:#111827;font-weight:900;}
        .sp-hw-row__actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
        .sp-btn-sm{padding:6px 10px;font-size:12px;}

        .sp-empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:10px;min-height:260px;padding:18px 10px;}
        .sp-empty-state__icon{width:92px;height:92px;border-radius:50%;border:3px solid rgba(246,161,5,.55);display:grid;place-items:center;color:var(--student-brand);font-size:34px;box-shadow:0 14px 28px rgba(0,0,0,0.06);background:#fff;}
        .sp-empty-state__text{font-weight:1000;color:#111827;}
        .sp-modal{border-radius:18px;}
    </style>
@endpush
