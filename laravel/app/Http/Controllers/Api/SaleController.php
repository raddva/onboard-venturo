<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Sale\SaleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\SaleRequest;
use App\Http\Resources\Sale\SaleCollection;
use App\Http\Resources\Sale\SaleResource;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    private $sale;
    public function __construct()
    {
        $this->sale = new SaleHelper();
    }

    public function index()
    {
        $sales = $this->sale->getAll();

        return response()->success(new SaleCollection($sales['data']));
    }

    public function store(SaleRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'customer_id',
            'voucher_id',
            'voucher_nominal',
            'discount_id',
            'date',
            'detail',
        ]);

        $payload = $this->renamePayload($payload);
        $details = $payload['detail'] ?? [];
        unset($payload['detail']);

        $sales = $this->sale->create($payload);
        if (!$sales['status']) {
            return response()->failed($sales['error']);
        }
        foreach ($details as $detail) {
            $detail['m_product_id'] = $detail['product_id'] ?? null;
            $detail['m_product_detail_id'] = $detail['product_detail_id'] ?? null;
            unset($detail['product_id'], $detail['product_detail_id']);
            $sales['data']->detail()->create($detail);
        }

        return response()->success(new SaleResource($sales['data']), 'Transaksi berhasil ditambahkan');
    }


    public function renamePayload($payload)
    {
        $payload['m_customer_id'] = $payload['customer_id'] ?? null;
        $payload['m_voucher_id'] = $payload['voucher_id'] ?? null;
        $payload['m_discount_id'] = $payload['discount_id'] ?? null;
        unset($payload['customer_id']);
        unset($payload['voucher_id']);
        unset($payload['discount_id']);
        return $payload;
    }
}
