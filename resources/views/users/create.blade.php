@extends('layouts.app')
@section('page')
users
@endsection
@section('title')
Add User
@endsection
@section('content')

<main class="h-full pb-16 overflow-y-auto">
          <div class="container px-6 mx-auto grid">
            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              @isset($user)
                Edit User - {{ $user->name }}
                @else
                Add New User
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
              class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800"
            >

            <span class="text-red-500 text-sm">* Is required</span>
              @isset($user)
              @method('PUT')
              <input type="hidden" name="event_edit_id" value="{{ $user->id }}">
              @endisset
            @csrf
            @if($errors->any())
                {!! implode('', $errors->all('<div class="text-red-500">:message</div>')) !!}
            @endif
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-signature text-xl"></i>
                Full Name <span class="text-red-500">*</span>
                </span>
                <input
                value="{{ old('name')??$user->name??"" }}"
                type="text"
                name="name"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="John Doe"
                />
              </label>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-envelope text-xl"></i>
                Email <span class="text-red-500">*</span>
                </span>
                 <input
                value="{{ old('email')??$user->email??"" }}"
                type="email"
                name="email"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="jon@doe.com"
                />
              </label>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-key text-xl"></i>
                Password <span class="text-red-500">*</span>
                </span>
                <input
                type="password"
                name="password"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="Pa$$w0rd"
                />
              </label>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-key text-xl"></i>
                Confirm Password <span class="text-red-500">*</span>
                </span>
                <input
                type="password"
                name="password_confirmation"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="Pa$$w0rd"
                />
              </label>
              <div>
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-circle text-xl"></i>
                Assign to events <span class="text-red-500">*</span>
                </span>
                <br>
                @foreach ($events as $event)
                  <label>
                    <input type="checkbox" name="events[]" value="{{ $event->id }}"> 
                  <span class="text-gray-700 dark:text-gray-400">
                    {{ $event->name }}
                  </span>
                  </label>
                  <br>
                @endforeach
              </div>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-circle text-xl"></i>
                Role <span class="text-red-500">*</span>
                </span>
                <select
                name="role"
                  required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                >
                <option value="" selected disabled>Select Role</option>
                @foreach($roles as $role)
                  <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
                </select>
              </label>
             <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-circle text-xl"></i>
                Vote Weight (0 to 100) <span class="text-red-500">*</span>
                </span>
                <input
                value="{{ old('weight')??$user->vote_weight??"" }}"
                type="number"
                name="weight"
                    required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                  placeholder="50"
                />
              </label>
                 
              <button type="submit" class="table items-center mt-4 justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
              @isset($user)
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
