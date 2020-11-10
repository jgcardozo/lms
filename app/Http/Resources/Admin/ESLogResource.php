<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ESLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "user" => new ESUserResource($this),
            "cohorts" => $this->user !== null ? new ESCohortsResource($this->user->cohorts) : [],
            "subject" => new ESSubjectResource($this),
            "activity" => new ESActivityResource($this->activity),
            "action" => new ESActionResource($this->action),
            "created_at" => $this->created_at->format("Y-m-d H:i:s"),
        ];
    }
}
