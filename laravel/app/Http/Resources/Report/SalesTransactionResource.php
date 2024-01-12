<?php

namespace App\Http\Resources\Report;

use App\Models\DiscountModel;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $productDetails = $this->detail->map(function ($detail) {
            $totalItem = $detail->total_item ?? 0;
            $price = $detail->product->price ?? 0;
            $discountNominal = $detail->discount_nominal ?? 0;
            $totalAmountWithDiscount = max(0, ($totalItem * $price) - $discountNominal);

            $discount = DiscountModel::find($this->m_discount_id);
            $promo = $discount ? $discount->promo : null;
            $noStruk = $this->noStruk($this->id);

            return [
                'id' => $this->id ?? null,
                'no_struk' => $noStruk,
                'date_transaction' => $this->date ? Carbon::parse($this->date)->isoFormat('D MMMM YYYY') : null,
                'customer_name' => $this->customer->name ?? null,
                'discount' => $promo ? $promo->nominal_percentage : null,
                'voucher' => $this->voucher->nominal_rupiah ?? null,
                'product' => [
                    'name' => $detail->product->name ?? null,
                    'price' => $price,
                    'total_item' => $totalItem,
                    'disc_nominal' => $discountNominal,
                    'total_amount' => $totalItem * $price,
                    'total_with_discount' => $totalAmountWithDiscount,
                ],
            ];
        })->toArray();

        return $productDetails;
    }

    private function noStruk($id)
    {
        $idPrefix = str_pad($id, 3, '0', STR_PAD_LEFT);
        $regionCode = 'KWT';
        $month = Carbon::parse($this->date)->format('m');
        $year = Carbon::parse($this->date)->format('Y');

        return sprintf('%s/%s/%s/%s', $idPrefix, $regionCode, $month, $year);
    }
}
