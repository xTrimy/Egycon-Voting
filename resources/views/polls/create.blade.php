@extends('layouts.app')
@section('page')
poll
@endsection
@section('title')
Add Poll
@endsection
@section('content')

<main class="h-full pb-16 overflow-y-auto">
          <div class="container px-6 mx-auto grid">
            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              @isset($poll)
                Edit Poll - {{ $poll->name }}
                @else
                Create New Poll
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
            action="{{ isset($poll) ? route('polls.update', $poll->id) : route('polls.store') }}"
              class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800"
            >

            <span class="text-red-500 text-sm">* Is required</span>
              @isset($poll)
              @method('PUT')
              <input type="hidden" name="poll_edit_id" value="{{ $poll->id }}">
              @endisset
            @csrf
            @if($errors->any())
                {!! implode('', $errors->all('<div class="text-red-500">:message</div>')) !!}
            @endif
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-signature text-xl"></i>
                Poll Name <span class="text-red-500">*</span>
                </span>
                <input
                value="{{ old('name')??$poll->name??"" }}"
                type="text"
                name="name"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="EGYcon X"
                />
              </label>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-signature text-xl"></i>
                Is Disabled? <span class="text-red-500">*</span>
                </span>
                <select name="is_disabled"
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                >
                  <option {{ old('is_disabled')??$poll->is_disabled??"" == 0 ? 'selected' : '' }} value="0">No</option>
                  <option {{ old('is_disabled')??$poll->is_disabled??"" == 1 ? 'selected' : '' }} value="1">Yes</option>
                </select>
                
              </label>
              @php
                $poll_types = [
                  'text' => 'Text',
                  'file' => 'Image',
                ];
              @endphp
              <div id="poll_lines">
                <div class="flex flex-col mt-4" style="display:none" id="poll_line_original">
                  <label class="block text-sm">
                    <span class="text-gray-700 dark:text-gray-400">
                    <i class="las la-signature text-xl"></i>
                    Poll Line <span class="text-red-500">*</span>
                    </span>
                    <select
                    data-name="poll_lines[][type]"
                    required
                      class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    >
                      @foreach ($poll_types as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                      @endforeach
                    </select>
                  </label>
                  <button onclick="this.parentElement.remove()" type="button" class="table items-center mt-4 justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg active:bg-red-600 hover:bg-red-700 focus:outline-none focus:shadow-outline-purple">
                    Remove
                    <i class="las la-trash-alt"></i>
                  </button>
                </div>
                @isset($poll)
                @foreach ($poll->poll_lines as $poll_line)
                <div class="flex flex-col mt-4">
                  <label class="block text-sm">
                    <span class="text-gray-700 dark:text-gray-400">
                    <i class="las la-signature text-xl"></i>
                    Poll Line <span class="text-red-500">*</span>
                    </span>
                    <select
                    name="poll_lines[{{ $poll_line->id }}][type]"
                    required
                      class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    >
                      @foreach ($poll_types as $key => $value)
                        <option value="{{ $key }}" {{ $poll_line->type == $key ? 'selected' : '' }}>{{ $value }}</option>
                      @endforeach
                    </select>

                  </label>
                  <button onclick="this.parentElement.remove()" type="button" class="table items-center mt-4 justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg active:bg-red-600 hover:bg-red-700 focus:outline-none focus:shadow-outline-purple">
                    Remove
                    <i class="las la-trash-alt"></i>
                  </button>
                </div>
                @endforeach
                @endisset
                <button id="add_poll_button" type="button" class="table items-center mt-4 justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-600 border border-transparent rounded-lg active:bg-green-600 hover:bg-green-700 focus:outline-none focus:shadow-outline-purple">
                  Add Poll Line 
                  <i class="las la-plus"></i>
                </button>
              </div>
              <script>
                var poll_line_original = document.getElementById('poll_line_original');
                var poll_lines = document.getElementById('poll_lines');
                var poll_line_original_html = poll_line_original.innerHTML;
                poll_line_original.remove();
                var poll_line_original_html = poll_line_original_html.replace('style="display:none"', '');
                var poll_line_original_html = poll_line_original_html.replace('id="poll_line_original"', '');
                var poll_line_original_html = poll_line_original_html.replace('data-name="', 'name="');
                var add_poll_button = document.getElementById('add_poll_button');
                add_poll_button.addEventListener('click', function(){
                  var poll_line = document.createElement('div');
                  poll_line.classList.add('flex');
                  poll_line.classList.add('flex-col');
                  poll_line.classList.add('mt-4');
                  poll_line.innerHTML = poll_line_original_html;
                  poll_lines.appendChild(poll_line);
                });
              </script>
                
              <button type="submit" class="table items-center mt-4 justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
              @isset($poll)
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
