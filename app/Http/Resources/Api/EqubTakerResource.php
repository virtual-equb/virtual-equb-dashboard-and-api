<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class EqubTakerResource extends JsonResource
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
            'equb_id' => $this->equb_id,
            'payment_type' => $this->payment_type,
            'amount' => $this->amount,
            'remaining_amount' => $this->remaining_amount,
            'status' => $this->status,
            'paid_by' => $this->paid_by,
            'total_payment' => $this->total_payment,
            'remaining_payment' => $this->remaining_payment,
            'cheque_amount' => $this->cheque_amount,
            'cheque_bank_name' => $this->cheque_bank_name,
            'cheque_description' => $this->cheque_description,
            'remark' => $this->remark,
            'transaction_number' => $this->transaction_number,
            'paid_date' => $this->paid_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'member' => new MemberResource($this->whenLoaded('member')),
            'equb' => new EqubResource($this->whenLoaded('equb'))
        ];
    }
}
