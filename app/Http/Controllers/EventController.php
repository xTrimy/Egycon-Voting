<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\EventController as APIEventController;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all events
        $events = (new APIEventController)->index();
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // store event
        $event = (new APIEventController)->store($request);
        return redirect()->route('events.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get event
        $event = (new APIEventController)->show($id);
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = (new APIEventController)->show($id);
        return view('events.create', compact('event'));
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
        // update event
        $event = (new APIEventController)->update($request, $id);
        return redirect()->route('events.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete event
        $event = (new APIEventController)->destroy($id);
        return redirect()->route('events.index');
    }

    /**
     * Show the voting settings form for the specified event.
     *
     * @param  Event  $event
     * @return \Illuminate\Http\Response
     */
    public function votingSettings(Event $event)
    {
        return view('events.voting-settings', compact('event'));
    }

    /**
     * Update the voting settings for the specified event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Event  $event
     * @return \Illuminate\Http\Response
     */
    public function updateVotingSettings(Request $request, Event $event)
    {
        $request->validate([
            'judge_voting_enabled' => 'required|boolean',
            'voting_starts_at' => 'nullable|date',
            'voting_ends_at' => 'nullable|date|after:voting_starts_at',
        ]);

        $event->update([
            'judge_voting_enabled' => $request->boolean('judge_voting_enabled'),
            'voting_starts_at' => $request->voting_starts_at ? \Carbon\Carbon::parse($request->voting_starts_at) : null,
            'voting_ends_at' => $request->voting_ends_at ? \Carbon\Carbon::parse($request->voting_ends_at) : null,
        ]);

        $message = $event->judge_voting_enabled ? 'Judge voting has been enabled.' : 'Judge voting has been disabled.';

        return redirect()->route('events.voting.settings', $event)
            ->with('success', $message);
    }
}
