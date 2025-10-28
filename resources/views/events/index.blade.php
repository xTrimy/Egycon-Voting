@extends('layouts.app')
@section('page')
events
@endsection
@section('title')
Events
@endsection
@section('content')
        <main class="h-full pb-16 overflow-y-auto">
          <div class="container grid px-6 mx-auto">
              <div class="flex justify-between items-center">

            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              Events
            </h2>
            <a href="{{ route('events.create') }}"><button class="bg-purple-600 text-white py-2 px-8 rounded-md">
                Create new Event
            </button></a>
              </div>

            <div class="w-full overflow-hidden rounded-lg shadow-xs">
              <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap" id="images">
                  <thead>
                    <tr
                      class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
                    >
                      <th class="px-4 py-3">Event Name</th>
                      <th class="px-4 py-3">Voting Status</th>
                      <th class="px-4 py-3">Actions</th>
                      <th class="px-4 py-3">Cosplayers</th>
                    </tr>
                  </thead>
                  <tbody
                    class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800"
                  >
                    @foreach ($events as $event)

                    <tr class="text-gray-700 dark:text-gray-400">
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <div>
                            <p class="font-semibold">{{ $event->name }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <div class="flex items-center">
                            @if($event->isJudgeVotingEnabled())
                              <i class="fas fa-check-circle text-green-500 mr-2"></i>
                              <span class="text-green-600 dark:text-green-400 font-medium">Enabled</span>
                            @else
                              <i class="fas fa-times-circle text-red-500 mr-2"></i>
                              <span class="text-red-600 dark:text-red-400 font-medium">Disabled</span>
                            @endif
                          </div>
                          @if($event->voting_starts_at || $event->voting_ends_at)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                              @if($event->voting_starts_at && $event->voting_ends_at)
                                {{ $event->voting_starts_at->format('M j') }} - {{ $event->voting_ends_at->format('M j, Y') }}
                              @elseif($event->voting_starts_at)
                                Starts: {{ $event->voting_starts_at->format('M j, Y g:i A') }}
                              @elseif($event->voting_ends_at)
                                Ends: {{ $event->voting_ends_at->format('M j, Y g:i A') }}
                              @endif
                            </div>
                          @endif
                        </div>
                      </td>
                      <td>
                        <div class="flex items-center text-sm py-2">
                            <a
                            href="{{ route('events.edit',$event) }}"
                            >
                            <button
                                class="flex items-center group disabled:hover:bg-inherit disabled:cursor-not-allowed  hover:bg-gray-300 dark:hover:bg-gray-600 justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                aria-label="Edit Event"
                            >
                                <i class="las la-pen text-xl group-disabled:text-gray-500 text-green-500"></i>
                            </button>
                            </a>
                            <a
                            href="{{ route('events.voting.settings', $event) }}"
                            title="Voting Settings"
                            >
                            <button
                                class="flex items-center group disabled:hover:bg-inherit disabled:cursor-not-allowed  hover:bg-gray-300 dark:hover:bg-gray-600 justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                aria-label="Voting Settings"
                            >
                                <i class="fas fa-vote-yea text-xl group-disabled:text-gray-500 text-blue-500"></i>
                            </button>
                            </a>
                            <button onclick="display_popup(this)"
                                data-title="Delete Event"
                                data-content="Are you sure you want to delete this event?"
                                data-action="{{ route('events.destroy',$event) }}"
                                data-method="DELETE"
                                class="flex items-center group disabled:hover:bg-inherit disabled:cursor-not-allowed  hover:bg-gray-300 dark:hover:bg-gray-600 justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                aria-label="Delete Event"
                            >
                                <i class="las la-trash text-xl group-disabled:text-gray-500 text-red-500"></i>
                            </button>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <a href="{{ route('cosplayers.index_with_event', $event->id) }}" class="underline">
                            <div>
                              <p class="font-semibold">{{ $event->cosplayers->count() }}</p>
                            </div>
                          </a>
                        </div>
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
