<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportSalesCustomer implements FromView
{
    private $reports;

    public function __construct(array $customer)
    {
        $this->reports = $customer;
    }

    public function view(): View
    {
        return view('generate.excel.report-customer', $this->reports);
    }
}
