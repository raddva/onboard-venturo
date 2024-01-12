<?php

namespace App\Helpers\Report;

use App\Helpers\Venturo;
use App\Models\SalesDetailModel;
use DateTime;
use Throwable;

class TotalSalesHelper extends Venturo
{
    private $sales;

    public function __construct()
    {
        $this->sales = new SalesDetailModel();
    }

    private function getTotalToday()
    {
        return $this->sales->getTotalSaleByPeriode((string) date('Y-m-d'), (string) date('Y-m-d'));
    }


    private function getTotalYesterday()
    {
        $date = new DateTime();
        $date->modify('-1 day');

        return $this->sales->getTotalSaleByPeriode(
            (string) $date->format('Y-m-d'),
            (string) $date->format('Y-m-d')
        );
    }

    private function getTotalThisMonth()
    {
        $startDate = new DateTime();
        $start     = $startDate->modify('first day of this month')
            ->format('Y-m-d');

        $endDate   = new DateTime();
        $end       = $endDate->modify('last day of this month')
            ->format('Y-m-d');

        return $this->sales->getTotalSaleByPeriode((string) $start, (string) $end);
    }

    private function getTotalLastMonth()
    {
        $startDate = new DateTime();
        $start     = $startDate->modify('first day of last month')
            ->format('Y-m-d');

        $endDate   = new DateTime();
        $end       = $endDate->modify('last day of last month')
            ->format('Y-m-d');

        return $this->sales->getTotalSaleByPeriode((string) $start, (string) $end);
    }

    public function getTotalInPeriode()
    {
        return [
            'status'  => true,
            'data'    => [
                'today'      => $this->getTotalToday(),
                'yesterday'  => $this->getTotalYesterday(),
                'this_month' => $this->getTotalThisMonth(),
                'last_month' => $this->getTotalLastMonth(),
            ]
        ];
    }

    public function getTotalPerYear($startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            $start = (new DateTime($startDate))->format('Y-m-d');
            $end = (new DateTime($endDate))->format('Y-m-d');
        } else {
            $start = null;
            $end = null;
        }

        $years = $this->sales->getListYear();
        sort($years);

        $diagram = [];
        foreach ($years as $year) {
            $total = ($start && $end)
                ? $this->sales->getTotalPerYears($year, $start, $end)
                : $this->sales->getTotalPerYears($year);

            $diagram['label'][] = (string) $year;
            $diagram['data'][] = $total;
        }

        return [
            'status' => true,
            'data'   => $diagram ?? []
        ];
    }


    public function getTotalPerMonth($startDate, $endDate)
    {
        try {
            $start = (new DateTime($startDate))->format('Y-m-d');
            $end = (new DateTime($endDate))->format('Y-m-d');

            $months = $this->sales->getListMonth();
            sort($months);

            $diagram = [];
            foreach ($months as $month) {
                $total = $this->sales->getTotalPerMonths($month, $start, $end);
                $diagram['label'][] = (string) $month;
                $diagram['data'][] = $total;
            }

            return [
                'status' => true,
                'data' => $diagram ?? []
            ];
        } catch (Throwable $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
