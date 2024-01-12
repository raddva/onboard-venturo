<?php

namespace App\Http\Controllers\Api;

use App\Helpers\User\RoleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\User\RoleCollection;
use App\Http\Resources\User\RoleResource;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $role;

    public function __construct()
    {
        $this->role = new RoleHelper();
    }

    /**
     * Delete data user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     * @param mixed $id
     */
    public function destroy($id)
    {
        $role = $this->role->delete($id);

        if (!$role) {
            return response()->failed(['Mohon maaf data role tidak ditemukan']);
        }

        return response()->success($role, "Role berhasil dihapus");
    }

    /**
     * Mengambil data user dilengkapi dengan pagination
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
        ];
        $roles = $this->role->getAll($filter, 5, $request->sort ?? '');

        return response()->success(new RoleCollection($roles['data']));
    }

    /**
     * Menampilkan user secara spesifik dari tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     * @param mixed $id
     */
    public function show($id)
    {
        $role = $this->role->getById($id);

        if (!($role['status'])) {
            return response()->failed(['Data role tidak ditemukan'], 404);
        }
        return response()->success(new RoleResource($role['data']));
    }

    /**
     * Membuat data user baru & disimpan ke tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function store(Request $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
         */
        $this->validate($request, [
            'name'     => 'required',
            'access'   => 'required'
        ]);

        $role = $this->role->create(
            [
                "name" => $request->name,
                "access" => $request->access
            ]
        );

        if (!$role['status']) {
            return response()->failed($role['error']);
        }

        return response()->success(new RoleResource($role['data']), "Role berhasil ditambahkan");
    }

    /**
     * Mengubah data user di tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function update(UpdateRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/UpdateRequest
         */
        $this->validate($request, [
            'name'     => 'required',
            'access'   => 'required'
        ]);

        $role = $this->role->update(
            [
                "name" => $request->name,
                "access" => $request->access
            ],
            $request->id
        );
        if (!$role['status']) {
            return response()->failed($role['error']);
        }

        return response()->success(new RoleResource($role['data']), "Role berhasil diubah");
    }
}
