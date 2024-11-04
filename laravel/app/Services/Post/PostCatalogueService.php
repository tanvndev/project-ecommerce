<?php

// Trong Laravel, Service Pattern thường được sử dụng để tạo các lớp service, giúp tách biệt logic của ứng dụng khỏi controller.

namespace App\Services\Post;

use App\Repositories\Interfaces\Post\PostRepositoryInterface;
use App\Services\BaseService;
use App\Services\Interfaces\Post\PostCatalogueServiceInterface;

class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{
    protected $postRepository;

    public function __construct(
        PostRepositoryInterface $postRepository,
    ) {
        $this->postRepository = $postRepository;
    }

    public function paginate()
    {
        $request = request();

        $orderBy = ['id' => 'desc'];
        $relations = ['user'];
        $condition = [
            'search'  => addslashes($request->search),
            'publish' => $request->publish,
            'archive' => $request->boolean('archive'),
        ];

        $pageSize = $request->pageSize;

        $data = $this->postRepository->pagination(['*'], $condition, $pageSize, $orderBy, [], $relations);

        return $data;
    }

    public function create()
    {
        return $this->executeInTransaction(function () {
            $payload = $this->preparePayload();

            $this->postRepository->create($payload);

            return successResponse(__('messages.create.success'));
        }, __('messages.create.error'));
    }

    public function update($id)
    {
        return $this->executeInTransaction(function () use ($id) {

            $payload = $this->preparePayload();
            $this->postRepository->update($id, $payload);

            return successResponse(__('messages.update.success'));
        }, __('messages.update.error'));
    }

    public function destroy($id)
    {
        return $this->executeInTransaction(function () use ($id) {
            $this->postRepository->delete($id);

            return successResponse(__('messages.delete.success'));
        }, __('messages.delete.error'));
    }

    private function preparePayload(): array
    {
        $payload = request()->except('_token', '_method');

        $payload['user_id'] = auth()->user()->id;
        $payload = $this->createSEO($payload);

        return $payload;
    }

    // CLIENT API //

    public function getAllPost()
    {
        $request = request();

        $condition = ['publish' => 1];

        $orderBy = ['id' => 'DESC'];

        $data = $this->postRepository->findByWhere($condition, ['*'], [], true, $orderBy);

        return $data;
    }

    public function getPost(array $conditions = [])
    {
        $request = request();

        $condition = [
            'publish' => 1,
        ];

        $condition = array_merge($condition, $conditions);

        $orderBy = ['id' => 'DESC'];

        $data = $this->postRepository->findByWhere($condition, ['*'], [], false, $orderBy);

        return $data;
    }
}
