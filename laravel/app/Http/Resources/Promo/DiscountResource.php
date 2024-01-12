<?php

namespace App\Http\Resources\Promo;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
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
            'id' => $this->id ?? null,
            'customer_id' => $this->customer->id ?? null,
            'customer_name' => $this->customer->name ?? null,
            'promo_id' => $this->promo->id ?? null,
            'promo_name' => $this->promo->name ?? null,
            'status' => $this->status,
        ];
    }
}
