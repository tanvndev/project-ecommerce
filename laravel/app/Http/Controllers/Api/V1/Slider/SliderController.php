<?php

namespace App\Http\Controllers\Api\V1\Slider;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Slider\StoreSliderRequest;
use App\Http\Requests\Slider\UpdateSliderRequest;
use App\Http\Resources\Slider\SliderCollection;
use App\Http\Resources\Slider\SliderResource;
use App\Repositories\Interfaces\Slider\SliderRepositoryInterface;
use App\Services\Interfaces\Slider\SliderServiceInterface;
use Illuminate\Http\JsonResponse;

class SliderController extends Controller
{
    protected $sliderService;

    protected $sliderRepository;

    public function __construct(
        SliderServiceInterface $sliderService,
        SliderRepositoryInterface $sliderRepository
    ) {
        $this->sliderService = $sliderService;
        $this->sliderRepository = $sliderRepository;
    }

    /**
     * Display a listing of the sliders.
     */
    public function index(): JsonResponse
    {
        $this->authorizePermission('sliders.index');
        $paginator = $this->sliderService->paginate();
        $data = new SliderCollection($paginator);

        return successResponse('', $data, true);
    }

    /**
     * Store a newly created slider in storage.
     */
    public function store(StoreSliderRequest $request): JsonResponse
    {
        $this->authorizePermission('sliders.store');
        // return response()->json($request->all());
        $response = $this->sliderService->create();

        return handleResponse($response, ResponseEnum::CREATED);
    }

    /**
     * Display the specified slider.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorizePermission('sliders.show');
        $slider = new SliderResource($this->sliderRepository->findById($id));

        return successResponse('', $slider, true);
    }

    /**
     * Update the specified slider in storage.
     */
    public function update(UpdateSliderRequest $request, string $id): JsonResponse
    {
        $this->authorizePermission('sliders.update');
        // return response()->json($request->all());
        $response = $this->sliderService->update($id);

        return handleResponse($response);
    }

    /**
     * Remove the specified slider from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorizePermission('sliders.destroy');
        $response = $this->sliderService->destroy($id);

        return handleResponse($response);
    }

    // CLIENT API //

    /**
     * Get all sliders for clients.
     */
    public function getAllSlider(): JsonResponse
    {
        $paginator = $this->sliderService->getAllSlider();
        $data = new SliderCollection($paginator);

        return successResponse('', $data, true);
    }
}
