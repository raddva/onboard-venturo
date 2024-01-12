<?php

namespace App\Helpers\Report;

use App\Helpers\Venturo;
use App\Models\SalesModel;
use Throwable;

class SalesTransactionHelper extends Venturo
{
    private $sales;

    public function __construct()
    {
        $this->sales = new SalesModel();
    }

    public function get($startDate, $endDate,  int $itemPerPage = 0, string $sort = '', $customerId = [], $productId = [])
    {
        $sales = $this->sales->getSalesTransaction($startDate, $endDate, $itemPerPage, $sort,  $customerId, $productId);

        return [
            'status' => true,
            'data'   => $sales
        ];
    }
}
