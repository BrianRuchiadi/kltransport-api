<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteFare extends Model {
    public $table = 't0205_route_fare';
    protected $fillable = [
        'node_from_id',
        'node_to_id',
        'cash_fare',
        'cashless_fare',
        'concession_fare',
        'weekly_fare',
        'monthly_fare'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $timestamps = true;
}
