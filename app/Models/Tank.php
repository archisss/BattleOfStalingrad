<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Client;

class Tank extends Model
{
    // protected $connection = 'mongodb';
    protected $collection = 'tanks';

    protected $fillable = [
        'health'
    ];
}
