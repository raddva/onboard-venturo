<?php

namespace App\Http\Controllers\Api;

use App\Exports\ReportSalesCategory;
use App\Exports\ReportSalescustomer;
use App\Helpers\Report\SalesCategoryHelper;
use App\Helpers\Report\SalesCustomerHelper;
use App\Helpers\Report\SalesPromoHelper;
use App\Helpers\Report\SalesTransactionHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Report\SalesPromoCollection;
use App\Http\Resources\Report\SalesTransactionCollection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportSalesController extends Controller
{
    private $salesPromo, $salesTransaction, $salesCategory, $salesCustomer;
    public function __construct()
    {
        $this->salesPromo = new SalesPromoHelper();
        $this->salesTransaction = new SalesTransactionHelper();
        $this->salesCategory = new SalesCategoryHelper();
        $this->salesCustomer = new SalesCustomerHelper();
    }

    public function viewSalesPromo(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        $customerId = isset($request->customer_id) ? explode(',', $request->customer_id) : [];
        $promoId = isset($request->promo_id) ? explode(',', $request->promo_id) : [];

        $sales = $this->salesPromo->get($startDate, $endDate, $customerId, $promoId);

        return response()->success(new SalesPromoCollection($sales['data']));
    }

    public function viewSalesTransaction(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        $customerId = isset($request->customer_id) ? explode(',', $request->customer_id) : [];
        $productId = isset($request->product_id) ? explode(',', $request->product_id) : [];

        $sales = $this->salesTransaction->get($startDate, $endDate, $request->per_page ?? 10, $request->sort ?? '', $customerId, $productId);

        return response()->success(new SalesTransactionCollection($sales['data']));
    }

    public function viewSalesCategories(Request $request)
    {
        $startDate     = $request->start_date ?? null;
        $endDate       = $request->end_date ?? null;
        $categoryId    = $request->category_id ?? null;
        $isExportExcel = $request->is_export_excel ?? null;

        $sales = $this->salesCategory->get($startDate, $endDate, $categoryId);

        if ($isExportExcel) {
            // dd($sales);
            return Excel::download(new ReportSalesCategory($sales), 'report-sales-category.xls');
        }

        return response()->success($sales['data'], '', [
            'dates'          => $sales['dates'] ?? [],
            'total_per_date' => $sales['total_per_date'] ?? [],
            'grand_total'    => $sales['grand_total'] ?? 0
        ]);
    }

    public function viewSalesCustomers(Request $request)
    {
        $startDate     = $request->start_date ?? null;
        $endDate       = $request->end_date ?? null;
        $customerId = isset($request->customer_id) ? explode(',', $request->customer_id) : [];
        $isExportExcel = $request->is_export_excel ?? null;

        $sales = $this->salesCustomer->get($startDate, $endDate, $customerId);

        if ($isExportExcel) {
            return Excel::download(new ReportSalesCustomer($sales), 'report-sales-customer.xls');
        }

        return response()->success($sales['data'], '', [
            'dates'          => $sales['dates'] ?? [],
            'total_per_date' => $sales['total_per_date'] ?? [],
            'grand_total'    => $sales['grand_total'] ?? 0
        ]);
    }

    public function viewCustomerSales($id)
    {
        $customerId    = $id ?? null;

        $sales = $this->salesCustomer->getCustomerSales($customerId);

        return response()->success($sales['data'], '', [
            'dates'          => $sales['dates'] ?? [],
            'total_per_date' => $sales['total_per_date'] ?? [],
            'grand_total'    => $sales['grand_total'] ?? 0
        ]);
    }
}
