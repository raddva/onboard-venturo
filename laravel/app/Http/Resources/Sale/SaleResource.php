<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'customer_id' => $this->m_customer_id,
            'voucher_id' => $this->m_voucher_id,
            'voucher_nominal' => $this->voucher_nominal,
            'discount_id' => $this->m_discount_id,
            'date' => $this->date,
            'detail' => SaleDetailResource::collection($this->detail),
        ];
    }
}
