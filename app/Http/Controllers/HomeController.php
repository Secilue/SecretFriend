<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Participant;
use Log;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function store(Request $request) 
    {
        if (Auth::check())
        {
            $userId = Auth::user()->getId();
        }
        $data_participants = [
            'name' => $request['name'],
            'cellphone_number' => $request['cellphoneNumber'],
            'from' => $request['from'],
            'give_to' => $request['give_to'],
        ];
        //$team = collect($data_participants)->except('name', 'cellphoneNumber','give_to','from')->toArray();
        $game = new Game();
        $game->password = bcrypt($request['password']); // Encrypt password.
        $game->user_id = $userId;
        $game->qty_participants = $request['qty_participants'];
        $game->save();
        $list = [];
        // Save each participant in Database.
        foreach ($data_participants['name'] as $key => $participant) {
            $temp = [];
            $participants = new Participant();
            $participants->name = $request['name'][$key];
            $participants->cellphone_number = $request['cellphoneNumber'][$key];
            $participants->save();
            $temp['name'] = $participant;
            $temp['id'] = $participants->id;
            $list[$key] = $temp;
        }
        // Save secret friend in intermediate table.
        foreach ($data_participants['from'] as $key => $from) {
            $l = [];
            $temp = [];
            $from_id = array_search($from, array_column($list, 'name'));
            $to_id = array_search($request['give_to'][$key], array_column($list, 'name'));
            $temp['participant_id'] = $list[$from_id]['id'];
            $temp['give_to'] = $list[$to_id]['id'];
            $l[$key] = $temp;
            $game->gameParticipants()->attach($l);
        }
        return response()->json([
            'state' => 'created',
            'game_id' => $game->id,
        ], 200);
    }
}
