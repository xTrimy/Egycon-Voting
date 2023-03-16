<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CosplayerResource;
use App\Models\Cosplayer;
use Illuminate\Http\Request;

class CosplayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CosplayerResource::collection(Cosplayer::all());
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
            'name' => 'required|string',
            'event_id' => 'required|exists:events,id',
            'character' => 'required|string',
            'anime' => 'required|string',
            'number' => 'required|integer',
        ]);
        $data = $request->only(['name', 'event_id', 'character', 'anime', 'number']);
        $cosplayer = Cosplayer::create($data);
        return new CosplayerResource($cosplayer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $cosplayer = new CosplayerResource(Cosplayer::find($id));
        return $cosplayer;
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
            'name' => 'nullable',
            'event_id' => 'nullable|exists:events,id',
            'character' => 'nullable',
            'anime' => 'nullable',
            'number' => 'nullable',
        ]);
        foreach ($request->only(['name', 'event_id', 'character', 'anime', 'number']) as $key => $value) {
            if ($value == null) {
                unset($request[$key]);
            }
        }
        $cosplayer = Cosplayer::find($id);
        $cosplayer->update($request->only(['name', 'event_id', 'character', 'anime', 'number']));
        return new CosplayerResource($cosplayer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cosplayer = Cosplayer::find($id);
        $cosplayer->delete();
        return response()->json(null, 204);
    }

    public function search_cosplayer_by_number($number)
    {
        $cosplayers = Cosplayer::where('number', $number)->first();
        return new CosplayerResource($cosplayers);
        
    }
}
