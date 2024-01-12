<?php

namespace App\Helpers\Promo;

use App\Helpers\Venturo;
use App\Models\PromoModel;
use Throwable;

class PromoHelper extends Venturo
{
    const PROMO_PHOTO_DIRECTORY = 'foto-promo';

    private $promoModel;

    public function __construct()
    {
        $this->promoModel = new PromoModel();
    }

    public function create(array $payload): array
    {
        try {
            $payload = $this->uploadGetPayload($payload);
            $promo = $this->promoModel->store($payload);

            return [
                'status' => true,
                'data' => $promo
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(int $id): bool
    {
        try {
            $this->promoModel->drop($id);
            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $categories = $this->promoModel->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $categories
        ];
    }

    public function getById(int $id): array
    {
        $promo = $this->promoModel->getById($id);
        if (empty($promo)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $promo
        ];
    }

    public function update(array $payload, int $id): array
    {
        try {
            $payload = $this->uploadGetPayload($payload);
            $this->promoModel->edit($payload, $id);
            $promo = $this->getById($id);

            return [
                'status' => true,
                'data' => $promo['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    private function uploadGetPayload(array $payload)
    {
        /**
         * Jika dalam payload terdapat base64 foto, maka Upload foto ke folder public/uploads/foto-user
         */
        if (!empty($payload['photo'])) {
            $fileName = $this->generateFileName($payload['photo'], 'PROMO_' . date('Ymdhis'));
            $photo = $payload['photo']->storeAs(self::PROMO_PHOTO_DIRECTORY, $fileName, 'public');
            $payload['photo'] = $photo;
        } else {
            unset($payload['photo']);
        }

        return $payload;
    }
}
