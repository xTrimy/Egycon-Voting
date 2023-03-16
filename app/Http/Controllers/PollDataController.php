<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollData;
use Illuminate\Http\Request;

class PollDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($poll_id)
    {
        $poll = Poll::with('poll_lines')->find($poll_id);
        $poll_data = $poll->poll_data()->with('poll_data_lines')->get();

        $poll_votes = $poll->poll_votes()->get();
        // count unique ip address
        $unique_ip = $poll_votes->unique('ip')->count();
        $number_of_votes = $unique_ip;
        // calculate percentage of each poll data
        foreach($poll_data as $data) {
            $votes = $poll_votes->where('poll_data_id', $data->id)->sum('rating');
            if ($number_of_votes == 0) {
                $number_of_votes = 1;
            }
            $data->avg = $votes / $number_of_votes;
            $data->percentage = $votes / $number_of_votes / 5 * 100;
        }
        return view('polls.poll_data.index', compact('poll', 'poll_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($poll_id)
    {
        $poll = Poll::with(['poll_lines','poll_data.poll_data_lines'])->find($poll_id);
        $poll_lines = $poll->poll_lines;
        return view('polls.poll_data.create', compact('poll','poll_lines'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $poll_id)
    {
        $poll = Poll::find($poll_id);
        $poll_lines = $poll->poll_lines;
        foreach($poll_lines as $key=>$poll_line) {
            $request->validate([
                'line_'.$key => 'required',
            ]);
        }
        $poll_data = $poll->poll_data()->create([
            'poll_id' => $poll_id,
        ]);
        foreach($poll_lines as $key=>$poll_line) {
            if($poll_line->type == 'text'){
                $poll_data->poll_data_lines()->create([
                    'poll_line_id' => $poll_line->id,
                    'poll_data_id' => $poll_data->id,
                    'value' => $request->input('line_'.$key),
                ]);
            }
            if($poll_line->type == 'file'){
                // image upload
                $image = $request->file('line_'.$key);
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                $image->move($destinationPath, $name);
                $poll_data->poll_data_lines()->create([
                    'poll_line_id' => $poll_line->id,
                    'poll_data_id' => $poll_data->id,
                    'value' => $name,
                ]);
            }
        }
        return redirect()->route('poll_data.index', $poll->id)->with('success', 'Poll data created successfully.');
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
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($poll_id,$id)
    {
        $poll_data = PollData::find($id);
        $poll_data->delete();
        return redirect()->route('poll_data.index', $poll_data->poll_id)->with('success', 'Poll data deleted successfully.');
    }
}
