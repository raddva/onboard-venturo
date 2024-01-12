<?php

namespace App\Helpers\Sale;

use App\Helpers\Venturo;
use App\Models\SalesDetailModel;
use App\Models\SalesModel;
use Throwable;

class SaleHelper extends Venturo
{
    private $sale, $saleDetail;

    public function __construct()
    {
        $this->sale = new SalesModel();
        $this->saleDetail = new SalesDetailModel();
    }

    public function getAll()
    {
        $categories = $this->sale->getAll();

        return [
            'status' => true,
            'data' => $categories
        ];
    }

    public function create(array $payload): array
    {
        try {
            $this->beginTransaction();
            $sale = $this->sale->store($payload);
            $this->insertDetail($payload['details'] ?? [], $sale->id);
            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $sale
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    private function insertDetail(array $details, int $saleId)
    {
        if (empty($details)) {
            return false;
        }

        foreach ($details as $val) {
            // Insert
            if (isset($val['is_added']) && $val['is_added']) {
                $val['m_product_id'] = $saleId;
                $this->saleDetail->store($val);
            }
        }
    }
}
