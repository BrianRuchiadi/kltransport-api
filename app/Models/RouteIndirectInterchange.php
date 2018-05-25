<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteIndirectInterchange extends Model {
    public $table = 't0206_route_indirect_interchange';
    protected $fillable = [
        'line_id_from',
        'line_id_to',
        'sequence_from_start',
        'sequence_from_end',
        'sequence_to_start',
        'sequence_to_end',
        'total_transit',
        'transit_1_interchange_id',        
        'transit_2_interchange_id',
        'transit_3_interchange_id',
        'transit_4_interchange_id',
        'transit_5_interchange_id',
        'transit_6_interchange_id',
        'transit_7_interchange_id',
        'transit_8_interchange_id',
        'transit_9_interchange_id',
        'transit_10_interchange_id',
        'updated_by'
    ];

    public $timestamps = true;

    public function interchangeOne() {
        if ($this->total_transit < 1) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'transit_1_interchange_id', 'id');
    }

    public function interchangeTwo() {
        if ($this->total_transit < 2) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'transit_2_interchange_id', 'id');
    }

    public function interchangeThree() {
        if ($this->total_transit < 3) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'transit_3_interchange_id', 'id');
    }

    public function interchangeFour() {
        if ($this->total_transit < 4) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'transit_4_interchange_id', 'id');
    }

    public function interchangeFive() {
        if ($this->total_transit < 5) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'transit_5_interchange_id', 'id');
    }

    public function interchangeSix() {
        if ($this->total_transit < 6) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'transit_6_interchange_id', 'id');
    }

    public function interchangeSeven() {
        if ($this->total_transit < 7) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'transit_7_interchange_id', 'id');
    }

    public function interchangeEight() {
        if ($this->total_transit < 8) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'transit_8_interchange_id', 'id');
    }

    public function interchangeNine() {
        if ($this->total_transit < 9) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'transit_9_interchange_id', 'id');
    }

    public function interchangeTen() {
        if ($this->total_transit < 10) {
            return;
        }

        return $this->belongsTo('App\Models\NodeInterchange', 'transit_10_interchange_id', 'id');
    }


}
