<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Test extends Model
{
    //protected $connection = 'test';
    protected $collection = 'tests';

    // protected $fillable = [
    //     'id'
    // ];
}
