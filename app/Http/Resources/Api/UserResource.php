<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'password' => $this->password,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'enabled' => $this->enabled,
            'token' => $this->token,
            'fcm_id' => $this->fcm_id,
            'address' => $this->address,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'created_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
