<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Client;

class Leaderboard extends Model
{
    // protected $connection = 'mongodb';
    protected $collection = 'leaderboard';

    protected $fillable = [
        'score'
    ];
}
