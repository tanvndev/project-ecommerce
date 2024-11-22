<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Slider;

use App\Repositories\Interfaces\Slider\SliderRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Slider\SliderServiceInterface;

class SliderService extends BaseService implements SliderServiceInterface
{
    protected $sliderRepository;

    public function __construct(
        SliderRepositoryInterface $sliderRepository,
    ) {
        $this->sliderRepository = $sliderRepository;
    }

    public function paginate()
    {
        $request = request();

        $select = [
            'id',
            'name',
            'code',
            'items',
            'setting',
            'publish',
        ];

        $condition = [
            'search'  => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $pageSize = $request->pageSize;

        $data = $this->sliderRepository->pagination($select, $condition, $pageSize);

        return $data;
    }

    public function create()
    {
        return $this->executeInTransaction(function () {
            // dd("Dưng");
            $payload = $this->preparePayload();

            // return $payload;
            // dd($payload);

            $this->sliderRepository->create($payload);

            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }

    public function update($id)
    {
        return $this->executeInTransaction(function () use ($id) {

            $payload = $this->preparePayload();

            $this->sliderRepository->update($id, $payload);

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }

    public function destroy($id)
    {
        return $this->executeInTransaction(function () use ($id) {
            $this->sliderRepository->delete($id);

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
                'effect'          => $payload['effect'],
                'effectSpeed'     => $payload['effectSpeed'],
                'transitionSpeed' => $payload['transitionSpeed'],
                'navigation'      => $payload['navigation'],
                'autoPlay'        => $payload['autoPlay'],
                'pauseOnHover'    => $payload['pauseOnHover'],
                'showArrow'       => $payload['showArrow'],
                'width'           => $payload['width'],
                'height'          => $payload['height'],
            ],
            'items' => $payload['items'],

            'code' => $payload['code'],

            'name' => $payload['name'],
        ];

        return $data;
    }

    // CLIENT API //

    public function getAllSlider()
    {
        $request = request();

        $select = [
            'id',
            'name',
            'code',
            'items',
            'setting',
            'publish',
        ];

        $condition = ['publish' => 1];

        $orderBy = ['id' => 'DESC'];

        $data = $this->sliderRepository->findByWhere($condition, $select, [], true, $orderBy);

        return $data;
    }
}
