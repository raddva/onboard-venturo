<?php

namespace App\Http\Requests\Customer;

use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class CreateRequest extends FormRequest
{
    use ConvertsBase64ToFiles; // Library untuk convert base64 menjadi File

    public $validator;

    /**
     * Setting custom attribute pesan error yang ditampilkan
     *
     * @return array
     */

    /**
     * Tampilkan pesan error ketika validasi gagal
     *
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:100',
            'email' => 'email|unique:m_customer',
            'phone_number' => 'numeric',
            'date_of_birth' => 'required|date',
            'photo' => 'nullable|file|image',
        ];
    }

    /**
     * inisialisasi key "photo" dengan value base64 sebagai "FILE"
     *
     * @return array
     */
    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-customer.jpg',
        ];
    }

    public function store(CreateRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name', 'date_of_birth', 'email', 'photo', 'phone_number', 'is_verified']);
        $customer = $this->customer->create($payload);

        if (!$customer['status']) {
            return response()->failed($customer['error']);
        }

        return response()->success(
            new CustomerResource(
                $customer['data']
            ),
            'Data customer berhasil disimpan'
        );
    }
}
