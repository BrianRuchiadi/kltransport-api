<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model {
    public $table = 't0201_line';
    protected $fillable = [
        'type',
        'reference',
        'name',
        'icon',
        'total_station',
        'is_active'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $timestamps = true;

    public function nodes() {
        return $this->hasMany('App\Models\Node');
    }
}
