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

use App\Models\TemLocation;

class TempLocationController extends BaseController
{
    public function existOldPosition($id){
        $tank = TemLocation::get()->find($id);
        return $tank;
    }

    public function oldPositionTank($id){
        $existOldPosition = $this->existOldPosition($id);
        if(!isset($existOldPosition)){ //Not exist yet in the collection
            $tank = app('App\Http\Controllers\TankLocationController')->currentPosition($id);
            if(!isset($tank)){ //Not previous position in grid
                return 'NotOldPosition';
            }else{
            $old_tank = new TemLocation;
            $old_tank->_id = $id;
            $old_tank->x = $tank->x;
            $old_tank->y = $tank->y;
            $old_tank->save();
            return $old_tank;
            }
        }
        return $existOldPosition;
    }

    public function delete($id){
        $tank = TemLocation::get()->find($id)->delete();
        return 'OK';
    }

}
