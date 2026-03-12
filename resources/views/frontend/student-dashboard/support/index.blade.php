@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    @php
        $tab = (string) request()->query('tab', 'support');

        $categories = [
            [
                'key' => 'plan-payment',
                'title' => 'Plan & Payment',
                'icon' => 'fas fa-credit-card',
            ],
            [
                'key' => 'lessons',
                'title' => 'Lessons',
                'icon' => 'fas fa-layer-group',
            ],
            [
                'key' => 'technical',
                'title' => 'Technical Issues',
                'icon' => 'fas fa-tools',
            ],
            [
                'key' => 'instructors',
                'title' => 'Instructors',
                'icon' => 'fas fa-user-tie',
            ],
            [
                'key' => 'account',
                'title' => 'Account & Profile',
                'icon' => 'fas fa-user-circle',
            ],
        ];
    @endphp

    <div class="sp-support">
        <h2 class="sp-page-title">{{ __('Support') }}</h2>

        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                <div class="sp-support__menu">
                    <a href="{{ route('student.support.index', ['tab' => 'support']) }}"
                        class="sp-support__menu-item {{ $tab === 'support' ? 'is-active' : '' }}">
                        {{ __('Support') }}
                    </a>
                    <a href="{{ route('student.support.index', ['tab' => 'tickets']) }}"
                        class="sp-support__menu-item {{ $tab === 'tickets' ? 'is-active' : '' }}">
                        {{ __('My Support Requests') }}
                    </a>
                </div>
            </div>

            <div class="col-lg-8">
                @if ($tab === 'tickets')
                    <div class="sp-support__panel">
                        <div class="sp-support__panel-title">{{ __('My Support Requests') }}</div>
                        <div class="sp-empty-state sp-empty-state--compact">
                            <div class="sp-empty-state__icon" aria-hidden="true">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="sp-empty-state__text">{{ __('You do not have any support requests yet.') }}</div>
                        </div>
                    </div>
                @else
                    <div class="sp-support__panel">
                        <div class="sp-support__question">{{ __('How can we help you?') }}</div>
                        <div class="sp-support__label">{{ __('Categories') }}</div>

                        <div class="sp-support__cats">
                            @foreach ($categories as $cat)
                                <a class="sp-support__cat" href="{{ route('student.support.index', ['tab' => 'support', 'category' => $cat['key']]) }}">
                                    <i class="{{ $cat['icon'] }}"></i>
                                    <span>{{ __($cat['title']) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sp-page-title{margin:6px 0 18px;text-align:center;font-weight:1000;color:var(--student-brand);}

        .sp-support__menu{background:var(--student-brand);border-radius:18px;padding:18px;min-height:380px;box-shadow:0 18px 44px rgba(246,161,5,.22);display:flex;flex-direction:column;gap:10px;}
        .sp-support__menu-item{display:flex;align-items:center;gap:10px;border-radius:12px;padding:12px 14px;color:#fff;text-decoration:none;font-weight:1000;border:1px solid rgba(255,255,255,.20);background:rgba(255,255,255,.08);}
        .sp-support__menu-item.is-active{background:rgba(255,255,255,.22);border-color:rgba(255,255,255,.35);}
        .sp-support__menu-item:hover{background:rgba(255,255,255,.18);color:#fff;}

        .sp-support__panel{background:#fff;border:1px solid #eef2f7;border-radius:18px;padding:18px;box-shadow:0 10px 24px rgba(0,0,0,0.04);}
        .sp-support__panel-title{font-weight:1000;color:#111827;margin-bottom:10px;}
        .sp-support__question{font-weight:1000;color:#111827;margin-bottom:14px;}
        .sp-support__label{font-weight:1000;color:#6b7280;margin-bottom:10px;}

        .sp-support__cats{display:grid;gap:12px;}
        .sp-support__cat{display:flex;align-items:center;gap:12px;background:#fff;border:1px solid #eef2f7;border-radius:16px;padding:16px 18px;text-decoration:none;color:#111827;box-shadow:0 10px 24px rgba(0,0,0,0.04);}
        .sp-support__cat i{width:38px;height:38px;border-radius:12px;display:grid;place-items:center;background:#fff7e6;color:var(--student-brand);border:1px solid rgba(246,161,5,.35);}
        .sp-support__cat span{font-weight:1000;}
        .sp-support__cat:hover{transform:translateY(-1px);box-shadow:0 18px 44px rgba(0,0,0,0.07);color:#111827;border-color:rgba(246,161,5,.35);}

        .sp-empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:10px;min-height:320px;padding:18px 10px;}
        .sp-empty-state__icon{width:92px;height:92px;border-radius:50%;border:3px solid rgba(246,161,5,.55);display:grid;place-items:center;color:var(--student-brand);font-size:34px;box-shadow:0 14px 28px rgba(0,0,0,0.06);background:#fff;}
        .sp-empty-state__text{font-weight:1000;color:#111827;}
        .sp-empty-state--compact{min-height:220px;}
    </style>
@endpush
