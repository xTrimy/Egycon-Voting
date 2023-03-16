@extends('layouts.app')
@section('page')
poll_data
@endsection
@section('title')
Poll Data - {{ $poll->name }}
@endsection
@section('content')
        <main class="h-full pb-16 overflow-y-auto">
          <div class="container grid px-6 mx-auto">
              <div class="flex justify-between items-center">

            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              Poll Data - {{ $poll->name }}
            </h2>
            <a href="{{ route('poll_data.create',$poll) }}"><button class="bg-purple-600 text-white py-2 px-8 rounded-md">
                Add Poll Data
            </button></a>
              </div>
           
            <div class="w-full overflow-hidden rounded-lg shadow-xs">
              <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap" id="images">
                  <thead>
                    <tr
                      class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
                    >
                      @foreach ($poll->poll_lines as $key=>$poll_line)
                      <th class="px-4 py-3">
                        {{ $key+1}} -
                        @if($poll_line->type == 'text')
                        {{ $poll_line->type }}
                        @elseif ($poll_line->type == 'file')
                        image 
                        @endif
                      </th>
                      @endforeach
                      <th class="px-4 py-3">Rating</th>
                      <th class="px-4 py-3">Actions</th>
                    </tr>
                  </thead>
                  <tbody
                    class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800"
                  >
                    @foreach ($poll_data as $poll_d)
                        
                    <tr class="text-gray-700 dark:text-gray-400">
                      @foreach ($poll_d->poll_data_lines as $poll_data_line)
                        <td class="px-4 py-3">
                          @if($poll_data_line->poll_line->type == 'text')
                          {{ $poll_data_line->value }}
                          @elseif ($poll_data_line->poll_line->type == 'file')
                          <img src="{{ asset('images/'.$poll_data_line->value) }}" alt="" width="100px">
                          @endif
                        </td>
                      @endforeach
                      <td class="px-4 py-3">
                        {{ $poll_d->avg }} / 5 ({{ $poll_d->percentage }}%)
                      </td>
                      <td>
                        <div class="flex items-center text-sm py-2">
                            {{-- <a 
                            href="{{ route('poll_data.edit',['poll'=>$poll,'poll_datum'=>$poll_d]) }}"
                            >
                            <button
                                class="flex items-center group disabled:hover:bg-inherit disabled:cursor-not-allowed  hover:bg-gray-300 dark:hover:bg-gray-600 justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                aria-label="Switch"
                            >
                                <i class="las la-pen text-xl group-disabled:text-gray-500 text-green-500"></i>
                            </button>
                            </a> --}}
                            <button onclick="display_popup(this)"
                                data-title="Delete Poll"
                                data-content="Are you sure you want to delete this poll?"
                                data-action="{{ route('poll_data.destroy',['poll'=>$poll,'poll_datum'=>$poll_d]) }}"
                                data-method="DELETE"
                                class="flex items-center group disabled:hover:bg-inherit disabled:cursor-not-allowed  hover:bg-gray-300 dark:hover:bg-gray-600 justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                aria-label="Switch"
                            >
                                <i class="las la-trash text-xl group-disabled:text-gray-500 text-red-500"></i>
                            </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            <div class="mt-4">
             {{-- {{$poll_data->links('pagination::tailwind')}} --}}
            </div>
          </div>
        </main>

@endsection