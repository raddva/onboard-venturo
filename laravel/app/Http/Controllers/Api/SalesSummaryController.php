<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Report\TotalSalesHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesSummaryController extends Controller
{
    private $sales;

    public function __construct()
    {
        $this->sales    = new TotalSalesHelper();
    }

    public function getDiagramPerYear(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (empty($startDate) && empty($endDate)) {
            $sales = $this->sales->getTotalPerYear();
        }

        $sales = $this->sales->getTotalPerYear($startDate, $endDate);
        return response()->json($sales);
    }

    public function getTotalSummary()
    {
        $sales = $this->sales->getTotalInPeriode();

        return response()->success($sales['data']);
    }

    public function getDiagramPerMonth(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (empty($startDate) && empty($endDate)) {
            $currentYear = date('Y');
            $startDate = $currentYear . '-01-01';
            $endDate = $currentYear . '-12-31';
        }

        $sales = $this->sales->getTotalPerMonth($startDate, $endDate);
        return response()->json($sales);
    }
}
