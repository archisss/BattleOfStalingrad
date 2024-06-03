<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use MongoDB\Laravel\Eloquent\Model;

use App\Models\Test;


class TestController extends BaseController
{
    public function show(){
        $test = Test::all();
        return $test;

    }

    public function store(Request $request)
    {
        $post = new Test;

        $post->_id = $request->_id;

        $post->save();

        return response()->json(["result" => "ok"], 201);
    }
}
