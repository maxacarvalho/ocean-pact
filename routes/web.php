<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', static function () {
    return redirect()->to('/admin');
});

Route::prefix('integra-hub/webhooks')
    ->name('integra-hub.webhooks.')
    ->group(static function (Router $router) {
        Route::webhooks('payload');
    });
