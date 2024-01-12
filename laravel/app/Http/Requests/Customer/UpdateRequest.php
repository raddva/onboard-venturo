<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class UpdateRequest extends FormRequest
{
    use ConvertsBase64ToFiles; // Library untuk convert base64 menjadi File

    public $validator = null;

    /**
     * Tampilkan pesan error ketika validasi gagal
     *
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-user.jpg',
        ];
    }
    public function rules()
    {
        return [
            'id' => 'required',
            'name' => 'required|max:100',
            'email' => 'email',
            'phone_number' => 'numeric',
            'date_of_birth' => 'required|date',
            'photo' => 'nullable|file|image', // Validasi untuk upload file image saja, jika tidak ada perubahan foto user, isi key foto dengan NULL
        ];
    }
}
