<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteTransitPoint extends Model {
    public $table = 't0206_route_transit_point';
    protected $fillable = [
        'node_from_id',
        'node_to_id',
        'line_from_id',
        'line_to_id',
        'total_interchanges',
        'total_routes',
        'interchange_1_id',
        'interchange_2_id',
        'interchange_3_id',
        'interchange_4_id',
        'interchange_5_id',
        'interchange_6_id',
        'interchange_7_id',
        'interchange_8_id',
        'interchange_9_id',
        'interchange_10_id',
    ];
    public $timestamps = true;
}
