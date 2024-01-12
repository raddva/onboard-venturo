<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = [
        'name',
        'm_customer_id',
        'm_promo_id',
        'status'
    ];
    protected $table = 'm_discount';


    public function customer()
    {
        return $this->hasOne(CustomerModel::class, 'id', 'm_customer_id');
    }

    public function promo()
    {
        return $this->hasOne(PromoModel::class, 'id', 'm_promo_id');
    }

    public function drop(int $id)
    {
        return $this->find($id)->delete();
    }

    public function edit(array $payload, int $id)
    {
        return $this->find($id)->update($payload);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $discount = $this->query();

        if ($filter['customer_id'] != null) {
            $discount->where('m_customer_id',  $filter['customer_id']);
        }

        $sort = $sort ?: 'id DESC';
        $discount->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $discount->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function getById(int $id)
    {
        return $this->find($id);
    }

    public function getByCustomerId(string $customerId)
    {
        return $this->with(['customer:id,name', 'promo:id,name,nominal_percentage'])
            ->where('m_customer_id', $customerId)
            ->where('status', 1)
            ->get();
    }
}
