<?php

namespace App\Http\Requests\Sale;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{

    public $validator;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function rules()
    {
        return [
            'customer_id' => 'required',
            'voucher_id' => 'nullable|numeric',
            'voucher_nominal' => 'nullable',
            'discount_id' => 'nullable|numeric',
            'date' => 'required|date',
            'detail.*.product_id' => 'nullable',
            'detail.*.product_detail_id' => 'nullable',
            'detail.*.total_item' => 'required',
            'detail.*.price' => 'required',
            'detail.*.discount_nominal' => 'nullable',
        ];
    }
}
