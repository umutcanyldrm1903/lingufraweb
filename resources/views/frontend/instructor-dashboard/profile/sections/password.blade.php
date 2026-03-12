<div class="tab-pane fade show {{ session('profile_tab') == 'password' ? 'active' : '' }}" id="settings-password" role="tabpanel"
    aria-labelledby="settings-password-tab" tabindex="0">
    <form action="{{ route('instructor.setting.password.update') }}" method="POST" class="sp-password-form">
        @csrf
        @method('PUT')
        <div class="form-grp">
            <label for="currentpassword">{{ __('Current Password') }} <code>*</code></label>
            <input id="currentpassword" type="password" name="current_password" placeholder="{{ __('Current Password') }}">
        </div>
        <div class="form-grp">
            <label for="newpassword">{{ __('New Password') }} <code>*</code></label>
            <input id="newpassword" type="password" name="password" placeholder="{{ __('New Password') }}">

        </div>
        <div class="form-grp">
            <label for="repassword">{{ __('Re-Type New Password') }} <code>*</code></label>
            <input id="repassword" type="password" name="password_confirmation" placeholder="{{ __('Re-Type New Password') }}">
        </div>
        <div class="sp-form-actions">
            <button type="submit" class="btn">{{ __('Update Password') }}</button>
        </div>
    </form>
</div>
