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

use App\Models\TankLocation;


class TankLocationController extends BaseController
{
    public function index(){
        //return TankLocation::get()->count()+1;
        return TankLocation::get()->all();
    }

    public function randomTankLocation(int $m, int $n, int $tank){
        $alreadyHasLocation = TankLocation::get()->find($tank);
        if(!isset($alreadyHasLocation)){
            $localtion = new TankLocation;
            $localtion->_id = $tank;
            $localtion->x = $this->randomLocation($m-1);
            $localtion->y = $this->randomLocation($n-1);
            $localtion->save();
            app('App\Http\Controllers\TempLocationController')->oldPositionTank($tank);
        }
        return 'location updated';//response()->json(["response" => "Tanks are set"], 200);
    }

    public function createInitalTankLocation($m = 10, $n = 10, $t1 = 7, $t2 = 5){
        $localtion = new TankLocation;
        $localtion->_id = $t1;
        $localtion->x = $this->randomLocation($m-1);
        $localtion->y = $this->randomLocation($n-1);
        $localtion->save();
        app('App\Http\Controllers\TempLocationController')->oldPositionTank($t1);

        $localtion = new TankLocation;
        $localtion->_id = $t2;
        $localtion->x = $this->randomLocation($m-1);
        $localtion->y = $this->randomLocation($n-1);
        $localtion->save();
        app('App\Http\Controllers\TempLocationController')->oldPositionTank($t2);

        return response()->json(["response" => "Tanks are set"], 200);
    }

    public function randomLocation($axel){
        return rand(1,$axel-1);
    }

    public function currentPosition($id){
        $tank = TankLocation::get()->find($id);
        return $tank;
    }

    public function updateTankLocation($id,int $x, int $y){
        app('App\Http\Controllers\TempLocationController')->oldPositionTank($id);

        $tank = TankLocation::get()->find($id)->update([
            'x'=>$x,
            'y'=>$y
        ]);
        return 'OK';
    }

    public function oldPosition($id){
        $tank = TankLocation::get()->find($id);
        return $tank;
    }

    public function delete($id){
        $tankisset = TankLocation::get()->find($id);
        if(isset($tankisset)){
            $tank = TankLocation::get()->find($id)->delete();
        }
        return 'OK';
    }

}
