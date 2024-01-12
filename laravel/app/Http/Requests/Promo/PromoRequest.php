<?php

namespace App\Http\Requests\Promo;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class PromoRequest extends FormRequest
{
    use ConvertsBase64ToFiles;
    public $validator;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }
    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-promo.jpg',
        ];
    }

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
            'status' => 'required|in:diskon,voucher',
            'expired_in_day' => 'required|numeric',
            'nominal_percentage' => 'nullable|required_if:status,diskon',
            'nominal_rupiah' => 'nullable|required_if:status,voucher',
            'term_conditions' => 'required',
            'photo' => 'nullable|file|image',
        ];
    }

    private function updateRules(): array
    {
        return [
            'id' => 'required',
            'name' => 'required|max:150',
            'status' => 'required|in:diskon,voucher',
            'expired_in_day' => 'required|numeric',
            'nominal_percentage' => 'nullable|required_if:status,diskon',
            'nominal_rupiah' => 'nullable|required_if:status,voucher',
            'term_conditions' => 'required',
            'photo' => 'nullable|file|image',
        ];
    }

    public function attributes()
    {
        return [
            'expired_in_day' => 'Waktu Kadaluarsa'
        ];
    }
}
