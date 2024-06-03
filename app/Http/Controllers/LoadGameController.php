<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

use MongoDB\Laravel\Eloquent\Model;

class LoadGameController extends BaseController
{
    public function index(){
        //Crate the Tanks
        for($i=1; $i<=15; $i++){
            app('App\Http\Controllers\TankController')->createTank();
        }
        //Create the Maps
        for($i=1; $i<=10; $i++){
            app('App\Http\Controllers\MapController')->createMap(10,10);
        }
        //Deploy the tanks
        for($i=1; $i<=15; $i++){
            app('App\Http\Controllers\TankLocationController')->randomTankLocation(10,10,$i);
        }

        return 'Game is ready';
    }

}
