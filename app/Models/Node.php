<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\NodeInterchange;

class Node extends Model {
    public $table = 't0202_node';
    protected $fillable = [
        'line_id',
        'sequence',
        'name',
        'name_ref',
        'has_interchange',
        'in_service'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $timestamps = true;

    public function line() {
        return $this->belongsTo('App\Models\Line');
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function showInterchange() {
        if (!$this->has_interchange) {
            return false;
        }

        $nodeInterchanges = $this->nodeInterchangeSql($this->id);

        return $nodeInterchanges;
    }

    public function nodeInterchangeSql($nodeId) {
        $sql = "
            SELECT ni.* , lf.`name` as `line_from_name`, lt.`name` as `line_to_name`
                FROM `t0203_node_interchange` ni, `t0201_line` lf, `t0201_line` lt
                    WHERE (ni.`node_from_id` = :nodeId1
                    OR ni.`node_to_id` = :nodeId2)
                    AND lf.`id` = ni.`line_from_id`
                    AND lt.`id` = ni.`line_to_id`
        ";

        return DB::select($sql, [
            'nodeId1' => $nodeId,
            'nodeId2' => $nodeId,
        ]);
    }
}
