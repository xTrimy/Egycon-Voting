@extends('layouts.app')
@section('page')
cosplayers
@endsection
@section('title')
Cosplayers
@endsection
@php
$first_event_id = auth()->user()->events()->get()->pluck('id')->first();

$cosplayer_s = "Cosplayer";
$cosplayers_s = "Cosplayers";
$character_s = "Character";

if($first_event_id == 6){
    $character_s = "Song";
    $cosplayer_s = "Singer";
    $cosplayers_s = "Singers";
}
@endphp
@section('content')
        <main class="h-full pb-16 overflow-y-auto">
          <div class="container grid px-6 mx-auto">
            <form 
            @isset($event_id)
              action="{{ route('cosplayers.search_by_number_with_event', $event_id) }}"
            @else
            action="{{ route('cosplayers.search_by_number') }}"
            @endisset
            >
                  <div class="flex  my-4">
                    <button class=" w-14 rounded-l-md flex items-center justify-center dark:bg-slate-800 border-l border-t border-b border-gray-800"> <i class="las la-search text-xl text-purple-500 "></i> </button>
                    <input name="q" placeholder="Search cosplay no." type="text" class="w-full py-2 px-4  flex-1  dark:bg-slate-800 rounded-r-md dark:text-white border-t border-r border-b border-l-0 border-gray-800 ">
                  </div>
              </form>
              <div class="flex justify-between items-center">
              <h2
                class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
              >
                {{ $cosplayers_s }}
              </h2>
            @include('includes.alerts')
            
            <a href="{{ route('cosplayers.create') }}"><button class="bg-purple-600 text-white py-2 px-8 rounded-md">
                Add new {{ $cosplayer_s }}
            </button></a>
              </div>
            @isset($event_id)
              <a href="{{ route('cosplayers.export_with_event',$event_id) }}"><button class="bg-purple-600 text-white text-sm py-1 px-4 rounded-md">
                  Export All <i class="las la-file-excel text-sm text-white "></i>
              </button></a>
            @endif
            <div class="w-full overflow-hidden rounded-lg shadow-xs">
              <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap" id="images">
                  <thead>
                    <tr
                      class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
                    >
                      <th class="px-4 py-3">#</th>
                      <th class="px-4 py-3">Stage Name</th>
                      <th class="px-4 py-3">{{ $character_s }} </th>
                      {{-- <th class="px-4 py-3">From</th> --}}
                      <th class="px-4 py-3">Event</th>
                      <th class="px-4 py-3">Actions</th>
                      <th>Score</th>
                    </tr>
                  </thead>
                  <tbody
                    class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800"
                  >
                    @foreach ($cosplayers as $cosplayer)
                        
                    <tr class="text-gray-700 dark:text-gray-400">
                      <td class="px-4 py-3">
                        {{ $cosplayer->number }}
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <div>
                            <p class="font-semibold">{{ $cosplayer->name }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        {{ $cosplayer->character }}
                      </td>
                      {{-- <td class="px-4 py-3">
                        {{ $cosplayer->anime }}
                      </td> --}}
                      <td class="px-4 py-3">
                        {{ $cosplayer->event->name }}
                      </td>
                      <td>
                        <div class="flex items-center text-sm py-2">
                            <a 
                            href="{{ route('cosplayers.edit',$cosplayer) }}"
                            >
                            <button
                                class="flex items-center group disabled:hover:bg-inherit disabled:cursor-not-allowed  hover:bg-gray-300 dark:hover:bg-gray-600 justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                aria-label="Switch"
                            >
                                <i class="las la-pen text-xl group-disabled:text-gray-500 text-green-500"></i>
                            </button>
                            </a>
                            <a 
                            href="{{ route('cosplayers.show',$cosplayer) }}"
                            >
                            <button
                                class="flex items-center group disabled:hover:bg-inherit disabled:cursor-not-allowed  hover:bg-gray-300 dark:hover:bg-gray-600 justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                aria-label="Switch"
                            >
                                <i class="las la-eye text-xl group-disabled:text-gray-500 text-neutral-500"></i>
                            </button>
                            </a>
                            <button onclick="display_popup(this)"
                                data-title="Delete Cosplayer"
                                data-content="Are you sure you want to delete this cosplayer?"
                                data-action="{{ route('cosplayers.destroy',$cosplayer) }}"
                                data-method="DELETE"
                                class="flex items-center group disabled:hover:bg-inherit disabled:cursor-not-allowed  hover:bg-gray-300 dark:hover:bg-gray-600 justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                aria-label="Switch"
                            >
                                <i class="las la-trash text-xl group-disabled:text-gray-500 text-red-500"></i>
                            </button>
                            @if(in_array($cosplayer->id,$judge_votes))
                            <a href="{{ route('cosplayers.vote.create',$cosplayer) }}"><button class="bg-purple-600 text-white py-1 px-4 rounded-md">
                                Edit Vote
                            </button></a>
                            @else
                            <a href="{{ route('cosplayers.vote.create',$cosplayer) }}"><button class="bg-purple-600 text-white py-1 px-4 rounded-md">
                                Vote
                            </button></a>
                            @endif
                        </div>
                        
                      </td>
                      <td>
                        {{ $cosplayer->calculateJudgeScore(); }}
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            {{-- <div class="mt-4">
             {{$events->links('pagination::tailwind')}}
            </div> --}}
          </div>
        </main>

@endsection