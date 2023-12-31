<?php

namespace App\Http\Controllers\QuotesPortal\API\Product;

use App\Http\Controllers\Controller;
use App\Jobs\QuotesPortal\BatchMassCreateOrUpdateProductsPayloadJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MassCreateOrUpdateProductsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $timestamp = now()->format('Y-m-d-H-i-s');
        $filename = "mass-create-or-update-products-{$timestamp}.json";

        BatchMassCreateOrUpdateProductsPayloadJob::dispatchAfterResponse($filename);

        return response()->json([
            'message' => 'Products are being processed',
        ], Response::HTTP_ACCEPTED);
    }
}
