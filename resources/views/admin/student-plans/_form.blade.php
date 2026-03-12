@php
    /** @var \App\Models\StudentPlan|null $plan */
    $plan = $plan ?? null;
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Plan Key') }} <code>*</code></label>
            <input name="key" type="text" class="form-control @error('key') is-invalid @enderror"
                value="{{ old('key', $plan?->key) }}" placeholder="plan_3m">
            @error('key')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Sort Order') }}</label>
            <input name="sort_order" type="number" min="0"
                class="form-control @error('sort_order') is-invalid @enderror"
                value="{{ old('sort_order', $plan?->sort_order ?? 0) }}">
            @error('sort_order')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Title (Payment)') }} <code>*</code></label>
            <input name="title" type="text" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $plan?->title) }}" placeholder="CORE STARTER">
            @error('title')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Display Title (UI)') }}</label>
            <input name="display_title" type="text" class="form-control @error('display_title') is-invalid @enderror"
                value="{{ old('display_title', $plan?->display_title) }}" placeholder="🥉 CORE STARTER">
            @error('display_title')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{ __('Duration (Months)') }} <code>*</code></label>
            <input name="duration_months" type="number" min="0"
                class="form-control @error('duration_months') is-invalid @enderror"
                value="{{ old('duration_months', $plan?->duration_months ?? 0) }}">
            @error('duration_months')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{ __('Lesson Duration (Minutes)') }} <code>*</code></label>
            <input name="lesson_duration" type="number" min="1"
                class="form-control @error('lesson_duration') is-invalid @enderror"
                value="{{ old('lesson_duration', $plan?->lesson_duration ?? 40) }}">
            @error('lesson_duration')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{ __('Lessons Total') }} <code>*</code></label>
            <input name="lessons_total" type="number" min="0"
                class="form-control @error('lessons_total') is-invalid @enderror"
                value="{{ old('lessons_total', $plan?->lessons_total ?? 0) }}">
            @error('lessons_total')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{ __('Cancel Total') }} <code>*</code></label>
            <input name="cancel_total" type="number" min="0"
                class="form-control @error('cancel_total') is-invalid @enderror"
                value="{{ old('cancel_total', $plan?->cancel_total ?? 0) }}">
            @error('cancel_total')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('List Total (Old Price)') }} <code>*</code></label>
            <input name="old_price" type="number" min="0" step="0.01"
                class="form-control @error('old_price') is-invalid @enderror"
                value="{{ old('old_price', $plan?->old_price ?? 0) }}">
            @error('old_price')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Package Total (Price)') }} <code>*</code></label>
            <input name="price" type="number" min="0" step="0.01"
                class="form-control @error('price') is-invalid @enderror"
                value="{{ old('price', $plan?->price ?? 0) }}">
            @error('price')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Per Lesson Price') }}</label>
            <input name="lesson_price" type="number" min="0" step="0.01"
                class="form-control"
                value="{{ old('lesson_price', ($plan?->lessons_total ?? 0) > 0 ? number_format(((float) ($plan?->price ?? 0)) / max(1, (int) $plan->lessons_total), 2, '.', '') : '') }}">
            <small class="form-text text-muted">
                {{ __('Entering a per-lesson price will auto-calculate the package total.') }}
            </small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{ __('Label') }}</label>
            <input name="label" type="text" class="form-control @error('label') is-invalid @enderror"
                value="{{ old('label', $plan?->label) }}" placeholder="{{ __('Optional') }}">
            @error('label')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-8">
        <div class="form-group">
            <label>{{ __('Subtitle') }}</label>
            <input name="subtitle" type="text" class="form-control @error('subtitle') is-invalid @enderror"
                value="{{ old('subtitle', $plan?->subtitle) }}" placeholder="{{ __('Optional') }}">
            @error('subtitle')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label>{{ __('Tagline') }}</label>
            <textarea name="tagline" rows="3" class="form-control @error('tagline') is-invalid @enderror"
                placeholder="{{ __('Optional') }}">{{ old('tagline', $plan?->tagline) }}</textarea>
            @error('tagline')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input" type="checkbox" id="featured" name="featured" value="1"
                    @checked(old('featured', $plan?->featured) ? true : false)>
                <label for="featured" class="custom-control-label">{{ __('Featured') }}</label>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1"
                    @checked(old('is_active', $plan?->is_active ?? true) ? true : false)>
                <label for="is_active" class="custom-control-label">{{ __('Active') }}</label>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lessonsField = document.querySelector('input[name="lessons_total"]');
            const priceField = document.querySelector('input[name="price"]');
            const lessonPriceField = document.querySelector('input[name="lesson_price"]');

            if (!lessonsField || !priceField || !lessonPriceField) {
                return;
            }

            const toNumber = (value) => {
                if (value === null || value === undefined) {
                    return 0;
                }
                const normalized = value.toString().replace(',', '.').replace(/[^0-9.]/g, '');
                const num = parseFloat(normalized);
                return Number.isFinite(num) ? num : 0;
            };

            const updateTotalFromLesson = () => {
                const lessons = toNumber(lessonsField.value);
                const perLesson = toNumber(lessonPriceField.value);
                if (lessons > 0 && perLesson > 0) {
                    priceField.value = (lessons * perLesson).toFixed(2);
                }
            };

            const updateLessonFromTotal = () => {
                const lessons = toNumber(lessonsField.value);
                const total = toNumber(priceField.value);
                if (lessons > 0 && total > 0) {
                    lessonPriceField.value = (total / lessons).toFixed(2);
                }
            };

            lessonPriceField.addEventListener('input', updateTotalFromLesson);
            priceField.addEventListener('input', updateLessonFromTotal);
            lessonsField.addEventListener('input', function () {
                if (lessonPriceField.value) {
                    updateTotalFromLesson();
                    return;
                }
                updateLessonFromTotal();
            });
        });
    </script>
@endpush
