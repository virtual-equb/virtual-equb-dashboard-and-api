<?php

namespace App\Http\Requests\MainEqub;

use Illuminate\Foundation\Http\FormRequest;

class StoreEqubRequest extends FormRequest
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

    // validate a route param to execute an action
    protected function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id
        ]);
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
            'image' => 'nullable',
            'remark' => 'nullable',
            'status' => 'nullable',
            'active' => 'nullable'
        ];
    }
}
