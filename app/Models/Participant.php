<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use SoftDeletes;
    protected $table = 'participants';
    //
    public function games() {
        return $this->belongsToMany(Game::class)->withTimestamps();
    }
}
