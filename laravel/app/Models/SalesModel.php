<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = [
        'm_customer_id',
        'm_voucher_id',
        'voucher_nominal',
        'm_discount_id',
        'date',
    ];
    protected $table = 't_sales';

    public function customer()
    {
        return $this->hasOne(CustomerModel::class, 'id', 'm_customer_id');
    }

    public function detail()
    {
        return $this->hasMany(SalesDetailModel::class, 't_sales_id', 'id');
    }

    public function discount()
    {
        return $this->hasOne(DiscountModel::class, 'id', 'm_discount_id');
    }

    public function voucher()
    {
        return $this->hasOne(VoucherModel::class, 'id', 'm_voucher_id');
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function getAll()
    {
        $sales = $this->query();

        return $sales->orderByDesc('id')->get();
    }

    public function getSalesPromo($startDate, $endDate, $customer = [], $promo = [])
    {
        $sales = $this->query()
            ->with(['voucher', 'discount', 'customer', 'voucher.promo', 'discount.promo']);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }
        if (!empty($customer)) {
            $sales->whereIn('m_customer_id', $customer);
        }

        $results = $sales->orderByDesc('date')->get();
        if (!empty($promo)) {
            $filteredResults = $results->filter(function ($result) use ($promo) {
                $voucherPromoId = optional(optional($result->voucher)->promo)->id;
                $discountPromoId = optional(optional($result->discount)->promo)->id;
                return in_array($voucherPromoId, $promo) || in_array($discountPromoId, $promo);
            });
            return $filteredResults->values();
        }
        return $results->values();
    }


    public function getSalesTransaction($startDate, $endDate, int $itemPerPage = 0, string $sort = '', $customer = [], $product = [])
    {
        $sales = $this->query()->with(['customer', 'detail.product']);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }
        if (!empty($customer)) {
            $sales->whereIn('m_customer_id', $customer);
        }
        if (!empty($product)) {
            $sales->whereHas('detail', function ($query) use ($product) {
                $query->whereIn('m_product_id', $product);
            })->with(['detail' => function ($query) use ($product) {
                $query->whereIn('m_product_id', $product);
            }]);
        }

        $sort = $sort ?: 'id DESC';
        $sales->orderByRaw($sort);
        $sales->whereNotNull('m_voucher_id');
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $sales->paginate($itemPerPage)->appends('sort', $sort);
    }


    public function getSalesByCategory($startDate, $endDate, $category = '')
    {
        $sales = $this->query()->with([
            'detail.product' => function ($query) use ($category) {
                if (!empty($category)) {
                    $query->where('m_product_category_id', $category);
                }
            },
            'detail',
            'detail.product.category'
        ]);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }

        return $sales->orderByDesc('date')->get();
    }

    public function getSalesByCustomer($startDate, $endDate, $customer = [])
    {
        $sales = $this->query()->with([
            'detail.product',
            'detail',
            'customer'
        ]);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }

        if (!empty($customer)) {
            $sales->whereIn('m_customer_id', $customer);
        }

        return $sales->orderByDesc('date')->get();
    }

    public function getCustomerSales(string $id)
    {
        $sales = $this->query()->with([
            'detail',
            'detail.product',
            'customer'
        ]);

        $sales->where('m_customer_id', $id);

        return $sales->orderByDesc('date')->get();
    }
}
