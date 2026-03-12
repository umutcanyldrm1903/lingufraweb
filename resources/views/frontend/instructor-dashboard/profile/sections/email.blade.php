<div class="tab-pane fade show {{ session('profile_tab') == 'email' ? 'active' : '' }}" id="settings-email" role="tabpanel"
    aria-labelledby="settings-email-tab" tabindex="0">
    <form action="{{ route('instructor.setting.email.update') }}" method="POST" class="sp-email-form">
        @csrf
        @method('PUT')
        <div class="form-grp">
            <label for="new-email">{{ __('New Email') }} <code>*</code></label>
            <input id="new-email" type="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div class="form-grp">
            <label for="current-email-password">{{ __('Current Password') }} <code>*</code></label>
            <input id="current-email-password" type="password" name="current_password" required>
        </div>
        <div class="sp-form-actions">
            <button type="submit" class="btn">{{ __('Change Email') }}</button>
        </div>
    </form>
</div>
