<?php

namespace App\Helpers\Report;

use App\Helpers\Venturo;
use App\Models\SalesModel;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;

class SalesCustomerHelper extends Venturo
{
    private $dates;
    private $endDate;
    private $sales;
    private $startDate;
    private $total;
    private $totalPerDate;

    public function __construct()
    {
        $this->sales = new SalesModel();
    }

    public function get($startDate, $endDate, $customerId = [])
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;

        $sales = $this->sales->getSalesByCustomer($startDate, $endDate, $customerId);

        return [
            'status'     => true,
            'data'       => $this->reformatReport($sales, $startDate, $endDate),
            'dates'          => array_values($this->dates),
            'total_per_date' => array_values($this->totalPerDate),
            'grand_total'    => $this->total
        ];
    }

    public function getCustomerSales($customerId = '')
    {

        $sales = $this->sales->getCustomerSales($customerId);

        return [
            'status'     => true,
            'data'       => $this->reformatCustomerSales($sales),
            'dates'          => array_values($this->dates),
            'total_per_date' => array_values($this->totalPerDate),
            'grand_total'    => $this->total
        ];
    }

    private function reformatCustomerSales($list)
    {
        $list = $list->toArray();
        $periods = $this->getPeriode();
        $salesDetail = [];

        foreach ($list as $sales) {
            $customerId = $sales['customer']['id'];
            $customerName = $sales['customer']['name'];
            $totalPerCustomer = 0;

            foreach ($sales['detail'] as $detail) {
                if (empty($detail['product'])) {
                    continue;
                }

                $date = date('Y-m-d', strtotime($sales['date']));
                $productId = $detail['product']['id'];
                $salesId = $detail['t_sales_id'];
                $nominalDiscount = $detail['discount_nominal'];
                $totalItem = $detail['total_item'];
                $total = $detail['price'];
                $totalSales = $total * $totalItem;
                $totalBayar = $totalSales - $nominalDiscount;
                $totalPerCustomer += $totalBayar;

                if (!isset($salesDetail[$customerId])) {
                    $salesDetail[$customerId] = [
                        'customer_id' => $customerId,
                        'customer_name' => $customerName,
                        'customer_total' => 0,
                        'transactions' => $periods,
                    ];
                }

                $salesDetail[$customerId]['customer_total'] += $totalBayar;
                if (!isset($salesDetail[$customerId]['transactions'][$date])) {
                    $salesDetail[$customerId]['transactions'][$date] = [
                        'date_transaction' => $date,
                        'total_sales' => 0,
                        'products' => [],
                    ];
                }
                $salesDetail[$customerId]['transactions'][$date]['no_struk'] = $this->noStruk($salesId, $date);
                $salesDetail[$customerId]['transactions'][$date]['total_sales'] += $totalBayar;
                $salesDetail[$customerId]['transactions'][$date]['products'][] = [
                    'product_id' => $productId,
                    'total' => $total,
                    'discount_nominal' => $nominalDiscount,
                    'total_item' => $totalItem,
                    'total_bayar' => $totalBayar,
                ];
                $this->totalPerDate[$date] = ($this->totalPerDate[$date] ?? 0) + $totalBayar;
                $this->total = ($this->total ?? 0) + $totalBayar;
            }
        }
        return $this->convertNumericKey($salesDetail);
    }

    private function noStruk($id, $date)
    {
        $idPrefix = str_pad($id, 3, '0', STR_PAD_LEFT);
        $regionCode = 'KWT';
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');

        return sprintf('%s/%s/%s/%s', $idPrefix, $regionCode, $month, $year);
    }

    private function reformatReport($list)
    {
        $list        = $list->toArray();
        $periods     = $this->getPeriode();
        $salesDetail = [];

        foreach ($list as $sales) {
            foreach ($sales['detail'] as $detail) {
                if (empty($detail['product'])) {
                    continue;
                }

                $date                   = date('Y-m-d', strtotime($sales['date']));
                $customerId             = $sales['customer']['id'];
                $customerName           = $sales['customer']['name'];
                $totalSales             = $detail['price'] * $detail['total_item'];
                $discountNominal        = $detail['discount_nominal'] ?? 0;
                $listTransactions       = $salesDetail[$customerId]['transactions'] ?? $periods;
                $subTotal               = $salesDetail[$customerId]['transactions'][$date]['total_sales'] ?? 0;
                $totalPerCustomer       = $salesDetail[$customerId]['customer_total'] ?? 0;
                $totalSalesAfterDiscount = $totalSales - $discountNominal;

                $salesDetail[$customerId] = [
                    'customer_id'    => $customerId,
                    'customer_name'  => $customerName,
                    'customer_total' => $totalPerCustomer + $totalSalesAfterDiscount,
                    'transactions'   => $listTransactions,
                ];

                $salesDetail[$customerId]['transactions'][$date] = [
                    'date_transaction' => $date,
                    'total_sales'      => $totalSalesAfterDiscount + $subTotal,
                ];

                $this->totalPerDate[$date] = ($this->totalPerDate[$date] ?? 0) + $totalSalesAfterDiscount;
                $this->total               = ($this->total ?? 0) + $totalSalesAfterDiscount;
            }
        }
        return $this->convertNumericKey($salesDetail);
    }




    private function convertNumericKey($salesDetail)
    {
        $list = [];

        foreach ($salesDetail as $customerId => $sales) {
            $list[] = [
                'customer_id'    => $customerId,
                'customer_name'  => $sales['customer_name'],
                'customer_total' => $sales['customer_total'],
                'transactions'   => array_values($sales['transactions']),
            ];
        }

        return $list;
    }

    private function getPeriode()
    {
        $begin = new DateTime($this->startDate);
        $end   = new DateTime($this->endDate);
        $end   = $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period   = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $date         = $dt->format('Y-m-d');
            $dates[$date] = [
                'date_transaction' => $date,
                'total_sales'      => 0,
            ];

            $this->setDefaultTotal($date);
            $this->setSelectedDate($date);
        }

        return $dates ?? [];
    }

    private function setDefaultTotal(string $date)
    {
        $this->totalPerDate[$date] = 0;
    }

    private function setSelectedDate(string $date)
    {
        $this->dates[] = $date;
    }
}
