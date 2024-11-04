<?php

namespace App\Http\Controllers\Api\V1\Post;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostCatalogueRequest;
use App\Http\Requests\Post\UpdatePostCatalogueRequest;
use App\Http\Resources\Post\PostCatalogueCollection;
use App\Http\Resources\Post\PostCatalogueResource;
use App\Repositories\Interfaces\Post\PostCatalogueRepositoryInterface;
use App\Services\Interfaces\Post\PostCatalogueServiceInterface;
use Illuminate\Http\JsonResponse;

class PostCatalogueController extends Controller
{
    protected $PostCatalogueService;

    protected $PostCatalogueRepository;

    public function __construct(
        PostCatalogueServiceInterface $PostCatalogueService,
        PostCatalogueRepositoryInterface $PostCatalogueRepository
    ) {
        $this->PostCatalogueService = $PostCatalogueService;
        $this->PostCatalogueRepository = $PostCatalogueRepository;
    }

    /**
     * Display a listing of the Post catalogues.
     */
    public function index(): JsonResponse
    {
        $response = $this->PostCatalogueService->paginate();

        return successResponse('', $response, true);
    }

    /**
     * Store a newly created Post catalogue in storage.
     */
    public function store(StorePostCatalogueRequest $request): JsonResponse
    {
        $response = $this->PostCatalogueService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Display the specified Post catalogue.
     */
    public function show(string $id): JsonResponse
    {
        $response = new PostCatalogueResource($this->PostCatalogueRepository->findById($id));

        return successResponse('', $response, true);
    }

    /**
     * Update the specified Post catalogue in storage.
     */
    public function update(UpdatePostCatalogueRequest $request, string $id): JsonResponse
    {
        $response = $this->PostCatalogueService->update($id);

        return handleResponse($response);
    }

    /**
     * Remove the specified Post catalogue from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $response = $this->PostCatalogueService->destroy($id);

        return handleResponse($response);
    }

    /**
     * List all Post catalogues for the client.
     */
    public function list(): JsonResponse
    {
        $paginator = $this->PostCatalogueService->list();
        $data = new PostCatalogueCollection($paginator);

        return successResponse('', $data, true);
    }
}
