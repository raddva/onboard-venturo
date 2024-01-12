<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleModel extends Model implements CrudInterface
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
     * Menentukan kolom apa saja yang bisa dimanipulasi oleh RoleModel
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'access',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    /**
     * Menentukan nama tabel yang terhubung dengan Class ini
     *
     * @var string
     */
    protected $table = 'user_role';

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
        $role = $this->query();

        if (!empty($filter['name'])) {
            $role->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        $sort = $sort ?: 'id DESC';
        $role->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $role->paginate($itemPerPage)->appends('sort', $sort);
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
