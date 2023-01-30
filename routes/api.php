<?php

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
        $router->get('', \App\Http\Controllers\API\Payload\IndexPayloadController::class)->name('index');
        $router->post('', \App\Http\Controllers\API\Payload\StorePayloadController::class)->name('store');
    });
});
