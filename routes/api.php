<?php

use App\Http\Controllers\IntegraHub\API\Payload\HandlesPayloadController;
use App\Http\Controllers\IntegraHub\API\Payload\ListPayloadController;
use App\Http\Controllers\IntegraHub\API\Payload\MarkPayloadAsCollectedController;
use App\Http\Controllers\QuotesPortal\API\ApproveQuoteItemsController;
use App\Http\Controllers\QuotesPortal\API\ListQuotesController;
use App\Http\Controllers\QuotesPortal\API\Product\StoreProductController;
use App\Http\Controllers\QuotesPortal\API\Product\UpdateProductController;
use App\Http\Controllers\QuotesPortal\API\StoreOrUpdatePaymentConditionBatchController;
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

Route::middleware('auth:sanctum')
    ->prefix('integra-hub')
    ->name('integra-hub.')
    ->group(static function (Router $router) {
        $router->name('payload.')
            ->group(static function (Router $router) {
                $router->get('/payloads', ListPayloadController::class)->name('index');

                $router->post('/payloads/{integration_type:code}', HandlesPayloadController::class)->name('store');

                $router->put('/payloads/{payload}/mark-as-collected', MarkPayloadAsCollectedController::class)->name('update-status');
            });
    });

// QuotesPortal
Route::middleware('auth:sanctum')
    ->prefix('quotes-portal')
    ->name('quotes-portal.')
    ->group(static function (Router $router) {
        $router->name('quote.')
            ->group(function (Router $router) {
                $router->get('/quotes', ListQuotesController::class)
                    ->name('index');

                $router->post('/quotes', StoreQuoteController::class)
                    ->name('store');

                $router->put('/quotes/{quote}/approve-items', ApproveQuoteItemsController::class)
                    ->name('approve-quote-items');
            });

        $router->name('product.')
            ->group(function (Router $router) {
                $router->post('/products', StoreProductController::class)
                    ->name('store');

                $router->put('/products/{code}/{companyCode}/{companyCodeBranch}', UpdateProductController::class)
                    ->name('update');
            });

        $router->name('purchase-request.')
            ->group(function (Router $router) {
                $router->post('/purchase-requests', StorePurchaseRequestController::class)
                    ->name('store');
            });

        $router->name('payment-conditions.')
            ->group(function (Router $router) {
                $router->post('/payment-conditions/batch', StoreOrUpdatePaymentConditionBatchController::class)
                    ->name('store-or-update-batch');
            });
    });
