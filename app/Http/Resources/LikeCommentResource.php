<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'=> $this->id,
            'rating'=> $this->rating,
            'project_id'=> $this->project_id,
            'user_id'=> $this->user_id,
            'comment'=> $this->comment,
            'username'=> $this->username,
            'email'=> $this->email,
            'created_at'=> $this->created_at
           
        ];
    }
}
