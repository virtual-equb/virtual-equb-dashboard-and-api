<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class EqubResource extends JsonResource
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
            'member_id' => $this->member_id,
            'equb_type_id' => $this->equb_type_id,
            'amount' => $this->amount,
            'total_amount' => $this->total_amount,
            'start_date' => (new \DateTime($this->start_date))->format('Y-m-d H:i:s'),
            'end_date' => (new \DateTime($this->end_date))->format('Y-m-d H:i:s'),
            'lottery_date' => $this->lottery_date,
            'status' => $this->status,
            'timeline' => $this->timeline,
            'check_for_draw' => $this->check_for_draw,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
            'equbType' => new EqubTypeResource($this->whenLoaded('equbType')),
            'member' => new MemberResource($this->whenLoaded('member'))
        ];
    }
}
