<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model {
    public $table = 't0204_route';
    protected $fillable = [
        'node_from_id',
        'node_to_id',
        'fare'
    ];
    public $timestamps = true;
}
