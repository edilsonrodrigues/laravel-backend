<?php

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

/**
 * @autor Flavio
 * @autor Edilson Rodrigues
 */
try {
    $route = new \App\Core\Library\Route();
    $route->init()->createRoute();
    $uri = '/';
    $callback = function () {
        return response()->json(['title' => 'Oops!', 'message' => 'Rota nao existe'], 400);
    };
    Route::get($uri, $callback);
    Route::post($uri, $callback);
    Route::put($uri, $callback);
    Route::patch($uri, $callback);
    Route::delete($uri, $callback);
    Route::options($uri, $callback);
} catch (Exception $e) {
    Route::{request()->method()}(request()->path(), function () use ($e) {
        return response()->json(['title' => 'Oops!', 'message' => $e->getMessage()], 400);
    });
}
