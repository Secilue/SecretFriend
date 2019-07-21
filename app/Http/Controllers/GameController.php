<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use App\Models\Game;
use Log;

class GameController extends Controller
{
    public function index() 
    {
        $games = Game::with('gameParticipants')->get();
        return view('games.index', compact('games'));
    }

    public function update(Request $request, $id) 
    {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }
        if (Hash::check($request['password'], $game->password)) {
            $game->locked = 0;
            $game->save();
            $game->gameParticipants;
            return response()->json([
                'state' => 'updated',
                'game' => $game,
            ], 200);
        } else {
            return response()->json([
                'state' => 'failed',
                'game_id' => $game->id,
            ], 201);
        }
    }
}
