<div class="tab-pane fade show {{ session('profile_tab') == 'profile' ? 'active': '' }}" id="settings-profile" role="tabpanel" aria-labelledby="settings-profile-tab" tabindex="0">
    @php
        $profile = (array) ($user->instructor_profile ?? []);
        $nameParts = preg_split('/\s+/', trim((string) $user->name), 2);
        $firstName = $profile['first_name'] ?? ($nameParts[0] ?? '');
        $lastName = $profile['last_name'] ?? ($nameParts[1] ?? '');
        $videoPath = $profile['intro_video'] ?? '';
        $videoUrl = $videoPath ? (str_starts_with($videoPath, 'http') ? $videoPath : asset($videoPath)) : null;
        $introVideoMaxMb = (int) ceil(((int) config('course.instructor_intro_video_max_kb', 204800)) / 1024);
        $canTeach = (array) ($profile['can_teach'] ?? []);
        $certificates = (array) ($profile['certificates'] ?? []);
        $teachingMaterials = (array) ($profile['teaching_materials'] ?? []);
        $workType = $profile['work_type'] ?? '';

        $educationOptions = [
            'high_school' => __('High School'),
            'associate' => __('Associate'),
            'bachelor' => __('Bachelor'),
            'master' => __('Master'),
            'phd' => __('PhD'),
        ];
        $turkishLevels = [
            'beginner' => __('Beginner'),
            'intermediate' => __('Intermediate'),
            'advanced' => __('Advanced'),
            'native' => __('Native'),
        ];
        $experienceOptions = [
            '0-1' => __('0-1 years'),
            '1-3' => __('1-3 years'),
            '3-5' => __('3-5 years'),
            '5+' => __('5+ years'),
        ];
        $availabilityOptions = [
            'flexible' => __('It\'s changeable'),
            '4' => __('4 lessons'),
            '8' => __('8 lessons'),
            '12' => __('12 lessons'),
            '16' => __('16 lessons'),
        ];
        $teachOptions = [
            'speaking_b1' => __('Speaking (B1)'),
            'general_english_a1' => __('General English (A1)'),
            'kids_6_12' => __('Kids (6-12)'),
            'young_13_18' => __('Young (13-18)'),
            'adults_18' => __('Adults (+18)'),
            'business_english' => __('Business English'),
            'exams' => __('Exams'),
        ];
        $certificateOptions = [
            'none' => __('None'),
            'tesol' => 'TESOL',
            'tefl' => 'TEFL',
            'celta' => 'CELTA',
        ];
        $workOptions = [
            'full_time' => __('Full-Time'),
            'part_time' => __('Part-Time'),
        ];
        $materialOptions = [
            'own' => __('I have my own'),
            'none' => __('I don\'t have any'),
        ];
    @endphp

    <form action="{{ route('instructor.setting.profile.update') }}" method="POST" enctype="multipart/form-data" class="sp-profile-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="email" value="{{ $user->email }}">
        <input type="hidden" name="name" id="full_name" value="{{ $user->name }}">
        <input type="file" name="avatar" id="avatar" hidden accept="image/*">
        <input type="file" name="intro_video" id="intro_video" hidden accept="video/*">

        <div class="sp-profile-media">
            <div class="sp-profile-card sp-profile-card--avatar">
                <img class="preview-avatar" src="{{ asset($user->image) }}" alt="{{ $user->name }}">
                <button type="button" class="sp-upload-btn" onclick="document.getElementById('avatar').click()">
                    {{ __('Profile Image') }}
                </button>
            </div>
            <div class="sp-profile-card sp-profile-card--guide">
                <div class="sp-profile-guide__header">
                    <div class="sp-profile-guide__thumb">
                        <img src="{{ asset($user->image) }}" alt="{{ $user->name }}">
                    </div>
                    <div>
                        <h5>{{ __('Profile Photo') }}</h5>
                        <ul>
                            <li>{{ __('Smile') }}</li>
                            <li>{{ __('Keep the background simple.') }}</li>
                            <li>{{ __('Use portrait photography.') }}</li>
                        </ul>
                    </div>
                </div>
                <p class="sp-profile-guide__note">{{ __('JPG or PNG format, maximum 5 MB file.') }}</p>
            </div>
        </div>

        <div class="sp-profile-video-block">
            <div class="sp-profile-video-head">
                <div>
                    <h4>{{ __('Öğretmen Tanıtım Videosu') }}</h4>
                    <p>{{ __('Bu video yalnızca öğretmen profilindeki tanıtım alanında gösterilir.') }}</p>
                    <p>{{ __('Desteklenen formatlar: MP4, WebM, OGG, MOV. Maksimum :size MB.', ['size' => $introVideoMaxMb]) }}</p>
                </div>
            </div>
            <label class="sp-profile-upload" for="intro_video">
                <i class="fas fa-upload" aria-hidden="true"></i>
                <span>{{ __('Tanıtım videosu yükle') }}</span>
            </label>
            <div class="sp-profile-video">
                @if ($videoUrl)
                    <video id="intro_video_preview" controls preload="metadata">
                        <source src="{{ $videoUrl }}">
                    </video>
                @else
                    <div class="sp-profile-video-empty">
                        <span>{{ __('Henüz tanıtım videosu yok') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="sp-profile-grid">
            <div class="form-grp">
                <label for="first_name">{{ __('Name') }} <code>*</code></label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $firstName) }}">
            </div>
            <div class="form-grp">
                <label for="last_name">{{ __('Surname') }}</label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $lastName) }}">
            </div>
            <div class="form-grp">
                <label for="phone">{{ __('Phone Number') }}</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="form-grp">
                <label for="country_id">{{ __('Country') }}</label>
                <select id="country_id" name="country_id" class="form-select">
                    <option value="">{{ __('Select') }}</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @selected((int) old('country_id', $user->country_id) === (int) $country->id)>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-grp">
                <label for="education">{{ __('Education') }}</label>
                <select id="education" name="education" class="form-select">
                    <option value="">{{ __('Select') }}</option>
                    @foreach ($educationOptions as $key => $label)
                        <option value="{{ $key }}" @selected(old('education', $profile['education'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-grp">
                <label for="university">{{ __('University') }}</label>
                <input id="university" name="university" type="text" value="{{ old('university', $profile['university'] ?? '') }}">
            </div>
            <div class="form-grp">
                <label for="turkish_level">{{ __('Turkish Language Level') }}</label>
                <select id="turkish_level" name="turkish_level" class="form-select">
                    <option value="">{{ __('Select') }}</option>
                    @foreach ($turkishLevels as $key => $label)
                        <option value="{{ $key }}" @selected(old('turkish_level', $profile['turkish_level'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-grp">
                <label for="experience_years">{{ __('Experience') }}</label>
                <select id="experience_years" name="experience_years" class="form-select">
                    <option value="">{{ __('Select') }}</option>
                    @foreach ($experienceOptions as $key => $label)
                        <option value="{{ $key }}" @selected(old('experience_years', $profile['experience_years'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-grp">
                <label for="availability_per_month">{{ __('Availability (Lessons/Month)') }}</label>
                <select id="availability_per_month" name="availability_per_month" class="form-select">
                    <option value="">{{ __('Select') }}</option>
                    @foreach ($availabilityOptions as $key => $label)
                        <option value="{{ $key }}" @selected(old('availability_per_month', $profile['availability_per_month'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-grp">
                <label for="age">{{ __('Age') }}</label>
                <input
                    id="age"
                    name="age"
                    type="number"
                    min="0"
                    max="150"
                    step="1"
                    value="{{ old('age', $user->age ?? '') }}">
            </div>
            <div class="form-grp">
                <label for="birth_date">{{ __('Birth Date') }}</label>
                <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', $profile['birth_date'] ?? '') }}">
            </div>
            <div class="form-grp">
                <label for="major">{{ __('Major') }}</label>
                <input id="major" name="major" type="text" value="{{ old('major', $profile['major'] ?? $user->job_title) }}">
            </div>
            <div class="form-grp form-grp--full">
                <label for="short_bio">{{ __('About Me') }}</label>
                <textarea id="short_bio" name="short_bio" rows="4">{{ old('short_bio', $user->short_bio) }}</textarea>
            </div>
            <div class="form-grp form-grp--full">
                <label for="bio">{{ __('Teaching Style') }}</label>
                <textarea id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
            </div>
        </div>

        <div class="sp-profile-section">
            <h4>{{ __('Agreement') }}</h4>
            <div class="sp-profile-grid">
                <div class="form-grp">
                    <label for="identity_number">{{ __('ID Number') }}</label>
                    <input id="identity_number" name="identity_number" type="text" value="{{ old('identity_number', $profile['identity_number'] ?? '') }}">
                </div>
                <div class="form-grp">
                    <label for="account_holder_name">{{ __('Account Holder\'s Name') }}</label>
                    <input id="account_holder_name" name="account_holder_name" type="text" value="{{ old('account_holder_name', $profile['account_holder_name'] ?? '') }}">
                </div>
                <div class="form-grp">
                    <label for="bank_number">{{ __('Bank Number') }}</label>
                    <input id="bank_number" name="bank_number" type="text" value="{{ old('bank_number', $profile['bank_number'] ?? '') }}">
                </div>
                <div class="form-grp form-grp--full">
                    <label for="agreement_address">{{ __('Address') }}</label>
                    <textarea id="agreement_address" name="agreement_address" rows="3">{{ old('agreement_address', $profile['agreement_address'] ?? $user->address) }}</textarea>
                </div>
            </div>
        </div>

        <div class="sp-profile-checks">
            <div class="sp-profile-check-group">
                <h5>{{ __('I can teach') }}</h5>
                @foreach ($teachOptions as $key => $label)
                    <label class="sp-check">
                        <input type="checkbox" name="can_teach[]" value="{{ $key }}" @checked(in_array($key, $canTeach, true))>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <div class="sp-profile-check-group">
                <h5>{{ __('Certificates') }}</h5>
                @foreach ($certificateOptions as $key => $label)
                    <label class="sp-check">
                        <input type="checkbox" name="certificates[]" value="{{ $key }}" @checked(in_array($key, $certificates, true))>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <div class="sp-profile-check-group">
                <h5>{{ __('I\'d like to work') }}</h5>
                @foreach ($workOptions as $key => $label)
                    <label class="sp-check">
                        <input type="radio" name="work_type" value="{{ $key }}" @checked($workType === $key)>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <div class="sp-profile-check-group">
                <h5>{{ __('Teaching Materials') }}</h5>
                @foreach ($materialOptions as $key => $label)
                    <label class="sp-check">
                        <input type="checkbox" name="teaching_materials[]" value="{{ $key }}" @checked(in_array($key, $teachingMaterials, true))>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="sp-form-actions">
            <button type="submit" class="btn">{{ __('Update') }}</button>
        </div>
    </form>
</div>

@push('styles')
    <style>
        .sp-profile-form{display:grid;gap:22px;}
        .sp-profile-media{display:grid;grid-template-columns:220px 1fr;gap:18px;align-items:start;}
        .sp-profile-card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:14px;box-shadow:0 10px 20px rgba(0,0,0,0.04);} 
        .sp-profile-card--avatar{display:grid;gap:12px;justify-items:center;text-align:center;}
        .sp-profile-card--avatar img{width:160px;height:160px;border-radius:16px;object-fit:cover;}
        .sp-profile-card--guide{display:grid;gap:12px;}
        .sp-profile-guide__header{display:flex;gap:14px;align-items:flex-start;}
        .sp-profile-guide__thumb{width:64px;height:64px;border-radius:16px;overflow:hidden;flex:0 0 auto;}
        .sp-profile-guide__thumb img{width:100%;height:100%;object-fit:cover;}
        .sp-profile-guide__header h5{margin:0 0 6px;font-weight:900;color:#111827;font-size:14px;}
        .sp-profile-guide__header ul{margin:0;padding-left:18px;color:#6b7280;font-weight:700;font-size:12px;}
        .sp-profile-guide__note{margin:0;color:#6b7280;font-weight:700;font-size:12px;}
        .sp-upload-btn{border:0;border-radius:10px;padding:8px 12px;background:#f6a105;color:#fff;font-weight:800;}
        .sp-upload-btn--ghost{background:#fff;color:#111827;border:1px solid #e5e7eb;}

        .sp-profile-video-block{display:grid;gap:12px;}
        .sp-profile-video-head{display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap;}
        .sp-profile-video-head h4{margin:0;font-weight:900;color:#111827;}
        .sp-profile-video-head p{margin:4px 0 0;color:#6b7280;font-weight:700;font-size:13px;}
        .sp-profile-upload{
            border:2px dashed #e5e7eb;
            border-radius:12px;
            padding:16px;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:10px;
            color:#64748b;
            font-weight:800;
            cursor:pointer;
            background:#f8fafc;
        }
        .sp-profile-upload i{color:#6b7280;}
        .sp-profile-video{border-radius:14px;overflow:hidden;background:#0f172a;aspect-ratio:16/9;display:grid;place-items:center;}
        .sp-profile-video video{width:100%;height:100%;object-fit:cover;display:block;}
        .sp-profile-video-empty{color:#fff;font-weight:800;}

        .sp-profile-grid{display:grid;grid-template-columns:repeat(2, minmax(0, 1fr));gap:16px;}
        .sp-profile-grid .form-grp{margin-bottom:0;}
        .sp-profile-grid .form-grp--full{grid-column:1/-1;}
        .sp-profile-grid input,
        .sp-profile-grid select,
        .sp-profile-grid textarea{
            width:100%;
            border-radius:12px;
            border:1px solid #e5e7eb;
            padding:10px 12px;
            font-weight:700;
            color:#111827;
            background:#fff;
        }
        .sp-profile-grid textarea{min-height:110px;}
        .sp-profile-grid input:focus,
        .sp-profile-grid select:focus,
        .sp-profile-grid textarea:focus{
            outline:none;
            border-color:#f6a105;
            box-shadow:0 0 0 4px rgba(246,161,5,.2);
        }
        .sp-profile-grid label{font-weight:800;color:#111827;margin-bottom:6px;display:block;}

        .sp-profile-section{margin-top:4px;}
        .sp-profile-section h4{margin:0 0 14px;font-weight:900;color:#111827;}

        .sp-profile-checks{display:grid;grid-template-columns:repeat(4, minmax(0, 1fr));gap:18px;}
        .sp-profile-check-group h5{margin:0 0 10px;font-weight:900;color:#111827;font-size:13px;}
        .sp-check{display:flex;align-items:center;gap:8px;font-weight:700;color:#111827;margin-bottom:6px;}
        .sp-check input{width:14px;height:14px;accent-color:#f6a105;}

        @media(max-width:991px){
            .sp-profile-media{grid-template-columns:1fr;}
            .sp-profile-grid{grid-template-columns:1fr;}
            .sp-profile-checks{grid-template-columns:repeat(2, minmax(0, 1fr));}
        }
        @media(max-width:575px){
            .sp-profile-checks{grid-template-columns:1fr;}
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const firstNameInput = document.getElementById('first_name');
            const lastNameInput = document.getElementById('last_name');
            const fullNameInput = document.getElementById('full_name');
            const syncFullName = () => {
                if (!fullNameInput) return;
                const first = (firstNameInput?.value || '').trim();
                const last = (lastNameInput?.value || '').trim();
                fullNameInput.value = [first, last].filter(Boolean).join(' ').trim();
            };
            if (firstNameInput) firstNameInput.addEventListener('input', syncFullName);
            if (lastNameInput) lastNameInput.addEventListener('input', syncFullName);
            syncFullName();

            const bindPreview = (inputId, targetSelector, type) => {
                const input = document.getElementById(inputId);
                const target = document.querySelector(targetSelector);
                if (!input || !target) return;
                input.addEventListener('change', () => {
                    const file = input.files && input.files[0];
                    if (!file) return;
                    const url = URL.createObjectURL(file);
                    if (type === 'video') {
                        target.innerHTML = '<video controls preload="metadata"><source></video>';
                        const video = target.querySelector('video');
                        const source = target.querySelector('source');
                        source.src = url;
                        video.load();
                    } else {
                        target.src = url;
                    }
                });
            };
            bindPreview('avatar', '.preview-avatar');
            bindPreview('intro_video', '.sp-profile-video', 'video');
        })();
    </script>
@endpush
