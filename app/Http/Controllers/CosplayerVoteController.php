<?php

namespace App\Http\Controllers;

use App\Models\Cosplayer;
use App\Models\Event;
use App\Notifications\CosplayVoteReport;
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
        $cosplayer = Cosplayer::with(['images','references', 'event'])->findOrFail($cosplayer);
        $vote = $cosplayer->vote(auth()->user());
        $events = auth()->user()->events->pluck('id')->toArray();

        if (!in_array($cosplayer->event_id, $events)) {
            return redirect()->route('cosplayers.index');
        }

        // Check if voting is enabled for this event
        if (!$cosplayer->event->isJudgeVotingEnabled()) {
            return redirect()->route('cosplayers.index')
                ->with('error', $cosplayer->event->getVotingStatusMessage());
        }

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
        $cosplayer = Cosplayer::with('event')->findOrFail($request->id);
        $events = auth()->user()->events->pluck('id')->toArray();
        if(!in_array($cosplayer->event_id, $events))
            return redirect()->route('cosplayers.index');

        // Check if voting is enabled for this event
        if (!$cosplayer->event->isJudgeVotingEnabled()) {
            return redirect()->route('cosplayers.index')
                ->with('error', $cosplayer->event->getVotingStatusMessage());
        }

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

    public function sendTopCosplayersReportView(Event $event){
        return view('cosplayers.send-report', compact('event'));
    }

    public function sendTopCosplayersReportPost(Event $event, Request $request)
    {
        $request->validate([
            'top' => 'required|numeric|min:1|max:100',
        ]);
        $users = $event->users()->get();
        foreach ($users as $user) {
            $user->notify(new CosplayVoteReport($event, $request->top));
        }
        return redirect()->back()->with('success', 'Report sent to subscribed users');
    }
}
