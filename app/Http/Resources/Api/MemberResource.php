<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'status' => $this->status,
            'email' => $this->email,
            'city' => $this->city,
            'subcity' => $this->subcity,
            'woreda' => $this->woreda,
            'house_number' => $this->house_number,
            'specific_location' => $this->specific_location,
            'profile_photo_path' => $this->profile_photo_path ? asset('storage/' . $this->profile_photo_path) : null,
            'verified' => $this->verified,
            'approved_by' => $this->approved_by,
            'approved_date' => (new \DateTime($this->approved_date))->format('Y-m-d H:i:s'),
            'remark' => $this->remark,
            'rating' => $this->rating,
            'date_of_birth' => (new \DateTime($this->date_of_birth))->format('Y-m-d H:i:s'),
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
            'equbs' => EqubResource::collection($this->whenLoaded('equbs')),

        ];
    }
}
