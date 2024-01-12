<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Promo\PromoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Promo\PromoRequest;
use App\Http\Resources\Promo\PromoCollection;
use App\Http\Resources\Promo\PromoResource;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    private $promo;
    public function __construct()
    {
        $this->promo = new PromoHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'status' => $request->status ?? '',
        ];
        $promos = $this->promo->getAll($filter, $request->per_page ?? 5, $request->sort ?? '');

        return response()->success(new PromoCollection($promos['data']));
    }

    public function show($id)
    {
        $promo = $this->promo->getById($id);

        if (!($promo['status'])) {
            return response()->failed(['Data promo tidak ditemukan'], 404);
        }

        return response()->success(new PromoResource($promo['data']));
    }

    public function store(PromoRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'name',
            'status',
            'expired_in_day',
            'nominal_percentage',
            'nominal_rupiah',
            'term_conditions',
            'photo',
        ]);

        $promo = $this->promo->create($payload);

        if (!$promo['status']) {
            return response()->failed($promo['error']);
        }

        return response()->success(new PromoResource($promo['data']), 'promo berhasil ditambahkan');
    }

    public function update(PromoRequest $request)
    {
        if ($request->input('status') == 'diskon') {
            $request->merge(['nominal_rupiah' => null]);
        } else {
            $request->merge(['nominal_percentage' => null]);
        }

        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'id',
            'name',
            'status',
            'expired_in_day',
            'nominal_percentage',
            'nominal_rupiah',
            'term_conditions',
            'photo',
        ]);

        $promo = $this->promo->update($payload, $payload['id'] ?? 0);

        if (!$promo['status']) {
            return response()->failed($promo['error']);
        }

        return response()->success(new PromoResource($promo['data']), 'promo berhasil diubah');
    }

    public function destroy($id)
    {
        $promo = $this->promo->delete($id);

        if (!$promo) {
            return response()->failed(['Mohon maaf promo tidak ditemukan']);
        }

        return response()->success($promo, 'promo berhasil dihapus');
    }
}
