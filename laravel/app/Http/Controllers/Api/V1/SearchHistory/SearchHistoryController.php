<?php

namespace App\Http\Controllers\Api\V1\SearchHistory;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchHistory\StoreSearchHistoryRequest;
use App\Http\Requests\SearchHistory\UpdateSearchHistoryRequest;
use App\Http\Resources\SearchHistory\SearchHistoryCollection;
use App\Http\Resources\SearchHistory\SearchHistoryResource;
use App\Repositories\Interfaces\SearchHistory\SearchHistoryRepositoryInterface;
use App\Services\Interfaces\SearchHistory\SearchHistoryServiceInterface;
use Illuminate\Http\JsonResponse;

class SearchHistoryController extends Controller
{

    protected $searchHistoryService;

    protected $searchHistoryRepository;

    public function __construct(
        SearchHistoryServiceInterface $searchHistoryService,
        SearchHistoryRepositoryInterface $searchHistoryRepository
    ) {
        $this->searchHistoryService = $searchHistoryService;
        $this->searchHistoryRepository = $searchHistoryRepository;
    }

    /**
     * Display a listing of the searchHistorys.
     */
    public function index(): JsonResponse
    {
        $paginator = $this->searchHistoryService->paginate();
        $data = new SearchHistoryCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created searchHistory in storage.
     */
    public function store(StoreSearchHistoryRequest $request): JsonResponse
    {
        // return response()->json($request->all());
        $response = $this->searchHistoryService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Display the specified searchHistory.
     */
    public function show(string $id): JsonResponse
    {
        $searchHistory = new SearchHistoryResource($this->searchHistoryRepository->findById($id));

        return successResponse('', $searchHistory, true);
    }

    /**
     * Update the specified searchHistory in storage.
     */
    public function update(UpdateSearchHistoryRequest $request, string $id): JsonResponse
    {
        // return response()->json($request->all());
        $response = $this->searchHistoryService->update($id);

        return handleResponse($response);
    }

    /**
     * Remove the specified searchHistory from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $response = $this->searchHistoryService->destroy($id);

        return handleResponse($response);
    }

    // CLIENT API //

    /**
     * Get all searchHistory for clients.
     */
    public function getAll(): JsonResponse
    {
        $paginator = $this->searchHistoryService->getAll();
        $data = new SearchHistoryCollection($paginator);

        return successResponse('', $data, true);
    }
}
