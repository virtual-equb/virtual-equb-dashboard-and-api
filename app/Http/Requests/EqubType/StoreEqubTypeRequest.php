<?php

namespace App\Http\Requests\EqubType;

use Illuminate\Foundation\Http\FormRequest;

class StoreEqubTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'main_equb_id' => 'required',
            'name' => 'required',
            'round' => 'required',
            'rote' => 'required',
            'type' => 'required',
            'remark' => 'nullable',
            'lottery_date' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'quota' => 'nullable',
            'terms' => 'required',
            'remaining_quota' => 'required',
            'image' => 'nullable'
        ];
    }
}
