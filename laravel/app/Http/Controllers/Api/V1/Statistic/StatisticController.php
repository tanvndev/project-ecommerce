<?php

namespace App\Http\Controllers\Api\V1\Statistic;

use App\Http\Controllers\Controller;
use App\Http\Resources\Statistic\StatisticCollection;
use App\Services\Interfaces\Statistic\StatisticServiceInterface;
use Illuminate\Http\JsonResponse;

class StatisticController extends Controller
{
    protected $statisticService;

    public function __construct(
        StatisticServiceInterface $statisticService,
    ) {
        $this->statisticService = $statisticService;
    }

    /**
     * Display a listing of the Statistics.
     */
    public function reportOverview(): JsonResponse
    {
        $paginator = $this->statisticService->reportOverview();

        return successResponse('', $paginator, true);
    }
    public function revenueByDate(): JsonResponse
    {
        $paginator = $this->statisticService->revenueByDate();

        return successResponse('', $paginator, true);
    }
    public function seasonalSale(): JsonResponse
    {
        $paginator = $this->statisticService->seasonalSale();

        return successResponse('', $paginator, true);
    }
    public function popularProducts(): JsonResponse
    {
        $paginator = $this->statisticService->popularProducts();

        return successResponse('', $paginator, true);
    }
    public function loyalCustomers(): JsonResponse
    {
        $paginator = $this->statisticService->loyalCustomers();

        return successResponse('', $paginator, true);
    }

    public function getProductReport()
    {
        $response = $this->statisticService->getProductReport();

        return successResponse('', $response, true);
    }
}
