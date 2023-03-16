@extends('layouts.app')
@section('page')
polls
@endsection
@section('title')
Polls
@endsection
@section('content')
        <main class="h-full pb-16 overflow-y-auto">
          <div class="container grid px-6 mx-auto">
              <div class="flex justify-between items-center">

            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              Polls
            </h2>
            <a href="{{ route('polls.create') }}"><button class="bg-purple-600 text-white py-2 px-8 rounded-md">
                Create new Poll
            </button></a>
              </div>
           
            <div class="w-full overflow-hidden rounded-lg shadow-xs">
              <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap" id="images">
                  <thead>
                    <tr
                      class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
                    >
                      <th class="px-4 py-3">Poll Name</th>
                      <th class="px-4 py-3">Poll Lines</th>
                      <th class="px-4 py-3">Actions</th>
                    </tr>
                  </thead>
                  <tbody
                    class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800"
                  >
                    @foreach ($polls as $poll)
                        
                    <tr class="text-gray-700 dark:text-gray-400">
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <div>
                            <p class="font-semibold">{{ $poll->name }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <div>
                            <p class="font-semibold">{{ implode(', ',$poll->poll_lines->pluck('type')->toArray()) }}</p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="flex items-center text-sm py-2">
                            <a 
                            href="{{ route('polls.edit',$poll) }}"
                            >
                            <button
                                class="flex items-center group disabled:hover:bg-inherit disabled:cursor-not-allowed  hover:bg-gray-300 dark:hover:bg-gray-600 justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                aria-label="Switch"
                            >
                                <i class="las la-pen text-xl group-disabled:text-gray-500 text-green-500"></i>
                            </button>
                            </a>
                            <button onclick="display_popup(this)"
                                data-title="Delete Poll"
                                data-content="Are you sure you want to delete this poll?"
                                data-action="{{ route('polls.destroy',$poll) }}"
                                data-method="DELETE"
                                class="flex items-center group disabled:hover:bg-inherit disabled:cursor-not-allowed  hover:bg-gray-300 dark:hover:bg-gray-600 justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                aria-label="Switch"
                            >
                                <i class="las la-trash text-xl group-disabled:text-gray-500 text-red-500"></i>
                            </button>
                            <a href="{{ route('poll_data.index',['poll'=>$poll]) }}"><button class="bg-purple-600 text-white py-1 px-4 mx-2 rounded-md">
                                Data
                            </button></a>
                            <a href="{{ route('polls.votes',['poll'=>$poll]) }}"><button class="bg-purple-600 text-white py-1 px-4  mx-2 rounded-md">
                                Votes
                            </button></a>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            <div class="mt-4">
             {{$polls->links('pagination::tailwind')}}
            </div>
          </div>
        </main>

@endsection