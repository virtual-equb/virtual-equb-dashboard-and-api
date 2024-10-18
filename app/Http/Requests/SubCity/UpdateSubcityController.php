<?php

namespace App\Http\Requests\SubCity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubcityController extends FormRequest
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
            'created_by' => 'required|exists:users,id',
            'city_id' => 'required|exists:cities,id',
            'active' => 'nullable',
            'remark' => 'nullable'
        ];
    }
}
