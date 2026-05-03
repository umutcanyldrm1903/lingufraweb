<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $plan = null;
        $trialLessonAvailable = false;
        $trialLessonStatus = null;

        if (Schema::hasTable('user_plans')) {
            $planRow = DB::table('user_plans')
                ->where('user_id', $this->id)
                ->orderByDesc('last_order_id')
                ->orderByDesc('id')
                ->first();
            if ($planRow) {
                $assignedName = null;
                if (!empty($planRow->assigned_instructor_id)) {
                    $assignedName = DB::table('users')
                        ->where('id', $planRow->assigned_instructor_id)
                        ->value('name');
                }

                $plan = [
                    'title' => (string) ($planRow->plan_title ?? ''),
                    'lessons_remaining' => (int) ($planRow->lessons_remaining ?? 0),
                    'cancel_remaining' => (int) ($planRow->cancel_remaining ?? 0),
                    'assigned_instructor_name' => $assignedName ? (string) str($assignedName)->before(' ') : null,
                ];
            }
        }

        if (($this->role ?? '') !== 'instructor' && Schema::hasTable('trial_lesson_requests')) {
            $trialLessonStatus = DB::table('trial_lesson_requests')
                ->where('user_id', $this->id)
                ->orderByDesc('id')
                ->value('status');

            $hasUsedTrial = in_array((string) $trialLessonStatus, ['booked', 'completed', 'used'], true);
            $hasCredits = $plan !== null && (int) ($plan['lessons_remaining'] ?? 0) > 0;
            $trialLessonAvailable = !$hasCredits && !$hasUsedTrial;
        }

        $instructorProfile = is_array($this->instructor_profile) ? $this->instructor_profile : [];
        $introVideoPath = (string) ($instructorProfile['intro_video'] ?? '');
        $introVideoUrl = '';
        if ($introVideoPath !== '') {
            $introVideoUrl = str_starts_with($introVideoPath, 'http') ? $introVideoPath : asset($introVideoPath);
        }

        return [
            'id'         => (int) $this->id,
            'name'       => (string) $this->name,
            'first_name' => (string) $this->first_name,
            'email'      => (string) $this->email,
            'role'       => (string) ($this->role ?? ''),
            'phone'      => (string) $this->phone,
            'age'        => (int) $this->age,
            'image'      => (string) $this->image,
            'job_title'  => (string) $this->job_title,
            'short_bio'  => (string) $this->short_bio,
            'bio'        => (string) $this->bio,
            'gender'     => (string) $this->gender,
            'country_id' => (int) $this->country_id,
            'state'      => (string) $this->state,
            'city'       => (string) $this->city,
            'address'    => (string) $this->address,
            "facebook"   => (string) $this->facebook,
            "twitter"    => (string) $this->twitter,
            "linkedin"   => (string) $this->linkedin,
            "website"    => (string) $this->website,
            "github"     => (string) $this->github,
            'instructor_profile' => $instructorProfile,
            'intro_video_url' => $introVideoUrl,
            'plan'       => $plan,
            'trial_lesson_available' => $trialLessonAvailable,
            'trial_lesson_status' => $trialLessonStatus,
        ];
    }
}
