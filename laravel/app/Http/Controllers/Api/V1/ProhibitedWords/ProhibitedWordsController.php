<?php

namespace App\Http\Controllers\Api\V1\ProhibitedWords;

use App\Enums\ResponseEnum;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProhibitedWords\ProhibitedWordResource;
use App\Http\Requests\ProhibitedWord\{
    StoreProhibitedWordRequest,
    UpdateProhibitedWordRequest
};
use App\Http\Resources\ProhibitedWords\ProhibitedWordCollection;
use App\Services\Interfaces\ProhibitedWord\ProhibitedWordServiceInterface;
use App\Repositories\Interfaces\ProhibitedWord\ProhibitedWordRepositoryInterface;

class ProhibitedWordsController extends Controller
{

    public function __construct(
        protected ProhibitedWordServiceInterface $prohibitedWordService,
        protected ProhibitedWordRepositoryInterface $prohibitedWordRepository
    ) {}

    public function index(): JsonResponse
    {
        $response = $this->prohibitedWordService->paginate();

        $data = new ProhibitedWordCollection($response);

        return successResponse('', $data, true);
    }

    public function store(StoreProhibitedWordRequest $request): JsonResponse
    {

        $response = $this->prohibitedWordService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $brand = new ProhibitedWordResource($this->prohibitedWordRepository->findById($id));

        return successResponse('', $brand, true);
    }

    public function update(UpdateProhibitedWordRequest $request, string $id): JsonResponse
    {

        $response = $this->prohibitedWordService->update($id);

        return handleResponse($response);
    }

    public function destroy(string $id): JsonResponse
    {
        $response = $this->prohibitedWordService->destroy($id);

        return handleResponse($response);
    }
}
