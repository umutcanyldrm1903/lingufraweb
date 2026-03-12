@extends('frontend.instructor-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="sp-settings">
        <div class="sp-settings__card">
            <div class="sp-settings__header">
                <h3>{{ __('Settings') }}</h3>
            </div>
            <ul class="nav sp-settings__tabs" id="settingsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ session('profile_tab') == 'profile' ? 'active' : '' }}" id="settings-profile-tab" data-bs-toggle="tab"
                        data-bs-target="#settings-profile" type="button" role="tab" aria-controls="settings-profile"
                        aria-selected="true">{{ __('Profile') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ session('profile_tab') == 'schedule' ? 'active' : '' }}" id="settings-schedule-tab" data-bs-toggle="tab"
                        data-bs-target="#settings-schedule" type="button" role="tab" aria-controls="settings-schedule"
                        aria-selected="false">{{ __('Schedule') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ session('profile_tab') == 'email' ? 'active' : '' }}" id="settings-email-tab" data-bs-toggle="tab"
                        data-bs-target="#settings-email" type="button" role="tab" aria-controls="settings-email"
                        aria-selected="false">{{ __('Email') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ session('profile_tab') == 'password' ? 'active' : '' }}" id="settings-password-tab" data-bs-toggle="tab"
                        data-bs-target="#settings-password" type="button" role="tab" aria-controls="settings-password"
                        aria-selected="false">{{ __('Password') }}</button>
                </li>
            </ul>

            <div class="tab-content sp-settings__content" id="settingsTabContent">
                @include('frontend.instructor-dashboard.profile.sections.profile')
                @include('frontend.instructor-dashboard.profile.sections.schedule')
                @include('frontend.instructor-dashboard.profile.sections.email')
                @include('frontend.instructor-dashboard.profile.sections.password')
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .sp-settings{position:relative;padding:10px 0 30px;}
    .sp-settings::before{
        content:"";
        position:absolute;
        left:-22px;
        right:-22px;
        top:-22px;
        height:180px;
        background:#f6a105;
        border-radius:18px;
        z-index:0;
    }
    .sp-settings__card{
        position:relative;
        z-index:1;
        background:#fff;
        border-radius:18px;
        padding:28px 30px 32px;
        max-width:980px;
        margin:0 auto;
        box-shadow:0 20px 40px rgba(0,0,0,0.1);
    }
    .sp-settings__header h3{margin:0 0 6px;font-weight:900;color:#111827;}
    .sp-settings__tabs{display:flex;gap:18px;border-bottom:1px solid #e5e7eb;margin:8px 0 24px;padding:0 0 6px;}
    .sp-settings__tabs .nav-link{
        border:0;
        padding:0 0 8px;
        font-weight:700;
        color:#6b7280;
        background:transparent;
        border-bottom:2px solid transparent;
    }
    .sp-settings__tabs .nav-link.active{
        color:#f6a105;
        border-bottom-color:#f6a105;
    }
    .sp-settings__content .tab-pane{padding-top:6px;}

    .sp-form-grid{display:grid;gap:18px;}
    .sp-form-row{display:grid;grid-template-columns:repeat(2, minmax(0, 1fr));gap:18px;}
    .sp-form-row .form-grp{margin-bottom:0;}
    .sp-form-actions{display:flex;justify-content:flex-end;margin-top:12px;}
    .sp-form-actions .btn{
        padding:10px 18px;
        border-radius:10px;
        font-weight:800;
        background:#f6a105;
        border:0;
        color:#fff;
    }
    .sp-form-actions .btn:hover{opacity:.92;}

    .sp-email-form{max-width:420px;margin:0 auto;}
    .sp-email-form .form-grp input{width:100%;}

    .sp-password-form{max-width:420px;margin:0 auto;}

    @media (max-width: 991px){
        .sp-settings::before{left:-14px;right:-14px;}
        .sp-settings__card{padding:22px;}
        .sp-form-row{grid-template-columns:1fr;}
    }
</style>
@endpush
