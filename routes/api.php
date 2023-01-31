<?php

use App\Http\Controllers\API\Payload\IndexPayloadController;
use App\Http\Controllers\API\Payload\StorePayloadController;
use App\Http\Controllers\API\Payload\UpdatePayloadStatusController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(static function (Router $router) {
    $router->name('payload.')->prefix('payload')->group(static function (Router $router) {
        $router->get('', IndexPayloadController::class)->name('index');

        $router->post('{integration_type:code}', StorePayloadController::class)->name('store');

        $router->put('{payload}', UpdatePayloadStatusController::class)->name('update-status');
    });
});
