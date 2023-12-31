<?php

namespace App\Http\Controllers\QuotesPortal\API\Product;

use App\Http\Controllers\Controller;
use App\Jobs\QuotesPortal\BatchMassCreateOrUpdateProductsPayloadJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MassCreateOrUpdateProductsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $timestamp = now()->format('Y-m-d-H-i-s');
        $filename = "mass-create-or-update-products-{$timestamp}.json";
        $result = Storage::disk('local')->put($filename, $request->getContent());

        BatchMassCreateOrUpdateProductsPayloadJob::dispatchAfterResponse($filename);

        return response()->json([
            'message' => 'Products are being processed',
            'result' => $result,
        ], Response::HTTP_ACCEPTED);
    }
}
