<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use SoftDeletes;
    protected $table = 'games';
    //
    public function participants() {
        return $this->hasyMany(Participant::class)->withTimestamps();
    }

    public function gameParticipants() {
        return $this->belongsToMany(Participant::class, 'games_participants', 'game_id', 'participant_id')->withPivot('give_to')->withTimestamps();
    }
}
