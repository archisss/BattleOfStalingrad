<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/start', 'App\Http\Controllers\LoadGameController@index');

Route::get('/test', 'App\Http\Controllers\TestController@show');

Route::get('/ct', 'App\Http\Controllers\TankController@createTank');
Route::get('/nt/{id}', 'App\Http\Controllers\TankController@newTank');
Route::get('/tanks', 'App\Http\Controllers\TankController@index');
Route::get('/health/{id}/{reduceHealth}', 'App\Http\Controllers\TankController@updateTankHealth');

Route::get('/nm', 'App\Http\Controllers\MapController@index');
Route::get('/cm/{m}/{n}', 'App\Http\Controllers\MapController@createMap');
Route::get('/um/{map}/{t1}/{t2}', 'App\Http\Controllers\MapController@updateMap');
Route::get('/vm/{map}','App\Http\Controllers\MapController@viewFullMap');
Route::get('/vp/{map}/{x}/{y}', 'App\Http\Controllers\MapController@viewPosition');
Route::get('/st/{mapid}/{row}/{col}/{newValue}', 'App\Http\Controllers\MapController@setTanks');

Route::get('/tanksLocations', 'App\Http\Controllers\TankLocationController@index');
Route::get('/sl', 'App\Http\Controllers\TankLocationController@createInitalTankLocation');
Route::get('/cp/{id}', 'App\Http\Controllers\TankLocationController@currentPosition');
Route::get('/dl/{id}', 'App\Http\Controllers\TankLocationController@delete');
Route::get('/up/{id}/{x}/{y}', 'App\Http\Controllers\TankLocationController@updateTankLocation');

Route::get('/s', 'App\Http\Controllers\SimulateController@index');
Route::get('/m/{id}/{mapid}', 'App\Http\Controllers\SimulateController@availabeMove');
Route::get('/d', 'App\Http\Controllers\SimulateController@decision');
Route::get('/turn', 'App\Http\Controllers\SimulateController@turn');
//Route::get('/s/{t1}/{t2}/{map}', 'App\Http\Controllers\SimulateController@index');
Route::get('/s', 'App\Http\Controllers\SimulateController@index');

//Temp Location routes
Route::get('/tmpl/{id}', 'App\Http\Controllers\TempLocationController@oldPositionTank');

//Leaderboard routes
Route::get('/lb/{id}/{points?}', 'App\Http\Controllers\LeaderBoardController@updateLeaderBoard');
Route::get('/lball', 'App\Http\Controllers\LeaderBoardController@leaderBoard');
