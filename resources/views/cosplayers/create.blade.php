@extends('layouts.app')
@section('page')
cosplayers
@endsection
@section('title')
Add Cosplayer
@endsection
@section('content')

<main class="h-full pb-16 overflow-y-auto">
          <div class="container px-6 mx-auto grid">
            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              @isset($cosplayer)
                Edit Cosplayer - {{ $cosplayer->name }}
                @else
                Add New Cosplayer
              @endisset

            </h2>
            
            @if(Session::has('success'))
            <div
              class="flex items-center justify-between px-4 p-2 mb-8 text-sm font-semibold text-green-600 bg-green-100 rounded-lg focus:outline-none focus:shadow-outline-purple"
            >
              <div class="flex items-center">
                <i class="fas fa-check mr-2"></i>
                <span>{{ Session::get('success') }}</span>
              </div>
            </div>
            @endif
            @if(Session::has('error'))
            <div
              class="flex items-center justify-between px-4 p-2 mb-8 text-sm font-semibold text-red-600 bg-red-100 rounded-lg focus:outline-none focus:shadow-outline-purple"
            >
              <div class="flex items-center">
                <i class="fas fa-check mr-2"></i>
                <span>{{ Session::get('error') }}</span>
              </div>
            </div>
            @endif
            <!-- General elements -->
            <form method="POST" enctype="multipart/form-data"
            action="{{ isset($cosplayer) ? route('cosplayers.update', $cosplayer->id) : route('cosplayers.store') }}"
              class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800"
            >

            <span class="text-red-500 text-sm">* Is required</span>
              @isset($cosplayer)
              @method('PUT')
              <input type="hidden" name="event_edit_id" value="{{ $cosplayer->id }}">
              @endisset
            @csrf
            @if($errors->any())
                {!! implode('', $errors->all('<div class="text-red-500">:message</div>')) !!}
            @endif
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-signature text-xl"></i>
                Cosplayer Name <span class="text-red-500">*</span>
                </span>
                <input
                value="{{ old('name')??$cosplayer->name??"" }}"
                type="text"
                name="name"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="John Doe"
                />
              </label>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-signature text-xl"></i>
                Stage Name <span class="text-red-500">*</span>
                </span>
                <input
                value="{{ old('stage_name')??$cosplayer->stage_name??"" }}"
                type="text"
                name="stage_name"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="John Doe"
                />
              </label>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-theater-masks text-xl"></i>
                Character <span class="text-red-500">*</span>
                </span>
                 <input
                value="{{ old('character')??$cosplayer->character??"" }}"
                type="text"
                name="character"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="Batman"
                />
              </label>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-film text-xl"></i>
                Anime/Movie/Game <span class="text-red-500">*</span>
                </span>
                <input
                value="{{ old('character')??$cosplayer->character??"" }}"
                type="text"
                name="anime"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="Batman"
                />
              </label>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-sort-numeric-up-alt text-xl"></i>
                Cosplayer Number <span class="text-red-500">*</span>
                </span>
                <input
                value="{{ old('number')??$cosplayer->number??"" }}"
                type="text"
                name="number"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="100"
                />
              </label>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-calendar text-xl"></i>
                Event <span class="text-red-500">*</span>
                </span>
                <select
                value="{{ old('character')??$cosplayer->character??"" }}"
                name="event_id"
                  required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                >
                <option value="" selected disabled>Select Event</option>
                @foreach($events as $event)
                  <option {{ (old('event_id')==$event->id || (($cosplayer->event_id??0)==$event->id && old('event_id') == null))?'selected':"" }} value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach

                </select>
              </label>
             
                 
              <button type="submit" class="table items-center mt-4 justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
              @isset($cosplayer)
              Update Cosplayer
              @else
              Add Cosplayer
              @endisset
              <span class="ml-2" aria-hidden="true">
                  <i class='las la-arrow-right'></i>
              </span>
            </button>
        </form>

          </div>
        </main>
@endsection
