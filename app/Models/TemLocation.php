<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Client;

class TemLocation extends Model
{
    // protected $connection = 'mongodb';
    protected $collection = 'tempLocation';

    protected $fillable = [
        'x',
        'y'
    ];

}
