<?php

namespace App\Http\Controllers\Api\V1\Statistic;

use App\Http\Controllers\Controller;
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
        $this->authorizePermission('statistics.reportOverview');

        $paginator = $this->statisticService->reportOverview();

        return successResponse('', $paginator, true);
    }

    public function revenueByDate(): JsonResponse
    {
        $this->authorizePermission('statistics.revenueByDate');
        $response = $this->statisticService->revenueByDate();

        return successResponse('', $response, true);
    }

    public function seasonalSale(): JsonResponse
    {
        $this->authorizePermission('statistics.revenueByDate');

        $paginator = $this->statisticService->seasonalSale();

        return successResponse('', $paginator, true);
    }

    public function popularProducts(): JsonResponse
    {
        $this->authorizePermission('statistics.popularProducts');

        $paginator = $this->statisticService->popularProducts();

        return successResponse('', $paginator, true);
    }

    public function loyalCustomers(): JsonResponse
    {
        $this->authorizePermission('statistics.loyalCustomers');

        $paginator = $this->statisticService->loyalCustomers();

        return successResponse('', $paginator, true);
    }

    public function getProductReport()
    {
        $this->authorizePermission('statistics.getProductReport');

        $response = $this->statisticService->getProductReport();

        return successResponse('', $response, true);
    }

    public function searchHistory(): JsonResponse
    {
        $this->authorizePermission('statistics.searchHistory');

        $paginator = $this->statisticService->getSearchHistory();

        return successResponse('', $paginator, true);
    }

    public function lowAndOutOfStockVariants()
    {
        $this->authorizePermission('statistics.lowAndOutOfStockVariants');

        $paginator = $this->statisticService->getLowAndOutOfStockVariants();

        return successResponse('', $paginator, true);
    }
}
