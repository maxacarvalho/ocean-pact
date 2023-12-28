<?php

namespace App\Http\Controllers\QuotesPortal\API\Product;

use App\Http\Controllers\Controller;
use App\Jobs\QuotesPortal\MassCreateOrUpdateProductPayloadProcessorJob;
use Cerbero\JsonParser\JsonParser;
use Cerbero\JsonParser\Tokens\Parser;
use Cerbero\JsonParser\Tokens\Token;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MassCreateOrUpdateProductsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $json = JsonParser::parse($request->getContent())->lazyPointer('/products');

        /** @var Token $value */
        foreach ($json as $key => $value) {
            /** @var Parser $item */
            foreach ($value as $item) {
                MassCreateOrUpdateProductPayloadProcessorJob::dispatch($item->toArray());
            }
        }

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
