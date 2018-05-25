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
        'total_station'
    ];
    public $timestamps = true;

    public function nodes() {
        return $this->hasMany('App\Models\Node');
    }
}
