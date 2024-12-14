<?php

namespace App\Http\Resources\Api;

use App\Models\MainEqub;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\MainEqubResource;

class EqubTypeResource extends JsonResource
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
            'main_equb_id' => $this->main_equb_id,
            'round' => $this->round,
            'status' => $this->status,
            'active' => $this->active,
            'deleted_at' => $this->deleted_at,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
            'remark' => $this->remark,
            'rote' => $this->rote,
            'type' => $this->type,
            'lottery_date' => (new \DateTime($this->lottery_date))->format('Y-m-d H:i:s'),
            'terms' => $this->terms,
            'quota' => $this->quota,
            'amount' => (int) $this->amount,
            'total_amount' => (int) $this->total_amount,
            'total_members' => $this->total_members,
            'expected_members' => (int) $this->expected_members,
            'remaining_quota' => $this->remaining_quota,
            'start_date' => (new \DateTime($this->start_date))->format('Y-m-d H:i:s'),
            'end_date' => (new \DateTime($this->end_date))->format('Y-m-d H:i:s'),
            'image' =>  $this->image ? asset('storage/' . $this->image) : null,
            // 'mainEqub' =>  MainEqubResource::collection($this->whenLoaded('mainEqub')),
            'mainEqub' => new MainEqubResource($this->whenLoaded('mainEqub')),
            
        ];
    }
}
