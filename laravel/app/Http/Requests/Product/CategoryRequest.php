<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public $validator;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    private function createRules(): array
    {
        return [
            'name' => 'required|max:150',
        ];
    }

    private function updateRules(): array
    {
        return [
            'name' => 'required|max:150',
            'id' => 'required'
        ];
    }
}
