<?php

namespace App\Http\Controllers;

use App\Models\Cosplayer;
use Illuminate\Http\Request;

class CosplayerVoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($cosplayer)
    {
        $cosplayer = Cosplayer::with(['images','references'])->findOrFail($cosplayer);
        $vote = $cosplayer->vote(auth()->user());
        $events = auth()->user()->events()->pluck('event_id')->toArray();
        if (!in_array($cosplayer->event_id, $events))
            return redirect()->route('cosplayers.index');
        return view('cosplayers.vote', compact('cosplayer', 'vote'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:cosplayers,id',
            'score' => 'required|integer|min:0|max:100',
            'comments' => 'nullable|string|max:255',
        ]);
        // check if user has access to cosplayer 
        $cosplayer = Cosplayer::findOrFail($request->id);
        $events = auth()->user()->events()->pluck('event_id')->toArray();
        if(!in_array($cosplayer->event_id, $events))
            return redirect()->route('cosplayers.index');
      
        $vote = $cosplayer->vote(auth()->user());
        if($vote){
            $vote->update([
                'vote' => $request->score??1,
                'comment' => $request->comments,
            ]);
            return redirect()->route('cosplayers.index');
        }
        // create vote
        $cosplayer->votes()->create([
            'vote' => $request->score??1,
            'user_id' => auth()->id(),
            'comment' => $request->comments,
        ]);

        return redirect()->route('cosplayers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
