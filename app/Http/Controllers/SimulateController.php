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
use App\Models\Map;
use App\Models\Simulate;

/**
 * @OA\Info(
 *     title="Battle Game",
 *     version="1.0"
 * )
 */
class SimulateController extends BaseController
{

    /**
     * @OA\Post(
     *     path="/api/AIsimulation/",
     *     description="Automatic simulation  of battle between random tanks in a random map",
     *     @OA\Response(response="200", description="Battle are finish, Simulation completed")
     * )
     */
    public function index()
    {
        //1. Pick the players
        $tank1 = Tank::get()->find(rand(1,\App::call('App\Http\Controllers\TankController@totalTanks'))); //bring a random tank
        $tank2 = new Tank;
        $tempTank = Tank::get()->find(rand(1,\App::call('App\Http\Controllers\TankController@totalTanks')));
        if($tank1->_id == $tempTank->_id) { //validate different tanks are playing
            $tempTank = Tank::get()->find(rand(1,\App::call('App\Http\Controllers\TankController@totalTanks')));
            $tank2 = $tempTank;
        }
        $tank2 = $tempTank;

        //2. Select a map for this game
        $selectMap = Map::get()->find(rand(1,\App::call('App\Http\Controllers\MapController@totalMaps')));
        $mapid = $selectMap->_id;

        $mapXY = app('App\Http\Controllers\MapController')->getMapXY($mapid);
        app('App\Http\Controllers\TankLocationController')->randomTankLocation($mapXY->m,$mapXY->n,$tank1->_id);
        app('App\Http\Controllers\TankLocationController')->randomTankLocation($mapXY->m,$mapXY->n,$tank2->_id);

        //3. Set tank in the map
        app('App\Http\Controllers\MapController')->updateMap($mapid,$tank1->_id,$tank2->_id);

        //4. Simulation starts
        $moves = 0; $turn=0;
        while ($moves <= 20) {
            $moves++;
            if(1 > 0 &&1 > 0){
        //5. check for winner
                if($turn == 0){ //TANK1
                    $turn=1;
                    $t1mo = $this->decision($tank1->_id,$tank2->_id,$mapid);
                }else{ //TANK2
                    $turn=0;
                    $t2mo = $this->decision($tank2->_id,$tank1->_id,$mapid);
                }
            }
        }

        //6. Validate winner adn Update Leaderboard
        $healthTank1 = app('App\Http\Controllers\TankController')->getTankHealth($tank1->_id);
        $healthTank2 = app('App\Http\Controllers\TankController')->getTankHealth($tank2->_id);
        if($healthTank1 == $healthTank2){
            app('App\Http\Controllers\LeaderBoardController')->draw($tank1->_id);
            app('App\Http\Controllers\LeaderBoardController')->draw($tank2->_id);
            $this->saveSimulationScore($tank1->_id, $tank2->_id, $mapid, 'DRAW');
            $res = 'DRAW';
        }elseif($healthTank1 > $healthTank2){
            app('App\Http\Controllers\LeaderBoardController')->updateLeaderBoard($tank1->_id);
            $this->saveSimulationScore($tank1->_id, $tank2->_id, $mapid, 'WINNER Tank 1');
            $res = 'WINNER IS TANK 1';
        }else{
            app('App\Http\Controllers\LeaderBoardController')->updateLeaderBoard($tank2->_id);
            $this->saveSimulationScore($tank2->_id, $tank1->_id, $mapid, 'WINNER Tank 2');
            $res = "WINNER IS TANK 2";
        }

        //7. reset for a new simulation
        app('App\Http\Controllers\TankController')->resetTankHealth($tank1->_id);
        app('App\Http\Controllers\TankController')->resetTankHealth($tank2->_id);

        return response()->json(["response" => $res], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/manualSimulation/",
     *     description="Manual battle simulation between tanks",
     *     @OA\Parameter(
     *         name="tanks[]",
     *         in="query",
     *         description="Create an array of 2 Tanks",
     *         required=true,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer")
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="mapid",
     *         in="query",
     *         description="Map id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Manual simulation was running correctly"),
     *     @OA\Response(response="500", description="One tank can play again itselft")
     * )
     */
    public function manualSimulation(Request $request, Response $tank){
        $tank1 = app('App\Http\Controllers\TankController')->getTank($request->tanks[0]);
        $tank2 = app('App\Http\Controllers\TankController')->getTank($request->tanks[1]);

        if ($tank1 == $tank2 || $tank2 == $tank1) {
            return response()->json(["response" => "One tank can play again itselft"], 500);
        }

        $mapid = $request->mapid;

        //4. Simulation starts
        $moves = 0; $turn=0;
        while ($moves <= 20) {
            $moves++;
            if(1 > 0 &&1 > 0){ //check for winner
                if($turn == 0){ //TANK1
                    //echo 'Turno -'.$moves.'- tank1';
                    $turn=1;
                    $t1mo = $this->decision($tank1->_id,$tank2->_id,$mapid);
                }else{ //TANK2
                    //echo 'Turno  -'.$moves.'- tank2';
                    $turn=0;
                    $t2mo = $this->decision($tank2->_id,$tank1->_id,$mapid);
                }
            }
        }

        //5. Validate winner adn Update Leaderboard
        $healthTank1 = app('App\Http\Controllers\TankController')->getTankHealth($tank1->_id);
        $healthTank2 = app('App\Http\Controllers\TankController')->getTankHealth($tank2->_id);
        if($healthTank1 == $healthTank2){
            app('App\Http\Controllers\LeaderBoardController')->draw($tank1->_id);
            app('App\Http\Controllers\LeaderBoardController')->draw($tank2->_id);
            $this->saveSimulationScore($tank1->_id, $tank2->_id, $mapid, 'DRAW');
            $res = 'DRAW';
        }elseif($healthTank1 > $healthTank2){
            app('App\Http\Controllers\LeaderBoardController')->updateLeaderBoard($tank1->_id);
            $this->saveSimulationScore($tank1->_id, $tank2->_id, $mapid, 'WINNER Tank 1');
            $res = 'WINNER IS TANK 1';
        }else{
            app('App\Http\Controllers\LeaderBoardController')->updateLeaderBoard($tank2->_id);
            $this->saveSimulationScore($tank2->_id, $tank1->_id, $mapid, 'WINNER Tank 2');
            $res = 'WINNER IS TANK 2';
        }

        //6. reset for a new simulation
        app('App\Http\Controllers\TankController')->resetTankHealth($tank1->_id);
        app('App\Http\Controllers\TankController')->resetTankHealth($tank2->_id);


        return response()->json(["response" => $res], 200);

    }

    public function decision(int $tank1,int $tank2,int $mapid){
        $action = rand(0,1);

        if($action == 0 ){ //shoot
            $this->shoot($tank1,$tank2,$mapid);
        }else{ //move
            $posibleMoves = $this->availabeMove($tank1,$mapid);
            $directions = ['left','right','up','down'];
            $goto = $directions[array_rand($directions)];
            $this->move($tank1,$goto,$posibleMoves[$goto]);
        }
    }

    public function move(int $tank,$where,int $step){
        $tankPosition = app('App\Http\Controllers\TankLocationController')->currentPosition($tank);
        $tank = $tankPosition->_id;
        $x = $tankPosition->x;
        $y = $tankPosition->y;
        switch ($where) {
            case 'left':
                app('App\Http\Controllers\TankLocationController')->updateTankLocation($tank,$x-$step,$y);
                break;
            case 'right':
                app('App\Http\Controllers\TankLocationController')->updateTankLocation($tank,$x+$step,$y);
                break;
            case 'up':
                app('App\Http\Controllers\TankLocationController')->updateTankLocation($tank,$x,$y+$step);
                break;
            case 'down':
                app('App\Http\Controllers\TankLocationController')->updateTankLocation($tank,$x,$y-$step);
                break;
        }
    }

    public function shoot(int $tank1,int $tank2, int $mapid){ //tank1 is the shooter
        $tankInfo1 = Tank::get()->find($tank1);
        $tank1Range =  $tankInfo1->range;
        $tank1ShootPower = $tank1Range * $tankInfo1->speed * 2;

        $tankInfo2 = Tank::get()->find($tank2);
        $tank2Range =  $tankInfo2->range;

        $positionTank1 = app('App\Http\Controllers\TankLocationController')->currentPosition($tank1);
        $positionTank2 = app('App\Http\Controllers\TankLocationController')->currentPosition($tank2);

        $mapinfo = new Map;
        $mapinfo = Map::get()->find($mapid);
        $m = $mapinfo->m-1;
        $n = $mapinfo->n-1;

        if($positionTank1->x == $positionTank2->x){
            $haytankenY = 0;
            for($i = $positionTank1->y; $i <=$tank1Range; $i++){
                if($positionTank1->y+$i <= $m and $positionTank1->y+$i >=0){
                    $right = app('App\Http\Controllers\MapController')->viewPosition($mapid,$positionTank1->x,$positionTank1->y+$i);// == 'TTTTTT'.$tank2 ? 1 : 0;
                    if($right == 'TTTTTT'.$tank2){
                        $haytankenY+=1;
                    }
                }
            }
            for($i = $positionTank1->y; $i <=$tank1Range; $i++){
                if($positionTank1->y-$i <= $m and $positionTank1->y-$i >=0){
                    $right = app('App\Http\Controllers\MapController')->viewPosition($mapid,$positionTank1->x,$positionTank1->y-$i);// == 'TTTTTT'.$tank2 ? 1 : 0;
                    if($right == 'TTTTTT'.$tank2){
                        $haytankenY+=1;
                    }
                }
            }
            app('App\Http\Controllers\TankController')->updateTankHealth($tank2,$tank1ShootPower);
        }
        if($positionTank1->y == $positionTank2->y){
            $haytankenX = 0;
            for($i = $positionTank1->x; $i <=$tank1Range; $i++){
                if($positionTank1->x+$i <= $m and $positionTank1->x+$i >=0){
                    $right = app('App\Http\Controllers\MapController')->viewPosition($mapid,$positionTank1->x+$i,$positionTank1->y);// == 'TTTTTT'.$tank2 ? 1 : 0;
                    if($right == 'TTTTTT'.$tank2){
                        $haytankenX+=1;
                    }
                }
            }
            for($i = $positionTank1->x; $i <=$tank1Range; $i++){
                if($positionTank1->x-$i <= $m and $positionTank1->x-$i >=0){
                    $right = app('App\Http\Controllers\MapController')->viewPosition($mapid,$positionTank1->x-$i,$positionTank1->y);// == 'TTTTTT'.$tank2 ? 1 : 0;
                    if($right == 'TTTTTT'.$tank2){
                        $haytankenX+=1;
                    }
                }
            }
            app('App\Http\Controllers\TankController')->updateTankHealth($tank2,$tank1ShootPower);
        }
    }

    public function availabeMove($tank,$mapid){
        $tankInfo = Tank::get()->find($tank);
        $tankSpeed =  $tankInfo->speed;

        $tankPosition = app('App\Http\Controllers\TankLocationController')->currentPosition($tank);

        $mapinfo = new Map;
        $mapinfo = Map::get()->find($mapid);
        $m = $mapinfo->m-1;
        $n = $mapinfo->n-1;


        // 1 = true means the space is available
        $avilableRightMoves = 0;
        for($i = 1; $i <=$tankSpeed; $i++){
            if($tankPosition->x+$i <= $m and $tankPosition->x+$i>=0){
                $right = app('App\Http\Controllers\MapController')->viewPosition($mapid,$tankPosition->x+$i,$tankPosition->y) == '0' ? 1 : 0;
                if($right == '1'){
                    $avilableRightMoves+=1;
                }else{
                    break;
                }
            }else {
                break;
            }
        }

        $avilableLeftMoves = 0;
        for($i = 1; $i <=$tankSpeed; $i++){
            if($tankPosition->x-$i <= $m-1 and $tankPosition->x-$i>=0){
                $left = app('App\Http\Controllers\MapController')->viewPosition($mapid,$tankPosition->x-$i,$tankPosition->y) == '0' ? 1 : 0;
                if($left == '1'){
                    $avilableLeftMoves+=1;
                }else{
                    break;
                }
            }else {
                break;
            }
        }

        $avilableUpMoves = 0;
        for($i = 1; $i <=$tankSpeed; $i++){
            if($tankPosition->y+$i <= $n and $tankPosition->y+$i>=0){
                $up = app('App\Http\Controllers\MapController')->viewPosition($mapid,$tankPosition->x,$tankPosition->y+$i) == '0' ? 1 : 0;
                if($up == '1'){
                    $avilableUpMoves+=1;
                }else{
                    break;
                }
            }else {
                break;
            }
        }

        $avilableDownMoves = 0;
        for($i = 1; $i <=$tankSpeed; $i++){
            if($tankPosition->y-$i <= $n and $tankPosition->y-$i>=0){
                $down = app('App\Http\Controllers\MapController')->viewPosition($mapid,$tankPosition->x,$tankPosition->y-$i) == '0' ? 1 : 0;
                if($down == '1'){
                    $avilableDownMoves+=1;
                }else{
                    break;
                }
            }else {
                break;
            }
        }

        return ['x'=> $m, 'y' => $n, 'TX' => $tankPosition->x, 'TY' => $tankPosition->y, 'left' => $avilableLeftMoves, 'right' => $avilableRightMoves, 'up' => $avilableUpMoves, 'down' => $avilableDownMoves];
    }

    public function saveSimulationScore($winner, $looser, $mapid, $result){
        $game = new Simulate;

        $game->_id = Simulate::get()->count()+1;
        $game->winner = 'Player_'.$winner;
        $game->looser = 'Player_'.$looser;
        $game->mapid = $mapid;
        $game->result = $result;

        $game->save();

        return response()->json(["response" => "Simlation Saved"], 200);
    }

     /**
     * @OA\Post(
     *     path="/api/score/{id}",
     *     summary="display battle score in JSON format",
     *     @OA\Parameter(
     *         description="Display the battle score",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Display the battle score in JSON"
     *     )
     * )
     */
    public function getSimulateResult(Request $request, Response $tank){
        if($request->id == 0 ){
            $game = Simulate::get()->all();
        }else{
            $game = Simulate::get()->find($request->id);
        }
        return $game;
    }
}
