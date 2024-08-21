<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Attribute;

use App\Repositories\Interfaces\Attribute\AttributeRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Attribute\AttributeServiceInterface;

class AttributeService extends BaseService implements AttributeServiceInterface
{
    protected $attributeRepository;

    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
    ) {
        $this->attributeRepository = $attributeRepository;
    }

    public function paginate()
    {
        $condition = [
            'search' => addslashes(request('search')),
        ];
        $select = ['id', 'name', 'code', 'description'];
        $pageSize = request('pageSize');

        $data = $pageSize && request('page')
            ? $this->attributeRepository->pagination($select, $condition, $pageSize)
            : $this->attributeRepository->all($select, ['attribute_values']);

        return $data;
    }

    public function create()
    {
        return $this->executeInTransaction(function () {

            $payload = request()->except('_token', '_method');
            $this->attributeRepository->create($payload);

            return successResponse('Tạo mới thành công.');
        }, 'Tạo mới thất bại.');
    }

    public function update($id)
    {
        return $this->executeInTransaction(function () use ($id) {

            $payload = request()->except('_token', '_method');
            $this->attributeRepository->update($id, $payload);

            return successResponse('Cập nhập thành công.');
        }, 'Cập nhập thất bại.');
    }

    public function destroy($id)
    {
        return $this->executeInTransaction(function () use ($id) {
            $this->attributeRepository->delete($id);

            return successResponse('Xóa thành công.');
        }, 'Xóa thất bại.');
    }
}
