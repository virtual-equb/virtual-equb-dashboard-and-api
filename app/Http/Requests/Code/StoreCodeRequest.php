<?php

namespace App\Http\Requests\Code;

use Illuminate\Foundation\Http\FormRequest;

class StoreCodeRequest extends FormRequest
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

    // protected function prepareForValidation()
    // {
    //     $this->merge([
    //         'created_by' => $this->user()->id
    //     ]);
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'created_by' => 'required',
            'icone' => 'nullable',
            'active' => 'nullable'
        ];
    }
}
