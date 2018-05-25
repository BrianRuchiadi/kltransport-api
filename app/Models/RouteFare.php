<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteFare extends Model {
    public $table = 't0205_route_fare';
    protected $fillable = [
        'route_id',
        'fare_type',
        'fare'
    ];
    public $timestamps = true;
}
