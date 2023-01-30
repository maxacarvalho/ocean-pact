<?php

namespace App\Http\Controllers\API\Payload;

use App\Data\PayloadData;
use App\Http\Controllers\Controller;
use App\Models\Payload;
use Illuminate\Http\Response;

class StorePayloadController extends Controller
{
    public function __invoke(PayloadData $payloadData)
    {
        Payload::query()->create(
            $payloadData->only(Payload::INTEGRATION_TYPE_ID, Payload::PAYLOAD)->toArray()
        );

        return response()->json([], Response::HTTP_CREATED);
    }
}
