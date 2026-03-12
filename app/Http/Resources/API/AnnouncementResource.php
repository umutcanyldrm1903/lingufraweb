<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'title'        => (string) $this->title,
            'announcement' => (string) $this->announcement,
            'created_at'   => (string) formatDate($this->created_at),
            'instructor'   => new InstructorResource($this->instructor),
        ];
    }
}
