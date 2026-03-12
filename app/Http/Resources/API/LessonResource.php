<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        if ($request->routeIs('api.get-file-info') || $request->routeIs('api.free-lesson')) {
            return [
                'id'              => (int) $this->id,
                'title'           => (string) $this->title,
                'description'     => (string) $this->description,
                'file_path'       => (string) generateVideoEmbedUrl($this->file_path, $this->storage, $this->file_type),
                'storage'         => (string) $this->storage,
                'file_type'       => (string) $this->file_type,
                'duration'        => (string) convertMinutesToHoursAndMinutes($this->duration),
                'is_downloadable' => (bool) $this->downloadable,
            ];
        }
        return [
            'id'        => (int) $this->id,
            'title'     => (string) $this->title,
            'file_type' => (string) $this->file_type,
            'duration'  => (string) convertMinutesToHoursAndMinutes($this->duration),
            'is_free'   => (bool) $this->is_free,
        ];
    }
}
