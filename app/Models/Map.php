<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Client;

class Map extends Model
{
    // protected $connection = 'mongodb';
    protected $collection = 'maps';

    protected $fillable = [
        'grid'
    ];
}
