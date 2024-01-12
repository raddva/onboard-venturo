<?php

namespace App\Http\Resources\Customer;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth,
            'phone_number' => $this->phone_number,
            // 'photo' => $this->photo,
            'photo_url' => !empty($this->photo) ? Storage::disk('public')->url($this->photo) : Storage::disk('public')->url('img/no-image.png'),
            'is_verified' => (string) $this->is_verified,
        ];
    }
}
