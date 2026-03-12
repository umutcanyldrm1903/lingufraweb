<div class="tab-pane fade show {{ session('profile_tab') == 'schedule' ? 'active' : '' }}" id="settings-schedule" role="tabpanel" aria-labelledby="settings-schedule-tab" tabindex="0">
    @php
        use Carbon\Carbon;

        $slotDuration = 40;
        $slotGap = 10;
        $slotStep = $slotDuration + $slotGap;
        $slotStart = Carbon::createFromTime(0, 0);
        $slotEnd = Carbon::createFromTime(23, 20);
        $timeSlots = [];
        $cursor = $slotStart->copy();
        while ($cursor->lte($slotEnd)) {
            $timeSlots[] = $cursor->copy();
            $cursor->addMinutes($slotStep);
        }

        $days = [
            0 => __('Monday'),
            1 => __('Tuesday'),
            2 => __('Wednesday'),
            3 => __('Thursday'),
            4 => __('Friday'),
            5 => __('Saturday'),
            6 => __('Sunday'),
        ];

        $availabilityMap = [];
        foreach ($availabilities as $slot) {
            $key = $slot->day_of_week . '|' . substr($slot->start_time, 0, 5) . '|' . substr($slot->end_time, 0, 5);
            $availabilityMap[$key] = true;
        }
    @endphp

    <p class="sp-schedule__note">{{ __('Please click to indicate your available hours so that we can create your course calendar.') }}</p>

    <form method="POST" action="{{ route('instructor.setting.schedule.update') }}" class="sp-schedule-grid-form">
        @csrf
        <div class="sp-schedule-grid">
            @foreach ($days as $dayIndex => $dayLabel)
                <div class="sp-schedule-col">
                    <div class="sp-schedule-col__title">{{ $dayLabel }}</div>
                    <div class="sp-schedule-col__slots">
                        @foreach ($timeSlots as $slotTime)
                            @php
                                $startLabel = $slotTime->format('H:i');
                                $endLabel = $slotTime->copy()->addMinutes($slotDuration)->format('H:i');
                                $value = $dayIndex . '|' . $startLabel . '|' . $endLabel;
                                $checked = isset($availabilityMap[$value]);
                            @endphp
                            <label class="sp-slot {{ $checked ? 'is-active' : '' }}">
                                <input type="checkbox" name="slots[]" value="{{ $value }}" {{ $checked ? 'checked' : '' }}>
                                <span>{{ $startLabel }}-{{ $endLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="sp-form-actions">
            <button type="submit" class="btn">{{ __('Update') }}</button>
        </div>
    </form>
</div>

@push('styles')
    <style>
        .sp-schedule__note{color:#6b7280;font-weight:600;margin-bottom:18px;}
        .sp-schedule-grid{display:grid;grid-template-columns:repeat(7, minmax(0,1fr));gap:14px;}
        .sp-schedule-col__title{text-align:center;font-weight:800;color:#111827;margin-bottom:10px;}
        .sp-schedule-col__slots{display:grid;gap:8px;}
        .sp-slot{display:block;cursor:pointer;}
        .sp-slot input{display:none;}
        .sp-slot span{
            display:block;
            border-radius:10px;
            border:1px solid #e5e7eb;
            background:#f3f4f6;
            color:#111827;
            font-weight:700;
            font-size:12px;
            text-align:center;
            padding:6px 8px;
            transition:background .2s ease,border-color .2s ease;
        }
        .sp-slot:hover span{border-color:#f6a105;}
        .sp-slot.is-active span,
        .sp-slot input:checked + span{
            background:#d1d5db;
            color:#111827;
            border-color:#cbd5e1;
        }

        @media (max-width: 991px){
            .sp-schedule-grid{grid-template-columns:repeat(4, minmax(0,1fr));}
        }
        @media (max-width: 767px){
            .sp-schedule-grid{grid-template-columns:repeat(2, minmax(0,1fr));}
        }
        @media (max-width: 575px){
            .sp-schedule-grid{grid-template-columns:1fr;}
        }
    </style>
@endpush
