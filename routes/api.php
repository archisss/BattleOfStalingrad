<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TestController;
use App\Http\Controllers\TankController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/tankInfo/{id}', 'App\Http\Controllers\TankController@tankInfo');
Route::post('/mapInfo/{id}', 'App\Http\Controllers\MapController@mapInfo');
Route::post('/manualSimulation', 'App\Http\Controllers\SimulateController@manualSimulation');
Route::post('/AIsimulation', 'App\Http\Controllers\SimulateController@index');
Route::post('/score/{id}', 'App\Http\Controllers\SimulateController@getSimulateResult');
Route::get('/leaderboard', 'App\Http\Controllers\LeaderBoardController@leaderBoard');

