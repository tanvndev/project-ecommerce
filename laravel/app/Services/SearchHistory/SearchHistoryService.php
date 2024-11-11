<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\SearchHistory;

use App\Repositories\Interfaces\SearchHistory\SearchHistoryRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\SearchHistory\SearchHistoryServiceInterface;

class SearchHistoryService extends BaseService implements SearchHistoryServiceInterface
{
    protected $searchHistoryRepository;

    public function __construct(
        SearchHistoryRepositoryInterface $searchHistoryRepository,
    ) {
        $this->searchHistoryRepository = $searchHistoryRepository;
    }

    public function paginate()
    {
        $request = request();

        $select = [
            'id',
            'keyword',
            'count',
        ];

        $condition = [
            'search'  => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $pageSize = $request->pageSize;

        $data = $this->searchHistoryRepository->pagination($select, $condition, $pageSize);

        return $data;
    }

    public function create()
    {
        return $this->executeInTransaction(function () {


            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }



    public function update($id)
    {
        return $this->executeInTransaction(function () use ($id) {



            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }

    public function destroy($id)
    {
        return $this->executeInTransaction(function () use ($id) {


            return successResponse(__('messages.delete.success'));
        }, __('messages.delete.error'));
    }

    private function preparePayload(): array
    {
        $payload = request()->except('_token', '_method');

        $formatPayload = $this->formatPayload($payload);

        return $formatPayload;
    }

    private function formatPayload($payload)
    {
        $data = [
            'setting' => [
                'effect' => $payload['effect'],
                'effectSpeed' => $payload['effectSpeed'],
                'transitionSpeed' => $payload['transitionSpeed'],
                'navigation' => $payload['navigation'],
                'autoPlay' => $payload['autoPlay'],
                'pauseOnHover' => $payload['pauseOnHover'],
                'showArrow' => $payload['showArrow'],
                'width' => $payload['width'],
                'height' => $payload['height'],
            ],
            'items' => $payload['items'],

            'code' => $payload['code'],

            'name' => $payload['name'],
        ];

        return $data;
    }

    // CLIENT API //

    public function getAll()
    {
        $request = request();
    }
}
