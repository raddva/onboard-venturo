<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SalesDetailModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = [
        't_sales_id',
        'm_product_id',
        'm_product_detail_id',
        'total_item',
        'price',
        'discount_nominal',
    ];
    protected $table = 't_sales_detail';

    public function sales()
    {
        return $this->belongsTo(SalesModel::class, 't_sales_id', 'id');
    }

    public function product()
    {
        return $this->hasOne(ProductModel::class, 'id', 'm_product_id');
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $user = $this->query();

        if (!empty($filter['t_sales_id'])) {
            $user->where('t_sales_id', 'LIKE', '%' . $filter['t_sales_id'] . '%');
        }

        $sort = $sort ?: 't_sales.index ASC';
        $user->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $user->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function getTotalSaleByPeriode(string $startDate, string $endDate): int
    {
        $total = $this->query()
            ->select(DB::raw('sum((total_item * price) - discount_nominal) as total_sales'))
            ->whereHas('sales', function ($query) use ($startDate, $endDate) {
                $query->whereRaw('date >= "' . $startDate . ' " 
                and date <= "' . $endDate . ' "');
            })
            ->first()
            ->toArray();

        return $total['total_sales'] ?? 0;
    }

    public function getListYear()
    {
        $sales   = new SalesModel();
        $years   = $sales->query()
            ->select(DB::raw('Distinct(year(date)) as year'))
            ->get()
            ->toArray();

        return array_map(function ($year) {
            return $year['year'];
        }, $years);
    }

    public function getTotalPerYears($year, $startDate = null, $endDate = null)
    {
        return $this->query()
            ->select(DB::raw('sum((total_item * price) - discount_nominal) as total_sales'))
            ->whereHas('sales', function ($query) use ($year, $startDate, $endDate) {
                $query->where(DB::raw('year(date)'), '=', $year);

                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
            })
            ->first()
            ->toArray()['total_sales'] ?? 0;
    }


    public function getListMonth()
    {
        $sales   = new SalesModel();
        $months   = $sales->query()
            ->select(DB::raw('Distinct(month(date)) as month'))
            ->get()
            ->toArray();

        return array_map(function ($month) {
            return $month['month'];
        }, $months);
    }

    public function getTotalPerMonths($month, $startDate, $endDate)
    {
        return $this->query()
            ->select(DB::raw('sum((total_item * price) - discount_nominal) as total_sales'))
            ->whereHas('sales', function ($query) use ($month, $startDate, $endDate) {
                $query->where(DB::raw('month(date)'), '=', $month)
                    ->whereBetween('date', [$startDate, $endDate]);
            })
            ->first()
            ->toArray()['total_sales'] ?? 0;
    }
}
