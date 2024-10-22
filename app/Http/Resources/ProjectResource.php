<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'title'=> $this->title,
            'project_type'=> $this->project_type,
            'group_name'=> $this->group_name,   
            'year_published'=> $this->year_published,
            'manuscript'=> $this->manuscript,
            'poster'=> $this->poster,
            'video'=> $this->video,
            'zip'=> $this->zip,
            'authors' => [
                $this->member_0,
                $this->member_1,
                $this->member_2,
                $this->member_3,
            ],
        ];
    }
}
