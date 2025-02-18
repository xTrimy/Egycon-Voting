<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CosplayerResource;
use App\Models\Cosplayer;
use App\Models\User;
use Illuminate\Http\Request;

class CosplayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        // get cosplayers in events that user has access to
        /** @var User $user */
        $user = auth()->user();
        $cosplayers = $user->events()->with('cosplayers')->get()->pluck('cosplayers')->flatten();
        return CosplayerResource::collection($cosplayers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\CosplayerResource
     */
    public function store(Request $request): CosplayerResource
    {
        $request->validate([
            'name' => 'required|string',
            'event_id' => 'required|exists:events,id',
            'character' => 'required|string',
            'anime' => 'required|string',
            'number' => 'required|integer',
            'stage_name' => 'nullable|string',
        ]);
        $data = $request->only(['name', 'event_id', 'character', 'anime', 'number', 'stage_name']);
        $cosplayer = Cosplayer::create($data);
        return new CosplayerResource($cosplayer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id): CosplayerResource
    {
        
        $cosplayer = new CosplayerResource(Cosplayer::find($id));
        return $cosplayer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id): CosplayerResource
    {
        $request->validate([
            'name' => 'nullable',
            'event_id' => 'nullable|exists:events,id',
            'character' => 'nullable',
            'anime' => 'nullable',
            'number' => 'nullable',
            'stage_name' => 'nullable',
        ]);
        foreach ($request->only(['name', 'event_id', 'character', 'anime', 'number', 'stage_name']) as $key => $value) {
            if ($value == null) {
                unset($request[$key]);
            }
        }
        $cosplayer = Cosplayer::find($id);
        $cosplayer->update($request->only(['name', 'event_id', 'character', 'anime', 'number', 'stage_name']));
        return new CosplayerResource($cosplayer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $cosplayer = Cosplayer::find($id);
        $cosplayer->delete();
        return response()->json(null, 204);
    }

    public function search_cosplayer_by_number($number): CosplayerResource
    {
        $cosplayers = Cosplayer::where('number', $number)->first();
        return new CosplayerResource($cosplayers);
        
    }
    public function search_cosplayer_by_number_with_event_id($event_id, $number): CosplayerResource
    {
        $cosplayers = Cosplayer::where('number', $number)->where('event_id', $event_id)->first();
        return new CosplayerResource($cosplayers);
        
    }

    public function get_all_by_event_id($event_id): CosplayerResource
    {
        $cosplayers = Cosplayer::where('event_id', $event_id)->get();
        return new CosplayerResource($cosplayers);
    }
}
