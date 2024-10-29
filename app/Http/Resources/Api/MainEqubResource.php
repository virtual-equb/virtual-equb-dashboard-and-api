<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\EqubTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MainEqubResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_by' => $this->created_by,
            'remark' => $this->remark,
            'status' => $this->status,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null, // Generates the full URL
            'subEqub' => EqubTypeResource::collection($this->whenLoaded('subEqub')),
            // 'subEqub' => $this->subEqub
        ];
    }
}
