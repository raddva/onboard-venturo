<?php

namespace App\Helpers\Promo;

use App\Helpers\Venturo;
use App\Models\VoucherModel;
use Throwable;

class VoucherHelper extends Venturo
{
    private $voucher;

    public function __construct()
    {
        $this->voucher = new VoucherModel();
    }

    /**
     * method untuk menginput data baru ke tabel m_voucher
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *                       $payload['name'] = string
     *                       $payload['m_customer_id'] = number
     *                       $payload['m_voucher_id'] = number
     *                       $payload['start_time'] = string date yyyy-mm-dd
     *                       $payload['end_time'] = string date yyyy-mm-dd
     *                       $payload['total_voucher'] = number
     *                       $payload['nominal_rupiah'] = number
     *                       $payload['description'] = number
     *                       $payload['photo'] = string
     *
     * @return array
     */
    public function create(array $payload): array
    {
        try {
            $voucher = $this->voucher->store($payload);

            return [
                'status' => true,
                'data' => $voucher
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Menghapus data voucher dengan sistem "Soft Delete"
     * yaitu mengisi kolom deleted_at agar data tsb tidak
     * keselect waktu menggunakan Query
     *
     * @param integer $id id dari tabel m_voucher
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $this->voucher->drop($id);

            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    /**
     * Mengambil data voucher dari tabel m_voucher
     *
     * @author Wahyu Agung <wahyuagung26@gmail.com>
     *
     * @param array $filter
     *                      $filter['m_customer_id'] = array
     *                      $filter['start_date'] = string date yyyy-mm-dd
     *                      $filter['end_date'] = string date yyyy-mm-dd
     * @param integer $itemPerPage jumlah data yang ditampilkan, kosongi jika ingin menampilkan semua data
     * @param string $sort nama kolom untuk melakukan sorting mysql beserta tipenya DESC / ASC
     *
     * @return array
     */
    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $categories = $this->voucher->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $categories
        ];
    }

    /**
     * Mengambil 1 data voucher dari tabel m_voucher
     *
     * @param integer $id id dari tabel m_voucher
     *
     * @return array
     */
    public function getById(int $id): array
    {
        $voucher = $this->voucher->getById($id);
        if (empty($voucher)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $voucher
        ];
    }

    /**
     * method untuk mengubah voucher pada tabel m_voucher
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *                       $payload['id'] = number
     *                       $payload['name'] = string
     *                       $payload['m_customer_id'] = number
     *                       $payload['m_voucher_id'] = number
     *                       $payload['start_time'] = string date yyyy-mm-dd
     *                       $payload['end_time'] = string date yyyy-mm-dd
     *                       $payload['total_voucher'] = number
     *                       $payload['nominal_rupiah'] = number
     *                       $payload['description'] = number
     *                       $payload['photo'] = string
     *
     * @return array
     */
    public function update(array $payload, int $id): array
    {
        try {
            $this->voucher->edit($payload, $id);

            $voucher = $this->getById($id);

            return [
                'status' => true,
                'data' => $voucher['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }
}
