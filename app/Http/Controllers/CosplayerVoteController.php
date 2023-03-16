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
        return view('cosplayers.vote', compact('cosplayer'));
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
            'score' => 'required|integer|min:1|max:10',
            'comments' => 'nullable|string|max:255',
        ]);
        
        $cosplayer = Cosplayer::findOrFail($request->id);
        // check if user has already voted
        if($cosplayer->votes()->where('user_id', auth()->id())->exists())
            return redirect()->route('cosplayers.show', $cosplayer->id);
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
