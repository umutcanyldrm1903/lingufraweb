<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'          => (int) $this->id,
            'title'       => (string) $this->title,
            'description' => (string) $this->description,
            'start_time'  => (string) $this->start_time,
            'end_time'    => (string) $this->end_time,
            'duration'    => (string) convertMinutesToHoursAndMinutes($this->duration),
            'is_live_now' => (string) $this->is_live_now,
            'type'        => (string) $this->live->type,
            'meeting_id'  => (string) $this->live->meeting_id,
            'password'    => (string) $this->live->password,
            'join_url'    => (string) $this->live->join_url,
        ];
    }
}
