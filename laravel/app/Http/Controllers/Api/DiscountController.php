<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Promo\DiscountHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Promo\DiscountRequest;
use App\Http\Resources\Promo\DiscountCollection;
use App\Http\Resources\Promo\DiscountResource;
use App\Models\DiscountModel;
use Illuminate\Http\Request;

class DiscountController extends Controller
{

    private $discount;
    public function __construct()
    {
        $this->discount = new DiscountHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'customer_id' => isset($request->customer_id) ? explode(',', $request->customer_id) : [],
        ];
        $discounts = $this->discount->getAll($filter, $request->per_page ?? 50, $request->sort ?? '');

        return response()->success(new DiscountCollection($discounts['data']));
    }

    public function store(DiscountRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['customer_id', 'promo_id', 'status']);
        $payload = $this->renamePayload($payload);
        $discount = $this->discount->create($payload);

        if (!$discount['status']) {
            return response()->failed($discount['error']);
        }

        return response()->success(new DiscountResource($discount['data']), 'Diskon berhasil ditambahkan');
    }

    public function update(DiscountRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'customer_id', 'promo_id', 'status']);
        $payload = $this->renamePayload($payload);
        $discount = $this->discount->update($payload, $payload['id'] ?? 0);

        if (!$discount['status']) {
            return response()->failed($discount['error']);
        }

        return response()->success(new DiscountResource($discount['data']), 'Diskon berhasil diubah');
    }

    public function renamePayload($payload)
    {
        $payload['m_customer_id'] = $payload['customer_id'] ?? null;
        $payload['m_promo_id'] = $payload['promo_id'] ?? null;
        unset($payload['customer_id']);
        unset($payload['promo_id']);
        return $payload;
    }

    public function getByCust(string $customerId)
    {
        $discountModel = new DiscountModel();
        $discounts = $discountModel->getByCustomerId($customerId);

        return response()->json($discounts);
    }
}
