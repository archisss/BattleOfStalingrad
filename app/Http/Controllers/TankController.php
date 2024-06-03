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

use App\Models\Tank;

class TankController extends BaseController
{
    public function index(){
        return Tank::get()->all();
    }

    public function createTank()
    {
        $tank = new Tank;

        $tank->_id = Tank::get()->count()+1;
        $tank->speed = rand(1,10);
        $tank->range = rand(10,50);
        $tank->health = 100;

        $tank->save();

        return response()->json(["response" => "Tank Created"], 201);
    }


      /**
     * @OA\Post(
     *     path="/api/tankInfo/{id}",
     *     summary="Load tank information from database",
     *     @OA\Parameter(
     *         description="Load a tank from database",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get the information for the tank from the database"
     *     )
     * )
     */
    public function tankInfo(Request $request, Response $tank){
        $tank = Tank::get()->find($request->id);
        return $tank;
    }

    public function totalTanks(){
        $tank = new Tank;
        return Tank::get()->count();
    }

    public function getTank($id){
        $tank = Tank::get()->find($id);
        return $tank;
    }

    public function getTankHealth($id){
        $tank = Tank::get()->find($id);
        return $tank->health;
    }

    public function getTankSpeed($id){
        $tank = Tank::get()->find($id);
        return $tank->speed;
    }

    public function getTankRange($id){
        $tank = Tank::get()->find($id);
        return $tank->speed;
    }

    public function updateTankHealth($id,$reduceHealth){
        $tank = Tank::get()->find($id);
        $newHeath = $tank->health - $reduceHealth;
        Tank::get()->find($id)->update([
            'health'=>$newHeath,
        ]);
        return 'Tank health was reduce '. $newHeath;
    }

    public function resetTankHealth($id){
        $tank = Tank::get()->find($id);
        Tank::get()->find($id)->update([
            'health'=>100,
        ]);
        return 'Tank health was reset ';
    }

}
