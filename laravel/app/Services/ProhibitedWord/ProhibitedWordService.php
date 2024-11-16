<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\ProhibitedWord;

use App\Services\BaseService;
use App\Services\Interfaces\ProhibitedWord\ProhibitedWordServiceInterface;
use App\Repositories\Interfaces\ProhibitedWord\ProhibitedWordRepositoryInterface;

class ProhibitedWordService extends BaseService implements ProhibitedWordServiceInterface
{
    protected $prohibitedWordRepository;
    protected $filePath;

    public function __construct(
        ProhibitedWordRepositoryInterface $prohibitedWordRepository,
    ) {
        $this->prohibitedWordRepository = $prohibitedWordRepository;
    }

    public function paginate()
    {
        $request = request();

        $condition = [
            'search'  => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $select = ['id', 'keyword'];
        $pageSize = $request->pageSize;

        $data = $this->prohibitedWordRepository->pagination($select, $condition, $pageSize);

        return $data;
    }

    public function create()
    {
        return $this->executeInTransaction(function () {

            $payload = $this->preparePayload();
            $this->prohibitedWordRepository->create($payload);


            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }


    public function update($id)
    {
        return $this->executeInTransaction(function () use ($id) {

            $payload = $this->preparePayload();

            $this->prohibitedWordRepository->update($id, $payload);

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }


    private function preparePayload(): array
    {
        $payload = request()->except('_token', '_method');
        $payload = $this->createSEO($payload);

        return $payload;
    }
}
