<?php

namespace App\Helpers\Customer;

use App\Helpers\Venturo;
use App\Models\CustomerModel;
use Throwable;

/**
 * Helper untuk manajemen user
 * Mengambil data, menambah, mengubah, & menghapus ke tabel user_auth
 *
 * @author Wahyu Agung <wahyuagung26@gmail.com>
 */
class CustomerHelper extends Venturo
{
    const CUSTOMER_PHOTO_DIRECTORY = 'foto-customer';
    private $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    /**
     * method untuk menginput data baru ke tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *                       $payload['name'] = string
     *                       $payload['email] = string
     *                       $payload['password] = string
     *
     * @return array
     */
    public function create(array $payload): array
    {
        try {
            $payload = $this->uploadGetPayload($payload);
            $user = $this->customerModel->store($payload);

            return [
                'status' => true,
                'data' => $user
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Menghapus data user dengan sistem "Soft Delete"
     * yaitu mengisi kolom deleted_at agar data tsb tidak
     * keselect waktu menggunakan Query
     *
     * @param integer $id id dari tabel user_auth
     *
     * @return bool
     */
    public function delete(string $id): bool
    {
        try {
            $this->customerModel->drop($id);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Mengambil data user dari tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@gmail.com>
     *
     * @param array $filter
     *                      $filter['name'] = string
     *                      $filter['email'] = string
     * @param integer $itemPerPage jumlah data yang ditampilkan, kosongi jika ingin menampilkan semua data
     * @param string $sort nama kolom untuk melakukan sorting mysql beserta tipenya DESC / ASC
     *
     * @return array
     */
    public function getAll(array $filter, int $itemPerPage = 0, string $sort = ''): array
    {
        $users = $this->customerModel->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $users
        ];
    }

    /**
     * Mengambil 1 data user dari tabel user_auth
     *
     * @param integer $id id dari tabel user_auth
     *
     * @return array
     */
    public function getById(string $id): array
    {
        $user = $this->customerModel->getById($id);
        if (empty($user)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $user
        ];
    }

    /**
     * method untuk mengubah user pada tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *                       $payload['name'] = string
     *                       $payload['email] = string
     *                       $payload['password] = string
     *
     * @return array
     */
    public function update(array $payload, string $id): array
    {
        try {
            $payload = $this->uploadGetPayload($payload);
            $this->customerModel->edit($payload, $id);
            $user = $this->getById($id);

            return [
                'status' => true,
                'data' => $user['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Upload file and remove payload when photo is not exist
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     * @return array
     */
    private function uploadGetPayload(array $payload)
    {
        /**
         * Jika dalam payload terdapat base64 foto, maka Upload foto ke folder public/uploads/foto-user
         */
        if (!empty($payload['photo'])) {
            $fileName = $this->generateFileName($payload['photo'], 'CUSTOMER_' . date('Ymdhis'));
            $photo = $payload['photo']->storeAs(self::CUSTOMER_PHOTO_DIRECTORY, $fileName, 'public');
            $payload['photo'] = $photo;
        } else {
            unset($payload['photo']);
        }

        return $payload;
    }
}
