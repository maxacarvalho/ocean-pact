<?php

use App\Http\Controllers\API\Quote\ListAnsweredQuotesController;
use App\Http\Controllers\API\Quote\MarkQuoteAsAcceptedController;
use App\Http\Controllers\IntegraHub\API\Payload\HandlesPayloadController;
use App\Http\Controllers\IntegraHub\API\Payload\IndexPayloadController;
use App\Http\Controllers\IntegraHub\API\Payload\UpdatePayloadStatusController;
use App\Http\Controllers\QuotesPortal\API\StorePurchaseRequestController;
use App\Http\Controllers\QuotesPortal\API\StoreQuoteController;
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
    $router->name('cotacoes.')->prefix('cotacoes')->group(static function (Router $router) {
        $router->get('respondidas', ListAnsweredQuotesController::class)->name('respondidas');
        $router->put('{quote}/aceita', MarkQuoteAsAcceptedController::class)->name('marca-como-aceita');
    });

    $router->name('payload.')->prefix('payload')->group(static function (Router $router) {
        $router->get('', IndexPayloadController::class)->name('index');

        $router->post('{integration_type:code}', HandlesPayloadController::class)->name('store');

        $router->put('{payload}', UpdatePayloadStatusController::class)->name('update-status');
    });
});

// QuotesPortal
Route::prefix('quotes-portal')
    ->name('quotes-portal.')
    ->group(static function (Router $router) {
        $router->name('quote.')
            ->prefix('quote')
            ->group(function (Router $router) {
                $router->post('/', StoreQuoteController::class)
                    ->name('store');
            });

        $router->name('purchase-request.')
            ->prefix('purchase-request')
            ->group(function (Router $router) {
                $router->post('/', StorePurchaseRequestController::class)
                    ->name('store');
            });
    });
