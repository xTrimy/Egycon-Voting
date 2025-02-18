@extends('layouts.app')
@section('page')
cosplayers
@endsection
@section('title')
Cosplayer Details - {{ $cosplayer->name }}
@endsection
@section('content')
        <main class="h-full pb-16 overflow-y-auto">
          <div class="container grid px-6 mx-auto">
              <div class="flex justify-between items-center">

            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              Cosplayer Details - {{ $cosplayer->name }}
            </h2>
            </div>
           
            <div class="w-full overflow-hidden rounded-lg shadow-xs text-gray-700 dark:text-gray-200">
              <div class="w-full overflow-x-auto">
                <table>
                    <tr>
                        <th class="text-left pr-4">Stage Name</th>
                        <td>{{ $cosplayer->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4">Character</th>
                        <td>{{ $cosplayer->character }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4">From</th>
                        <td>{{ $cosplayer->anime }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4">Event</th>
                        <td>{{ $cosplayer->event->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4">Number</th>
                        <td>{{ $cosplayer->number }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4">Judges Score</th>
                        <td>{{ $cosplayer->calculateJudgeScore() }}% 
                            <span class="text-xs text-gray-500">({{ $cosplayer->votes->count() }} {{ Str::plural("Vote", $cosplayer->votes->count()) }})</span>
                        </td>
                    </tr>
                    @if ($judge_vote)
                        <tr>
                            <th class="text-left pr-4">Your Vote</th>
                            <td> {{ $judge_vote->vote }} </td>
                        </tr>
                        <tr>
                            <th class="text-left pr-4">Your Comment</th>
                            <td> {{ $judge_vote->comment??"None" }} </td>
                        </tr>
                    @endif
                    
                   
                </table>
                {{-- @php
                    $images = $cosplayer->getMedia('images');
                @endphp
                @if(count($images) > 0)
                    <h2 class="mt-4 font-bold text-xl">Images</h2>
                    @foreach ($images as $image)
                    <div class="flex flex-col items-center justify-center mt-4 max-w-xl ">
                        {{ $image }}
                    </div>
                    @endforeach
                @endif --}}
               @php
                    $images = $cosplayer->references;
                @endphp
                @if(count($images) > 0)
                    <h2 class="mt-4 font-bold text-xl">References</h2>
                    @foreach ($images as $image)
                    <div class="flex flex-col items-center justify-center mt-4 max-w-xl ">
                        <img src="{{ asset('storage/'.$image->image) }}">
                    </div>
                    @endforeach
                @endif
              </div>
              <div>
                <a href="{{ route('cosplayers.edit', $cosplayer->id) }}"><button class="bg-purple-600 text-white py-2 px-8 rounded-md">
                    Edit
                </button></a>
                <button
                onclick="display_popup(this)"
                data-title="Delete Cosplayer"
                data-content="Are you sure you want to delete this cosplayer?"
                data-action="{{ route('cosplayers.destroy', $cosplayer->id) }}"
                data-method="DELETE"
                class="bg-red-600 text-white py-2 px-8 rounded-md">
                    Delete
                </button>
            </div>
            {{-- <div class="mt-4">
             {{$events->links('pagination::tailwind')}}
            </div> --}}
          </div>
        </main>

@endsection