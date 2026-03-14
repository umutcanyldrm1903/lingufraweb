<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StudentProfileUpdateRequest extends FormRequest
{
    function __construct()
    {
        setFormTabStep('profile_tab', 'profile');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $introVideoMaxKb = (int) config('course.instructor_intro_video_max_kb', 204800);

        return [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2000'],
            'image' => ['nullable', 'image', 'max:2000'],
            'cover' => ['nullable', 'image', 'max:2000'],
            'intro_video' => ['nullable', 'file', 'mimes:mp4,webm,ogg,mov', 'max:' . $introVideoMaxKb],
            'phone' => ['nullable', 'string', 'max:30'],
            'age' => ['nullable', 'integer', 'max:150'],
            'gender' => ['nullable', 'in:male,female'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'short_bio' => ['nullable', 'string', 'max:2000'],
            'bio' => ['nullable', 'string', 'max:10000'],
            'first_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'country_id' => ['nullable', 'integer'],
            'education' => ['nullable', 'string', 'max:255'],
            'university' => ['nullable', 'string', 'max:255'],
            'turkish_level' => ['nullable', 'string', 'max:100'],
            'experience_years' => ['nullable', 'string', 'max:100'],
            'availability_per_month' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'major' => ['nullable', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:255'],
            'account_holder_name' => ['nullable', 'string', 'max:255'],
            'bank_number' => ['nullable', 'string', 'max:255'],
            'agreement_address' => ['nullable', 'string', 'max:255'],
            'can_teach' => ['nullable', 'array'],
            'can_teach.*' => ['string', 'max:100'],
            'certificates' => ['nullable', 'array'],
            'certificates.*' => ['string', 'max:100'],
            'work_type' => ['nullable', 'string', 'max:100'],
            'teaching_materials' => ['nullable', 'array'],
            'teaching_materials.*' => ['string', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = trim((string) $this->input('name', ''));
        if ($name !== '') {
            return;
        }

        $firstName = trim((string) $this->input('first_name', ''));
        $lastName = trim((string) $this->input('last_name', ''));
        $fallbackName = trim($firstName . ' ' . $lastName);

        if ($fallbackName !== '') {
            $this->merge(['name' => $fallbackName]);
        }
    }

    // custom validation error messages
    function messages(): array
    {
        $introVideoMaxMb = (int) ceil(((int) config('course.instructor_intro_video_max_kb', 204800)) / 1024);

        return [
            'name.required' => __('The name field is required'),
            'name.string' => __('The name must be a string'),
            'name.max' => __('The name may not be greater than 50 characters.'),
            'email.required' => __('The email field is required'),
            'email.email' => __('The email must be a valid email address'),
            'email.max' => __('The email may not be greater than 255 characters'),
            'image.image' => __('The image must be an image'),
            'image.max' => __('The image may not be greater than 2000 kilobytes'),
            'cover.image' => __('The cover must be an image'),
            'cover.max' => __('The cover may not be greater than 2000 kilobytes'),
            'intro_video.max' => __('The intro video may not be greater than :size MB.', ['size' => $introVideoMaxMb]),
            'phone.string' => __('The phone must be a string'),
            'phone.max' => __('The phone may not be greater than 30 characters'),
            'age.integer' => __('The age must be an integer'),
            'age.max' => __('The age may not be greater than 150'),
        ];
    }

}
