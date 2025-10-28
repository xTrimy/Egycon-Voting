@extends('layouts.app')
@section('page')
cosplayers
@endsection
@section('title')
Cosplayer Details - {{ $cosplayer->name }}
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
              <div class="flex justify-between items-center">

            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              {{$cosplayer_s}} Details - {{ $cosplayer->name }}
            </h2>
            </div>

            <!-- Voting Status Alert -->
            <div class="mb-4">
              @if($cosplayer->event->isJudgeVotingEnabled())
                <div class="flex items-center px-4 py-3 mb-4 text-sm font-semibold text-green-600 bg-green-100 rounded-lg">
                  <i class="fas fa-check-circle mr-2"></i>
                  <span>{{ $cosplayer->event->getVotingStatusMessage() }}</span>
                </div>
              @else
                <div class="flex items-center px-4 py-3 mb-4 text-sm font-semibold text-red-600 bg-red-100 rounded-lg">
                  <i class="fas fa-exclamation-triangle mr-2"></i>
                  <span>{{ $cosplayer->event->getVotingStatusMessage() }}</span>
                </div>
              @endif
            </div>

            <div class="w-full overflow-hidden rounded-lg shadow-xs text-gray-700 dark:text-gray-200">
              <div class="w-full overflow-x-auto">
                <table>
                    <tr>
                        <th class="text-left pr-4">{{$cosplayer_s}} Name</th>
                        <td>{{ $cosplayer->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4">{{$character_s}}</th>
                        <td>{{ $cosplayer->character }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4">From</th>
                        <td>{{ $cosplayer->anime }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4">Number</th>
                        <td>{{ $cosplayer->number }}</td>
                    </tr>
                    @if($cosplayer->getAllCustomData())
                        @foreach($cosplayer->getAllCustomData() as $key => $value)
                            <tr>
                                <th class="text-left pr-4">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
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
                        <img class="viewer-image" src="{{ asset('storage/'.$image->image) }}">
                    </div>
                    @endforeach
                @endif
              </div>

              @if($cosplayer->event->isJudgeVotingEnabled())
                <form action="" method="POST">
                  @csrf
                  <input type="hidden" name="id" value="{{ $cosplayer->id }}">
                  <div class="flex flex-col mt-4 max-w-xl text-left ">
                      <label for="score" class="text-gray-700 text-left dark:text-gray-200">Score
                          <span class="text-sm text-gray-500 dark:text-gray-400"> (0-100)</span>
                          <span class="text-sm text-red-500 dark:text-red-400">*</span>
                      </label>
                      <input type="number"
                      oninput="if(value<0) value=0;if(value>100) value=100;"
                      name="score" id="score" class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:border-blue-500 focus:outline-none focus:shadow-outline-blue" min="0" max="100" step="1" value="{{ old('score')??@$vote->vote??"" }}">
                      @error('score')
                          <div class="text-red-500 mt-2 text-sm">
                              {{ $message }}
                          </div>
                      @enderror
                  </div>
                  <div class="flex flex-col mt-4 max-w-xl text-left ">
                      <label for="comments" class="text-gray-700 text-left dark:text-gray-200">Comments</label>
                      <textarea name="comments" id="comments" class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:border-blue-500 focus:outline-none focus:shadow-outline-blue">{{ old('comments')??@$vote->comments??"" }}</textarea>
                      @error('comments')
                          <div class="text-red-500 mt-2 text-sm">
                              {{ $message }}
                          </div>
                      @enderror
                  </div>

                  <div class="flex flex-col mt-4 max-w-xl text-left ">
                      <button type="submit" class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                          {{ isset($vote) && $vote ? 'Update Vote' : 'Submit Vote' }}
                      </button>
                  </div>
                </form>
              @else
                <div class="flex flex-col mt-4 max-w-xl text-left">
                  <div class="px-4 py-6 text-center text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <i class="fas fa-lock text-4xl mb-2"></i>
                    <p class="text-lg font-medium">Voting is Currently Disabled</p>
                    <p class="text-sm mt-2">{{ $cosplayer->event->getVotingStatusMessage() }}</p>
                  </div>
                </div>
              @endif

          </div>
        </main>

@endsection
