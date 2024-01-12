<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerModel extends Model implements CrudInterface
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    use Uuid;

    /**
     * Akan mengisi kolom "created_at" dan "updated_at" secara otomatis,
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Menentukan kolom apa saja yang bisa dimanipulasi oleh UserModel
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'photo',
        'date_of_birth',
        'is_verified'
    ];

    protected $casts = [
        'id' => 'string',
    ];

    /**
     * Menentukan nama tabel yang terhubung dengan Class ini
     *
     * @var string
     */
    protected $table = 'm_customer';

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $customer = $this->query();

        if (!empty($filter['name'])) {
            $customer->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }
        if (!empty($filter['id'])) {
            $customer->where('id', 'LIKE', '%' . $filter['id'] . '%');
        }
        if ($filter['is_verified'] != null) {
            $customer->where('is_verified', '=', $filter['is_verified']);
        }

        $sort = $sort ?: 'id DESC';
        $customer->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $customer->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }
}
