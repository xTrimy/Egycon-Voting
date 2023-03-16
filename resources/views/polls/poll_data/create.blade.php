@extends('layouts.app')
@section('page')
polls
@endsection
@section('title')
Add Poll Data
@endsection
@section('content')

<main class="h-full pb-16 overflow-y-auto">
          <div class="container px-6 mx-auto grid">
            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              @isset($poll_data)
                Edit Poll Data - {{ $poll->name }}
                @else
                Create New Poll Data - {{ $poll->name }}
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
            action="{{ isset($poll_data) ? route('poll_data.update',['poll'=>$poll,'poll_datum'=>$poll_data]) : route('poll_data.store',$poll) }}"
              class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800"
            >

            <span class="text-red-500 text-sm">* Is required</span>
              @isset($poll_data)
              @method('PUT')
              <input type="hidden" name="poll_data_edit_id" value="{{ $poll_data->id }}">
              @endisset
            @csrf
            @if($errors->any())
                {!! implode('', $errors->all('<div class="text-red-500">:message</div>')) !!}
            @endif
              @foreach ($poll_lines as $key=>$poll_line)
                <label class="block text-sm">
                  <span class="text-gray-700 dark:text-gray-400">
                  <i class="las la-signature text-xl"></i>
                  Line {{ $key }} <span class="text-red-500">*</span>
                  </span>
                  <input type="hidden" name="line_type_{{ $key }}" value="{{ $poll_line->type }}">
                  <input
                  value="{{ old('line_'.$key)??"" }}"
                  type="{{ $poll_line->type }}"
                  @if ($poll_line->type == 'file')
                    accept="image/*"
                  @endif
                  name="line_{{ $key }}"
                      required
                    class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  />
                </label>
              @endforeach
                
              <button type="submit" class="table items-center mt-4 justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
              @isset($poll_data)
              Update Poll
              @else
              Create Poll
              @endisset
              <span class="ml-2" aria-hidden="true">
                  <i class='las la-arrow-right'></i>
              </span>
            </button>
        </form>

          </div>
        </main>
@endsection
