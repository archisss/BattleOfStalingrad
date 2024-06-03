<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Client;

class TankLocation extends Model
{
    // protected $connection = 'mongodb';
    protected $collection = 'tankLocation';

    protected $fillable = [
        'x',
        'y'
    ];

}
