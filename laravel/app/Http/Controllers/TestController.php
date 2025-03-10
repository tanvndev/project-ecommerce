<?php

namespace App\Http\Controllers;

use App\Http\Resources\Product\Client\ClientProductVariantCollection;
use App\Services\Interfaces\Apriori\AprioriServiceInterface;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function __construct(
        protected AprioriServiceInterface $aprioriService
    ) {}

    public function getOrder(Request $request)
    {

        // $orders = $this->aprioriService->exportOrdersToCsv();
        $response = $this->aprioriService->suggestProducts(34);
        dd($response);
        // $data = new ClientProductVariantCollection($response);

        // return successResponse('', $data, true);
    }
}
