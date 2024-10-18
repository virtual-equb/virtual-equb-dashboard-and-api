<?php

namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
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
            'name' => 'required',
            'created_by' => 'exists:users,id',
            'active' => 'nullable',
            'remark' => 'nullable',
            'status' => 'nullable',
            'country_id' => 'exists:countries,id'
        ];
    }
}
