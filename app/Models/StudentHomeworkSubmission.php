<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentHomeworkSubmission extends Model
{
    protected $fillable = [
        'student_homework_id',
        'student_id',
        'submission_path',
        'submission_name',
        'note',
        'submitted_at',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function homework(): BelongsTo
    {
        return $this->belongsTo(StudentHomework::class, 'student_homework_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public static function parseNotePayload(?string $raw): array
    {
        $value = trim((string) $raw);
        if ($value === '') {
            return [
                'student_note' => '',
                'instructor_note' => '',
                'reviewed_at' => null,
            ];
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return [
                'student_note' => trim((string) ($decoded['student_note'] ?? '')),
                'instructor_note' => trim((string) ($decoded['instructor_note'] ?? '')),
                'reviewed_at' => !empty($decoded['reviewed_at'])
                    ? Carbon::parse((string) $decoded['reviewed_at'])
                    : null,
            ];
        }

        return [
            'student_note' => $value,
            'instructor_note' => '',
            'reviewed_at' => null,
        ];
    }

    public static function buildNotePayload(
        ?string $studentNote = null,
        ?string $instructorNote = null,
        DateTimeInterface|string|null $reviewedAt = null,
    ): ?string {
        $studentNote = trim((string) $studentNote);
        $instructorNote = trim((string) $instructorNote);

        $reviewedAtValue = null;
        if ($reviewedAt instanceof DateTimeInterface) {
            $reviewedAtValue = $reviewedAt->format(DateTimeInterface::ATOM);
        } elseif (is_string($reviewedAt) && trim($reviewedAt) !== '') {
            $reviewedAtValue = Carbon::parse($reviewedAt)->toAtomString();
        }

        if ($studentNote === '' && $instructorNote === '' && $reviewedAtValue === null) {
            return null;
        }

        if ($instructorNote === '' && $reviewedAtValue === null) {
            return $studentNote !== '' ? $studentNote : null;
        }

        return json_encode([
            'student_note' => $studentNote,
            'instructor_note' => $instructorNote,
            'reviewed_at' => $reviewedAtValue,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
