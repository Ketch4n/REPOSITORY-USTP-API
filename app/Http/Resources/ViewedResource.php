<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewedResource extends JsonResource
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
            'project_id'=> $this->project_id,
            'user_id'=> $this->user_id,
            'file_name'=> $this->file_name,
            
        ];
    }
}
