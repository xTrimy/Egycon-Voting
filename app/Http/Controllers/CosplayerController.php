<?php

namespace App\Http\Controllers;

use App\Exports\CosplayersExport;
use App\Exports\CosplayersWithEvent;
use App\Http\Controllers\API\CosplayerController as APICosplayerController;
use App\Http\Controllers\API\EventController;
use App\Imports\CosplayersImport;
use App\Models\Cosplayer;
use App\Models\CosplayerVote;
use App\Models\Event;
use App\Models\Poll;
use App\Models\PollData;
use App\Models\PollDataLine;
use App\Models\PollLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class CosplayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all cosplayers
        $cosplayers = (new APICosplayerController)->index();
        $judge_votes = CosplayerVote::where('user_id', auth()->user()->id)->pluck('cosplayer_id')->toArray();

        return view('cosplayers.index', compact('cosplayers', 'judge_votes'));
    }

    public function index_with_event_id($event_id)
    {
        // get all cosplayers for event
        $event = Event::findOrFail($event_id);
        $cosplayers = Cosplayer::where('event_id', $event_id)->get();
        $judge_votes = CosplayerVote::where('user_id', auth()->user()->id)->pluck('cosplayer_id')->toArray();

        return view('cosplayers.index', compact('cosplayers','judge_votes', 'event_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $events = (new EventController)->index();
        return view('cosplayers.create', compact('events'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // store cosplayer
        $cosplayer = (new APICosplayerController)->store($request);
        return redirect()->route('cosplayers.index');
    }

    public function bulk_add()
    {
        $events = (new EventController)->index();
        return view('cosplayers.bulk-add', compact('events'));
    }

    public function bulk_add_store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'sheet' => 'nullable|file|mimes:csv,xlsx,xls',
        ]);

        $file = $request->file('sheet');
        $event_id = $request->get('event_id');

       (new CosplayersImport($event_id))->import($file);
        return redirect()->route('cosplayers.index');
    }

    public function bulk_upload_references()
    {
        $events = (new EventController)->index();
        return view('cosplayers.references.bulk-upload', compact('events'));
    }

    public function bulk_upload_references_store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);
        $files = $request->file('images');
        $event_id = $request->get('event_id');
        foreach($files as $file){
            $cosplayer_number = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            dd($cosplayer_number);
            $cosplayer = Cosplayer::where('event_id', $event_id)->where('number', $cosplayer_number)->first();
            if($cosplayer){
                $image = Image::read($file)
                    ->scaleDown(600, 600);
                $path = "references/{$event_id}/{$cosplayer_number}.jpg";
                Storage::disk('public')->put($path, $image->toJpeg(80));
                $cosplayer->references()->create(['image' => $path]);
            }
        }

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
        // get cosplayer
        $cosplayer = (new APICosplayerController)->show($id);
        $judge_vote = CosplayerVote::where('cosplayer_id', $id)->where('user_id', auth()->user()->id)->first();
        if(!$cosplayer->resource)
            abort(404);
        return view('cosplayers.show', ['cosplayer'=>$cosplayer, 'judge_vote'=>$judge_vote]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $events = (new EventController)->index();
        $cosplayer = (new APICosplayerController)->show($id);
        if(!$cosplayer->resource)
            abort(404);
        return view('cosplayers.create', compact('cosplayer', 'events'));
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
        // update cosplayer
        $cosplayer = (new APICosplayerController)->update($request, $id);
        return redirect()->route('cosplayers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete cosplayer
        $cosplayer = (new APICosplayerController)->destroy($id);
        return redirect()->route('cosplayers.index');
    }

    /**
     * Show the form for adding images to the cosplayer.
     */
    public function addImagesView($id)
    {
        // show form for adding images to cosplayer
        $cosplayer = (new APICosplayerController)->show($id);
        if(!$cosplayer->resource)
            abort(404);
        return view('cosplayers.images.create', compact('cosplayer'));
    }

    /**
     * Add new images to the cosplayer.
     *
     * @return \Illuminate\Http\Response
     */
    public function addImages(Request $request, $id, $collection = 'images')
    {
        // add images to cosplayer
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        foreach($request->file('images') as $image)
        {
            // don't use the API cosplayer controller here
            $cosplayer = Cosplayer::find($id);
            // resize image if more than 1920px wide

            $test = $cosplayer->addMedia($image)->withResponsiveImages()->toMediaCollection($collection);
        }
        return redirect()->route('cosplayers.index');
    }

    /**
     * Remove the specified image from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeImage($id, $imageId, $collection = 'images')
    {
        // remove image from cosplayer
        $cosplayer = Cosplayer::find($id);
        $cosplayer->getMedia($collection)->find($imageId)->delete();
        return redirect()->route('cosplayers.index');
    }
    /**
     * Show the form for adding references to the cosplayer.
     */

    public function addReferencesView($id)
    {
        // show form for adding references to cosplayer
        $cosplayer = (new APICosplayerController)->show($id);
        if(!$cosplayer->resource)
            abort(404);
        return view('cosplayers.references.create', compact('cosplayer'));
    }

    /**
     * Add new references to the cosplayer.
     *
     * @return \Illuminate\Http\Response
     */
    public function addReferences(Request $request, $id, $collection = 'references'){
        return $this->addImages($request, $id, $collection);
    }

    /**
     * Remove the specified reference from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeReference($id, $imageId, $collection = 'references')
    {
        return $this->removeImage($id, $imageId, $collection);
    }

    public function search_cosplayer_by_number(Request $request)
    {
        $q = $request->get('q');
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $cosplayers = $user->events()->with('cosplayers')->get()->pluck('cosplayers')->flatten()->where('number', $q);
        $judge_votes = CosplayerVote::where('user_id', auth()->user()->id)->pluck('cosplayer_id')->toArray();
        return view('cosplayers.index', compact('cosplayers', 'judge_votes'));
    }

    public function search_cosplayer_by_number_with_event_id(Request $request, $event_id)
    {
        $q = $request->get('q');
        $cosplayers = Cosplayer::where('event_id', $event_id)->where('number', $q)->get();
        $judge_votes = CosplayerVote::where('user_id', auth()->user()->id)->pluck('cosplayer_id')->toArray();
        return view('cosplayers.index', compact('cosplayers', 'judge_votes', 'event_id'));
    }



    private function getTopCosplayersByJudgeScore($event_id){
        $cosplayers = Cosplayer::where('event_id', $event_id)->get();
        $top_cosplayers = [];
        $max_cosplayers = 10;
        foreach($cosplayers as $cosplayer){
            $cosplayer->score = $cosplayer->calculateJudgeScore();
            if($cosplayer->score > 0){
                $top_cosplayers[] = $cosplayer;
            }
        }
        usort($top_cosplayers, function($a, $b) {
            return $b->score <=> $a->score;
        });
        $top_cosplayers = array_slice($top_cosplayers, 0, $max_cosplayers);
        return $top_cosplayers;
    }

    public function top_cosplayers($event_id){
        $top_cosplayers = $this->getTopCosplayersByJudgeScore($event_id);
        return $top_cosplayers;
    }

    public function create_poll_from_top_cosplayers($event_id){
        $top_cosplayers = $this->getTopCosplayersByJudgeScore($event_id);
        $poll = new Poll();
        $poll->name = 'Top 10 Cosplayers';
        $poll->save();
        // create poll lines 
        $poll_lines_types = [
            'text',
            'file',
            'text',
            'text',
            'text',
        ];
        foreach($poll_lines_types as $type){
            $poll_line = new PollLine();
            $poll_line->poll_id = $poll->id;
            $poll_line->type = $type;
            $poll_line->save();
        }

        $cosplayers_data = [
            'number',
            '',
            'name',
            'anime',
            'character',
        ];
        // add cosplayers as poll_data to poll
        foreach($top_cosplayers as $cosplayer){
            $poll_data = new PollData();
            $poll_lines = $poll->poll_lines;
            $poll_data->poll_id = $poll->id;
            $poll_data->save();
            foreach($poll_lines as $key => $line){
                $poll_data_line = new PollDataLine();
                $poll_data_line->poll_data_id = $poll_data->id;
                $poll_data_line->poll_line_id = $line->id;
                if($line->type == 'text'){
                    $poll_data_line->value = $cosplayer->{$cosplayers_data[$key]};
                }
                if($line->type == 'file'){
                    $poll_data_line->value = "generated/cosplayers/{$cosplayer->number}.jpg";
                }
                $poll_data_line->save();
            }
        }
        return redirect()->route('polls.index');
    }

    public function export_cosplayers(){
        return Excel::download(new CosplayersExport, 'cosplayers.xlsx');
    }

    public function export_cosplayers_with_event($evnet_id)
    {
        return Excel::download(new CosplayersWithEvent($evnet_id), 'cosplayers.xlsx');
        
    }
}
