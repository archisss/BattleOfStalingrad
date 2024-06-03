<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Client;

class Simulate extends Model
{
    // protected $connection = 'mongodb';
    protected $collection = 'simulate';

}
