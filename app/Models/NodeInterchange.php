<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NodeInterchange extends Model {
    public $table = 't0203_node_interchange';
    protected $fillable = [
        'node_from_id',
        'node_to_id',
        'line_from_id',
        'line_to_id'

    ];
    public $timestamps = true;
}
