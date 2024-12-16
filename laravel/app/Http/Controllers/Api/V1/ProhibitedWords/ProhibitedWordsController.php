<?php

namespace App\Http\Controllers\Api\V1\ProhibitedWords;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProhibitedWord\StoreProhibitedWordRequest;
use App\Http\Requests\ProhibitedWord\UpdateProhibitedWordRequest;
use App\Http\Resources\ProhibitedWords\ProhibitedWordCollection;
use App\Http\Resources\ProhibitedWords\ProhibitedWordResource;
use App\Repositories\Interfaces\ProhibitedWord\ProhibitedWordRepositoryInterface;
use App\Services\Interfaces\ProhibitedWord\ProhibitedWordServiceInterface;
use Illuminate\Http\JsonResponse;

class ProhibitedWordsController extends Controller
{
    public function __construct(
        protected ProhibitedWordServiceInterface $prohibitedWordService,
        protected ProhibitedWordRepositoryInterface $prohibitedWordRepository
    ) {}

    public function index(): JsonResponse
    {
        $this->authorizePermission('prohibited-words.index');
        $response = $this->prohibitedWordService->paginate();

        $data = new ProhibitedWordCollection($response);

        return successResponse('', $data, true);
    }

    public function store(StoreProhibitedWordRequest $request): JsonResponse
    {
        $this->authorizePermission('prohibited-words.store');

        $response = $this->prohibitedWordService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $this->authorizePermission('prohibited-words.show');
        $brand = new ProhibitedWordResource($this->prohibitedWordRepository->findById($id));

        return successResponse('', $brand, true);
    }

    public function update(UpdateProhibitedWordRequest $request, string $id): JsonResponse
    {
        $this->authorizePermission('prohibited-words.update');

        $response = $this->prohibitedWordService->update($id);

        return handleResponse($response);
    }
}
