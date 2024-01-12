<?php

namespace App\Http\Requests\Promo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class VoucherRequest extends FormRequest
{
    public $validator;

    /**
     * Tampilkan pesan error ketika validasi gagal
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get the validation rules that apply to the request.
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @return array
     */
    public function rules()
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    /**
     * Create rule for insert / Request Method = POST
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @return array
     */
    private function createRules(): array
    {
        return [
            'customer_id' => 'required',
            'promo_id' => 'required|numeric',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_voucher' => 'required|numeric',
            'nominal_rupiah' => 'required|numeric',
        ];
    }

    /**
     * Create rule for update / Request Method = PUT
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @return array
     */
    private function updateRules(): array
    {
        return [
            'id' => 'required|numeric',
            'customer_id' => 'required',
            'promo_id' => 'required|numeric',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_voucher' => 'required|numeric',
            'nominal_rupiah' => 'required|numeric',
        ];
    }

    /**
     * Setting custom attribute pesan error yang ditampilkan
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'customer_id' => 'Customer',
            'promo_id' => 'Voucher',
            'nominal_rupiah' => 'Nominal',
        ];
    }
}
