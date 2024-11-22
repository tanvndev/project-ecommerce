<?php

namespace App\Services\Interfaces\Voucher;

interface VoucherServiceInterface
{
    public function paginate();

    public function create();

    public function update(string $id);

    public function destroy(string $id);

    public function getAllVoucher();

    public function applyVoucher(string $code, string $id = '');
}
