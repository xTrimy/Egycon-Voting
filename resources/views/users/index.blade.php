@extends('layouts.app')
@section('page')
users
@endsection
@section('title')
Users
@endsection
@section('content')
        <main class="h-full pb-16 overflow-y-auto">
          <div class="container grid px-6 mx-auto">
              <div class="flex justify-between items-center">
              <h2
                class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
              >
                Users
              </h2>
            @include('includes.alerts')
            
            <a href="{{ route('users.create') }}"><button class="bg-purple-600 text-white py-2 px-8 rounded-md">
                Add new user
            </button></a>
              </div>
            <div class="w-full overflow-hidden rounded-lg shadow-xs">
              <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap" id="images">
                  <thead>
                    <tr
                      class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
                    >
                      <th class="px-4 py-3">#</th>
                      <th class="px-4 py-3">Full Name</th>
                      <th class="px-4 py-3">Email</th>
                      <th class="px-4 py-3">Events</th>
                      <th class="px-4 py-3">Role</th>
                      <th class="px-4 py-3">Vote weight</th>
                    </tr>
                  </thead>
                  <tbody
                    class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800"
                  >
                    @foreach ($users as $user)
                        
                    <tr class="text-gray-700 dark:text-gray-400">
                      <td class="px-4 py-3">
                        {{ $user->id }}
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center text-sm">
                          <div>
                            <p class="font-semibold">{{ $user->name }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        {{ $user->email }}
                      </td>
                      <td class="px-4 py-3">
                        {{ implode(', ', $user->events()->pluck('name')->toArray()) }}
                      </td>
                      <td class="px-4 py-3">
                        {{ implode(', ', $user->roles()->pluck('name')->toArray()) }}
                      </td>
                      <td class="px-4 py-3">
                        {{ $user->vote_weight }}
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