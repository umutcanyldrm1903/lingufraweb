@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="sp-students">
        <div class="sp-students__head">
            <div>
                <h2 class="sp-students__title">{{ __('Students') }}</h2>
                <p class="sp-students__subtitle">{{ __('Students who booked lessons with you appear here.') }}</p>
            </div>
            <form method="GET" action="{{ route('instructor.students.index') }}" class="sp-students__filter">
                <input type="text" name="name" class="form-control" placeholder="{{ __('Name') }}" value="{{ $filters['name'] ?? '' }}">
                <input type="text" name="email" class="form-control" placeholder="{{ __('Email') }}" value="{{ $filters['email'] ?? '' }}">
                <button type="submit" class="sp-btn sp-btn-dark sp-btn-sm">{{ __('Search') }}</button>
            </form>
        </div>

        @if (!$hasPlansTable)
            <div class="alert alert-warning mb-0">{{ __('user_plans table not found. Please run migrations first.') }}</div>
        @else
            <div class="sp-students__grid">
                @forelse ($myPlans as $plan)
                    <div class="sp-student-card">
                        <div class="sp-student-card__top">
                            <div class="sp-student-card__avatar">
                                @if ($plan->user?->image)
                                    <img src="{{ asset($plan->user->image) }}" alt="{{ $plan->user?->name }}">
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </div>
                            <div class="sp-student-card__info">
                                <strong>{{ $plan->user?->name ?? __('Student') }}</strong>
                                <button class="sp-student-card__link js-student-open" data-student-id="{{ $plan->user_id }}">
                                    {{ __('View') }}
                                </button>
                            </div>
                            <button class="sp-student-card__menu js-student-open" data-student-id="{{ $plan->user_id }}">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                        <div class="sp-student-card__meta">
                            <span>{{ __('Credits') }}: <strong>{{ $plan->lessons_remaining ?? 0 }}</strong></span>
                            <span>{{ __('Cancel') }}: <strong>{{ $plan->cancel_remaining ?? 0 }}</strong></span>
                        </div>
                    </div>
                @empty
                    <div class="sp-empty-state">
                        <div class="sp-empty-state__icon" aria-hidden="true">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="sp-empty-state__text">{{ __('No assigned students yet.') }}</div>
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    <div class="sp-drawer" id="studentDrawer" aria-hidden="true">
        <div class="sp-drawer__backdrop" data-drawer-close></div>
        <div class="sp-drawer__panel">
            <div class="sp-drawer__top">
                <button class="sp-drawer__close" data-drawer-close>&times;</button>
            </div>
            <div id="studentDrawerContent" class="sp-drawer__body">
                <div class="sp-drawer-loading">{{ __('Loading...') }}</div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .sp-students__head{display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:18px;}
        .sp-students__title{margin:0;font-weight:1000;color:#111827;}
        .sp-students__subtitle{margin:6px 0 0;color:#6b7280;font-weight:700;}
        .sp-students__filter{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
        .sp-students__filter .form-control{min-width:160px;}

        .sp-students__grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:26px;}
        .sp-student-card{border:1px solid #eef2f7;border-radius:16px;padding:14px;background:#fff;box-shadow:0 10px 24px rgba(0,0,0,0.06);display:grid;gap:12px;}
        .sp-student-card__top{display:flex;align-items:center;gap:10px;}
        .sp-student-card__avatar{width:44px;height:44px;border-radius:12px;background:#f3f4f6;display:grid;place-items:center;overflow:hidden;}
        .sp-student-card__avatar img{width:100%;height:100%;object-fit:cover;}
        .sp-student-card__info{flex:1;display:grid;gap:4px;}
        .sp-student-card__info strong{font-weight:900;color:#111827;}
        .sp-student-card__link{background:transparent;border:0;padding:0;font-weight:800;color:var(--student-dark);text-align:left;}
        .sp-student-card__menu{background:transparent;border:0;color:#6b7280;}
        .sp-student-card__meta{display:grid;gap:4px;font-weight:800;color:#111827;font-size:12px;}
        .sp-btn-sm{padding:6px 10px;font-size:12px;}

        .sp-drawer{position:fixed;inset:0;display:none;z-index:1055;}
        .sp-drawer.is-open{display:block;}
        .sp-drawer__backdrop{position:absolute;inset:0;background:rgba(15,23,42,0.6);}
        .sp-drawer__panel{position:absolute;top:0;right:0;width:min(420px,100%);height:100%;background:#fff;box-shadow:-20px 0 40px rgba(0,0,0,0.2);display:flex;flex-direction:column;}
        .sp-drawer__top{display:flex;justify-content:flex-end;padding:12px 16px;border-bottom:1px solid #f3f4f6;}
        .sp-drawer__close{background:transparent;border:0;font-size:26px;line-height:1;}
        .sp-drawer__body{padding:16px;overflow:auto;}
        .sp-drawer-loading{text-align:center;color:#6b7280;font-weight:800;padding:30px 0;}
        .sp-drawer__header h3{margin:0;font-weight:1000;color:#111827;}
        .sp-drawer__header p{margin:4px 0 0;color:#6b7280;font-weight:700;}
        .sp-drawer__tabs{border-bottom:1px solid #eef2f7;margin:14px 0;gap:10px;}
        .sp-drawer__tabs .nav-link{border:0;color:#6b7280;font-weight:1000;padding:10px 2px;border-bottom:2px solid transparent;}
        .sp-drawer__tabs .nav-link.active{color:var(--student-brand);border-bottom-color:var(--student-brand);background:transparent;}
        .sp-drawer__content{display:grid;gap:10px;}
        .sp-drawer-card{display:flex;align-items:center;justify-content:space-between;gap:12px;border:1px solid #eef2f7;border-radius:14px;padding:12px;background:#fff;}
        .sp-drawer-card span{display:block;color:#6b7280;font-weight:700;font-size:12px;}
        .sp-drawer-status{font-weight:900;color:#111827;}
        .sp-drawer-card--completed{border-color:#d1fae5;background:#ecfdf3;}
        .sp-drawer-card--late{border-color:#fde68a;background:#fffbeb;}
        .sp-drawer-card--no_show{border-color:#fecaca;background:#fef2f2;}
        .sp-drawer-card--cancelled_teacher,
        .sp-drawer-card--cancelled_student{border-color:#e5e7eb;background:#f9fafb;}
        .sp-drawer-empty{padding:18px 6px;color:#6b7280;font-weight:800;}
        .sp-drawer-profile{display:grid;gap:10px;}
        .sp-drawer-profile__row{display:flex;align-items:center;justify-content:space-between;font-weight:800;color:#111827;border-bottom:1px dashed #e5e7eb;padding-bottom:8px;}

        .sp-empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:10px;min-height:180px;padding:18px 10px;}
        .sp-empty-state__icon{width:92px;height:92px;border-radius:50%;border:3px solid rgba(246,161,5,.55);display:grid;place-items:center;color:var(--student-brand);font-size:34px;box-shadow:0 14px 28px rgba(0,0,0,0.06);background:#fff;}
        .sp-empty-state__text{font-weight:1000;color:#111827;}

        @media (max-width: 1200px){
            .sp-students__grid{grid-template-columns:repeat(3,minmax(0,1fr));}
        }
        @media (max-width: 991px){
            .sp-students__grid{grid-template-columns:repeat(2,minmax(0,1fr));}
        }
        @media (max-width: 575px){
            .sp-students__grid{grid-template-columns:1fr;}
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const drawer = document.getElementById('studentDrawer');
            const drawerContent = document.getElementById('studentDrawerContent');

            const closeDrawer = () => {
                drawer.classList.remove('is-open');
                drawer.setAttribute('aria-hidden', 'true');
            };

            document.querySelectorAll('[data-drawer-close]').forEach((btn) => {
                btn.addEventListener('click', closeDrawer);
            });

            document.querySelectorAll('.js-student-open').forEach((button) => {
                button.addEventListener('click', async () => {
                    const studentId = button.dataset.studentId;
                    if (!studentId) return;
                    drawer.classList.add('is-open');
                    drawer.setAttribute('aria-hidden', 'false');
                    drawerContent.innerHTML = '<div class="sp-drawer-loading">{{ __('Loading...') }}</div>';
                    try {
                        const response = await fetch(`{{ url('/instructor/students') }}/${studentId}/panel`);
                        const html = await response.text();
                        drawerContent.innerHTML = html;
                    } catch (e) {
                        drawerContent.innerHTML = '<div class="sp-drawer-loading">{{ __('Unable to load.') }}</div>';
                    }
                });
            });
        })();
    </script>
@endpush
