<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class MoodleTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $request->user()->version >= '1.1.0' ? $this->id : Str::before($this->remote_id, '@'),
            'title' => $this->title,
            'due' => $this->due_date,
            'class' => $this->class,
            'description' => $this->description,
            'completed_at' => $this->completed_at
        ];
    }
}
