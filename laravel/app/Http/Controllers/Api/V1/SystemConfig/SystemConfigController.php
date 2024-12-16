<?php

namespace App\Http\Controllers\Api\V1\SystemConfig;

use App\Enums\ResponseEnum;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SystemConfig\SystemConfigRepositoryInterface;
use App\Services\Interfaces\SystemConfig\SystemConfigServiceInterface;
use Illuminate\Http\JsonResponse;

class SystemConfigController extends Controller
{
    protected $systemConfigRepository;

    protected $systemConfigService;

    public function __construct(
        SystemConfigRepositoryInterface $systemConfigRepository,
        SystemConfigServiceInterface $systemConfigService
    ) {
        $this->systemConfigRepository = $systemConfigRepository;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * Display a listing of the system configurations.
     */
    public function index(): JsonResponse
    {
        $data = $this->systemConfigService->all();

        return successResponse('', $data, true);
    }

    /**
     * Update the system configurations.
     */
    public function update(): JsonResponse
    {
        $this->authorizePermission('system-configs.update');
        $response = $this->systemConfigService->update();

        return handleResponse($response, ResponseEnum::CREATED);
    }
}
