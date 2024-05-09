<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ESUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $this is a Log instance
        return [
            "id" => $this->user_id,
            "type" => $this->activity_id == 7 ? "admin" : "user",
            "name" => $this->user !== null ? $this->user->name : null,
            "email" => $this->user !== null ? $this->user->email : $this->deleted_user,
			"contact" => $this->user !== null ? $this->user->contact_id : null
        ];
    }
}
