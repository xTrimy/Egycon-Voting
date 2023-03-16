<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;

class PollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $polls = Poll::with('poll_lines')->latest()->paginate(15);
        return view('polls.index', compact('polls'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('polls.create');
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
            'name' => 'required',
            'poll_lines' => 'required|array',
            'poll_lines.*.type' => 'required',
            'is_disabled' => 'required|boolean',
        ]);

        $poll = Poll::create([
            'name' => $request->name,
            'is_disabled' => $request->is_disabled,
        ]);
        foreach($request->poll_lines as $poll_line) {
            $poll->poll_lines()->create([
                'type' => $poll_line['type'],
                'poll_id' => $poll->id,
            ]);
        }

        return redirect()->route('polls.index')->with('success', 'Poll created successfully.');
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
        $poll = Poll::with('poll_lines')->findOrFail($id);
        return view('polls.create', compact('poll'));
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
        $request->validate([
            'name' => 'required',
            'poll_lines' => 'required|array',
            'poll_lines.*.type' => 'required',
            'is_disabled' => 'required|boolean',
        ]);

        $poll = Poll::findOrFail($id);
        $poll->update([
            'name' => $request->name,
            'is_disabled' => $request->is_disabled,
        ]);
        $poll_lines = $request->poll_lines;
        $current_poll_lines = $poll->poll_lines;
        foreach($current_poll_lines as $current_poll_line) {
            $id = $current_poll_line->id;
            // dd($id, $poll_lines);
            if(!isset($poll_lines[$id])) {
                $current_poll_line->delete();
            }else{
                $current_poll_line->update([
                    'type' => $request->poll_lines[$id]['type'],
                ]);
            }
            // remove from array so we can create new ones
            if(isset($poll_lines[$id])){
                $poll_lines[$id] = null;
            }
        }
        foreach($poll_lines as $poll_line) {
            if(!$poll_line) continue;
            $poll->poll_lines()->create([
                'type' => $poll_line['type'],
                'poll_id' => $poll->id,
            ]);
        }

        return redirect()->route('polls.index')->with('success', 'Poll updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $poll = Poll::findOrFail($id);
        $poll->delete();
        return redirect()->route('polls.index')->with('success', 'Poll deleted successfully.');
    }

    public function votes($id)
    {
        $poll = Poll::with('poll_lines')->findOrFail($id);
        $poll_votes = $poll->poll_votes;
        return view('polls.votes', compact('poll', 'poll_votes'));
    }

    public function generate_qr($id){
        $qr_options = [
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 20,
            'imageBase64' => false,
            'imageTransparent' => false,
        ];
        $qr_options = new QROptions($qr_options);
        $result = (new QRCode($qr_options))->render(route('voting_page.index', $id));
        return response($result)->header('Content-Type', 'image/png');
    }
}
