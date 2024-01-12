<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleDetailResource extends JsonResource
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
            'product_id' => $this->m_product_id,
            'product_detail_id' => $this->m_product_detail_id,
            'total_item' => $this->total_item,
            'price' => $this->price,
            'discount_nominal' => $this->discount_nominal,
        ];
    }
}
