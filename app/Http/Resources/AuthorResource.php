<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
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
            'group_name'=> $this->group_name,
            'project_id'=> $this->project_id,
            'title'=> $this->title,
            'project_type'=> $this->project_type,
            'year_published'=> $this->year_published,
            'author' => [
                $this->member_0,
                $this->member_1,
                $this->member_2,
                $this->member_3,
            ],
            // 'member_0'=> $this->member_0,
            // 'member_1'=> $this->member_1,
            // 'member_2'=> $this->member_2,
            // 'member_3'=> $this->member_3,
        ];
    }
}
