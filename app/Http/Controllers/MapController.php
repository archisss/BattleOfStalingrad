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

use App\Models\Map;


class MapController extends BaseController
{
    public function total(){
        return Map::get()->count()+1;
    }

    public function findMap(){
        $map = new Map;
        $resuilt = Map::find();
    }

    public function createMap($m, $n)
    {
        if ($m < 10 || $n < 10) {
            throw new \InvalidArgumentException("Map dimensions must be at least 50x50");
        }

        $states = ['0', 'X'];
        $grid = [];

        for ($i = 0; $i < $m; $i++) {
            $row = [];
            for ($j = 0; $j < $n; $j++) {
                    $row[] = $states[array_rand($states)];
            }
            $grid[] = $row;
        }

        $map = [
            '_id' => Map::get()->count()+1,
            'grid' => $grid
        ];

        $newMap = new Map;
        $newMap->_id = Map::get()->count()+1;
        $newMap->m = (int)$m;
        $newMap->n = (int)$n;
        $newMap->grid = $grid;
        $newMap->save();

        return response()->json(["response" => "Map was created and ready for next play"], 200);
    }

    public function updateMap($map, int $t1, int $t2)
    {
        $mapinfo = new Map;
        $mapinfo = Map::get()->find($map);
        $m = $mapinfo->m;
        $n = $mapinfo->n;
        $mapid = $mapinfo->_id;

        $grid = [];

        $tank1 = app('App\Http\Controllers\TankLocationController')->currentPosition($t1);
        $tank1_old_validate = app('App\Http\Controllers\TempLocationController')->oldPositionTank($t1);
        $tank1_old = $tank1_old_validate == 'NotOldPosition' ? app('App\Http\Controllers\TankLocationController')->randomTankLocation($m,$n,$t1) : $tank1_old_validate;

        $tank2 = app('App\Http\Controllers\TankLocationController')->currentPosition($t2);
        $tank2_old_validate = app('App\Http\Controllers\TempLocationController')->oldPositionTank($t2);
        $tank2_old = $tank2_old_validate == 'NotOldPosition' ? app('App\Http\Controllers\TankLocationController')->randomTankLocation($m,$n,$t2) : $tank2_old_validate;

        for ($i = 0; $i < $m; $i++) {
            $row = [];
            for ($j = 0; $j < $n; $j++) {
                if($tank1->x == $j and $tank1->y ==$i){
                    $row[] = 'TTTTTT'.$t1;
                }elseif($tank2->x== $j and $tank2->y==$i){
                    $row[] = 'TTTTTT'.$t2;
                }else{
                    $row[] = ($this->viewPosition($mapid,$j,$i)== 'TTTTTT'.$t1 || $this->viewPosition($mapid,$j,$i)== 'TTTTTT'.$t2) ? '0' : $this->viewPosition($mapid,$j,$i);
                }
            }
            $grid[] = $row;
        }

        //update map with new position from tanks
        $this->deleteMap($mapid);

        $map = [
            '_id' => $mapid,
            'grid' => $grid
        ];

        $newMap = new Map;
        $newMap->_id = $mapid;
        $newMap->m = $m;
        $newMap->n = $n;
        $newMap->grid = $grid;
        $newMap->save();

        app('App\Http\Controllers\TempLocationController')->delete($t1);
        app('App\Http\Controllers\TempLocationController')->delete($t2);

        return response()->json(["response" => "Map was update and ready for next play"], 200);
    }

    public function viewFullMap($map){
        $map = Map::get()->find($map);
        foreach($map->grid as $location){
            print_r($location);
            print_r('<br/><br/>');
        }
    }

    public function viewPosition($map, $x, $y){
        $position = new Map;
        $position = Map::get()->find($map);
        $result = $position->grid[$y][$x];
        return $result;
    }

    public function getMapXY($id){
        $MapXY = Map::get()->find($id);
        return $MapXY;
    }

     /**
     * @OA\Post(
     *     path="/api/mapInfo/{id}",
     *     summary="Load map information from database",
     *     @OA\Parameter(
     *         description="Load a map from database",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get the information for a map in the database"
     *     )
     * )
     */
    public function MapInfo(Request $request, Response $tank){
        $Map = Map::get()->find($request->id);
        return $Map;
    }

    static function testStatic(){
        return "static funcion test from Map";
    }

    public function totalMaps(){
        $map = new Map;
        return Map::get()->count();
    }

    public function deleteMap($id){
        $map = Map::get()->find($id)->delete();
        return "Map deleted";
    }
}
