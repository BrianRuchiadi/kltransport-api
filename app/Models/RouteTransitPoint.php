<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteTransitPoint extends Model {
    public $table = 't0207_route_transit_point';
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

    public function interchangeOne() {
        if ($this->total_routes < 1) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'interchange_1_id', 'id');
    }

    public function interchangeTwo() {
        if ($this->total_routes < 2) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'interchange_2_id', 'id');
    }

    public function interchangeThree() {
        if ($this->total_routes < 3) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'interchange_3_id', 'id');
    }

    public function interchangeFour() {
        if ($this->total_routes < 4) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'interchange_4_id', 'id');
    }

    public function interchangeFive() {
        if ($this->total_routes < 5) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'interchange_5_id', 'id');
    }

    public function interchangeSix() {
        if ($this->total_routes < 6) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'interchange_6_id', 'id');
    }

    public function interchangeSeven() {
        if ($this->total_routes < 7) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'interchange_7_id', 'id');
    }

    public function interchangeEight() {
        if ($this->total_routes < 8) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'interchange_8_id', 'id');
    }

    public function interchangeNine() {
        if ($this->total_routes < 9) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'interchange_9_id', 'id');
    }

    public function interchangeTen() {
        if ($this->total_routes < 10) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'interchange_10_id', 'id');
    }
}
