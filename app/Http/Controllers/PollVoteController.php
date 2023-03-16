<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PollVoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id, Request $request)
    {
        // vote data is "vote_(poll_data_id)" stores rating value
        $poll = Poll::find($id);
        if($poll == null) {
            Session::flash('status', 'Poll not found!');
            return view('voting_page.index');
        }
        if($poll->is_disabled != false) {
            Session::flash('status', 'Poll is not active!');
            return view('voting_page.index');
        }
        $poll_data = $poll->poll_data;
        // check if user has voted before
        $ip = $request->ip();
        $user_agent = $request->userAgent();
        $has_voted = $poll->poll_votes()->where('ip', $ip)->exists();
        $has_voted_cookies = $request->cookie('poll_' . $id);

        if ($has_voted || $has_voted_cookies) {
            Session::flash('status', 'Thank you for voting!');
            return view('voting_page.index');
        }
        return view('voting_page.index', compact('poll', 'poll_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        // vote data is "vote_(poll_data_id)" stores rating value
        $poll = Poll::find($id);
        $poll_data = $poll->poll_data;
        // check if user has voted before
        $ip = $request->ip();
        $user_agent = $request->userAgent();
        $has_voted = $poll->poll_votes()->where('ip', $ip)->exists();
        $has_voted_cookies = $request->cookie('poll_'.$id);
        if ($has_voted || $has_voted_cookies) {
            return back()->with('status', 'Thank you for voting!');
        }
        foreach ($poll_data as $data) {
            $vote = $request->input('vote_'.$data->id);
            $data->poll_votes()->create([
                'rating' => $vote,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        $cookie = cookie('poll_'.$id, 'voted', 60*24*7);
        return redirect()->route('voting_page.index',$poll->id)->withCookie($cookie)->with('status', 'Thank you for voting!');
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
