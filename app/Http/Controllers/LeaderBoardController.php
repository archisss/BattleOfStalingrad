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

use App\Models\Leaderboard;


class LeaderBoardController extends BaseController
{
     /**
     * @OA\Get(
     *     path="/api/leaderboard",
     *     summary="All-time/global Leaderboard",
     *     @OA\Response(
     *         response=200,
     *         description="Get the information for a map in the database"
     *     )
     * )
     */
    public function leaderBoard(){
        $player = Leaderboard::get();
        $global = [];
        foreach($player as $element){
            array_push($global,[ "player_id" => "Player_".$element->_id, "score" => $element->score]);
        }
        return $global;
    }

    public function updateLeaderBoard(int $id, int $points = 100){
        $player = Leaderboard::get()->find($id);
        if(isset($player)){
            $newScore = $player->score + $points;
        }else{
            $player = $this->createNewPlayer($id);
            $newScore = 0 + $points;
        }

        Leaderboard::get()->find($player)->update([
            'score'=>$newScore,
        ]);
        return 'Update the Player Score';
    }

    public function createNewPlayer(int $id)
    {
        $player = new Leaderboard;
        $player->_id = $id;
        $player->score = 0;
        $player->save();

        return $player->_id;
    }

    public function draw(int $id){
        $this->updateLeaderBoard($id,10);
    }

}
