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
    public function revenueByDate(): JsonResponse
    {
        $paginator = $this->statisticService->paginate();

        return successResponse('', $paginator, true);
    }

}
