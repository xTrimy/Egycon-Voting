@extends('layouts.app')
@section('page')
polls
@endsection
@section('title')
Poll Votes - {{ $poll->name }}
@endsection
@section('content')
        <main class="h-full pb-16 overflow-y-auto">
          <div class="container grid px-6 mx-auto">
              <div class="flex justify-between items-center">

            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              Poll Votes - {{ $poll->name }}
            </h2>
            
              </div>
           
            <div class="w-full overflow-hidden rounded-lg shadow-xs">
              <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap" id="images">
                  <thead>
                    <tr
                      class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
                    >
                      <th class="px-4 py-3">IP Address</th>
                      <th class="px-4 py-3">Vote Time</th>
                      <th class="px-4 py-3">Rating</th>
                      <th class="px-4 py-3">Data Ref.</th>
                    </tr>
                  </thead>
                  <tbody
                    class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800"
                  >
                    @foreach ($poll_votes as $vote)
                        
                    <tr class="text-gray-700 dark:text-gray-400">
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <div>
                            <p class="font-semibold">{{ $vote->ip }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <div>
                            <p class="font-semibold">{{ $vote->created_at->format('Y/m/d H:i') }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <div>
                            <p class="font-semibold">{{ $vote->rating }}/5</p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <div>
                            <p class="font-semibold">{{ $vote->poll_data->poll_data_lines->first()->value }}</p>
                          </div>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
          </div>
        </main>

@endsection