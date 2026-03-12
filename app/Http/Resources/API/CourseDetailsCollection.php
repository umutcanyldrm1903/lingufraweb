<?php

namespace App\Http\Resources\API;

use App\Http\Resources\API\ChapterResource;
use App\Http\Resources\API\CurrentProgressResource;
use App\Http\Resources\API\InstructorResource;
use App\Models\CourseProgress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseDetailsCollection extends JsonResource {
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array {
        if ($request->routeIs('api.learning')) {
            $user_id = auth()->id();
            $currentProgress = CourseProgress::where('user_id', $user_id)
                ->where('course_id', $this->id)
                ->where('current', 1)
                ->orderBy('id', 'desc')
                ->first();

            if (!$currentProgress) {
                $lessonId = @$this->chapters?->first()?->chapterItems()?->first()?->lesson->id;
                if ($lessonId) {
                    $currentProgress = CourseProgress::create([
                        'user_id'    => $user_id,
                        'course_id'  => $this->id,
                        'chapter_id' => $this->chapters->first()->id,
                        'lesson_id'  => $lessonId,
                        'current'    => 1,
                    ]);
                }
            }

            $alreadyWatchedLectures = CourseProgress::where('user_id', $user_id)
                ->where('course_id', $this->id)
                ->where('type', 'lesson')
                ->where('watched', 1)
                ->pluck('lesson_id')
                ->toArray();

            $alreadyCompletedQuiz = CourseProgress::where('user_id', $user_id)
                ->where('course_id', $this->id)
                ->where('type', 'quiz')
                ->where('watched', 1)
                ->pluck('lesson_id')
                ->toArray();

            return [
                'thumbnail'                => (string) $this->thumbnail,
                'title'                    => (string) $this->title,
                'description'    => (string) $this->description,
                'instructor'               => new InstructorResource($this->instructor),
                'curriculums'              => ChapterResource::collection($this->chapters),
                'current_progress'         => new CurrentProgressResource($currentProgress),
                'already_watched_lectures' => (array) $alreadyWatchedLectures,
                'already_completed_quiz'   => (array) $alreadyCompletedQuiz,
            ];
        }
        $currency = strtoupper($request->query('currency'));
        $price = $this->price == 0 ? (int) $this->price : (string) apiCurrency($this->price, $currency);
        $discount = $this->discount == 0 ? (int) $this->discount : (string) apiCurrency($this->discount, $currency);
        return [
            'demo_video_source'     => (string) $this->demo_video_storage,
            'demo_video'     => (string) generateVideoEmbedUrl($this->demo_video_source, $this->demo_video_storage),
            'thumbnail'      => (string) $this->thumbnail,
            'is_wishlist'    => (bool) $this->is_wishlist,
            'title'          => (string) $this->title,
            'slug'           => (string) $this->slug,
            'instructor'     => new InstructorResource($this->instructor),
            'average_rating' => (float) $this->average_rating ?? 0,
            'reviews_count'  => (int) $this->reviews_count ?? 0,
            'students'       => (int) $this->enrollments_count ?? 0,
            'last_updated'   => (string) formatDate($this->updated_at),
            'duration'       => (string) convertMinutesToHoursAndMinutes($this->duration),
            'certificate'    => (bool) $this->certificate,
            'lessons_count'  => (int) $this->lessons_count ?? 0,
            'quizzes_count'  => (int) $this->quizzes_count ?? 0,
            'languages'      => (string) $this->languages->pluck('language.name')->implode(', '),
            'price'          => $price,
            'discount'       => $discount,
            'description'    => (string) $this->description,
            'curriculums'    => ChapterResource::collection($this->chapters),
        ];
    }
}
