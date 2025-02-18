@php
$first_event_id = auth()->user()->events()->get()->pluck('id')->first();

$cosplayer_s = "Cosplayer";
$cosplayers_s = "Cosplayers";
if($first_event_id == 6){
    $cosplayer_s = "Singer";
    $cosplayers_s = "Singers";
}
@endphp
<div class="py-4 text-gray-500 dark:text-gray-400">
          <a
            class="ml-6 text-lg flex flex-wrap items-center font-bold text-gray-800 dark:text-gray-200"
            href="#"
          >
          <div class="w-8 h-8 inline-block mr-4 group">
            @php
              $logo = 'logo.png';
              $dashboard_name = 'Egycon Voting';
              if(isset($event_id)){
                $event = App\Models\Event::find($event_id);
                $dashboard_name = $event->name;
                if($event->logo)
                  $logo = Storage::url($event->logo);
              }
              
            @endphp
            <img src="{{ asset($logo) }}" class="w-full h-full object-contain" alt="">
          </div>
            {{ $dashboard_name }}
          </a>
          <ul class="mt-6">
            <li class="relative px-6 py-3">
              @if($page == 'dashboard')
              <span
                class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                aria-hidden="true"
              ></span>
              @endif
              <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                {{-- href="{{ route('admin.home',$event_id) }}" --}}
              >
                <svg
                  class="w-5 h-5"
                  aria-hidden="true"
                  fill="none"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                  ></path>
                </svg>
                <span class="ml-4">Dashboard</span>
              </a>
            </li>
          </ul>
          
          <ul>
            <li class="relative px-6 py-3">
                @if($page == 'cosplayers')
                <span
                    class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                    aria-hidden="true"
                ></span>
                @endif
              <button
                class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                @click="toggleCosplayerMenu"
                aria-haspopup="true"
              >
                <span class="inline-flex items-center">
                  <i class="las la-theater-masks text-2xl"></i>
                  <span class="ml-4">{{$cosplayers_s}}</span>
                </span>
                <svg
                  class="w-4 h-4"
                  aria-hidden="true"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  ></path>
                </svg>
              </button>
              <template x-if="isCosplayerMenuOpen">
                <ul
                  x-transition:enter="transition-all ease-in-out duration-300"
                  x-transition:enter-start="opacity-25 max-h-0"
                  x-transition:enter-end="opacity-100 max-h-xl"
                  x-transition:leave="transition-all ease-in-out duration-300"
                  x-transition:leave-start="opacity-100 max-h-xl"
                  x-transition:leave-end="opacity-0 max-h-0"
                  class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                  aria-label="submenu"
                >
                  <li
                    class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                  >
                    <a class="w-full" 
                    href="{{ route('cosplayers.create') }}"
                    >
                      Add {{$cosplayer_s}}</a>
                  </li>
                  <li
                    class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                  >
                    <a class="w-full" 
                    href="{{ route('cosplayers.bulk-add') }}"
                    >
                      Bulk Add {{$cosplayer_s}}</a>
                  </li>
                  <li
                    class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                  >
                    <a class="w-full" 
                    href="{{ route('cosplayers.bulk-upload-references') }}"
                    >
                      Bulk Upload References</a>
                  </li>
                  <li
                    class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                  >
                    <a class="w-full" 
                    href="{{ route('cosplayers.index') }}"
                    >
                      View {{$cosplayers_s}}
                    </a>
                  </li>
                </ul>
              </template>
            </li>
            <li class="relative px-6 py-3">
                @if($page == 'events')
                <span
                    class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                    aria-hidden="true"
                ></span>
                @endif
              <button
                class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                @click="toggleEventMenu"
                aria-haspopup="true"
              >
                <span class="inline-flex items-center">
                  <i class="las la-calendar text-2xl"></i>
                  <span class="ml-4">Events</span>
                </span>
                <svg
                  class="w-4 h-4"
                  aria-hidden="true"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  ></path>
                </svg>
              </button>
              <template x-if="isEventMenuOpen">
                <ul
                  x-transition:enter="transition-all ease-in-out duration-300"
                  x-transition:enter-start="opacity-25 max-h-0"
                  x-transition:enter-end="opacity-100 max-h-xl"
                  x-transition:leave="transition-all ease-in-out duration-300"
                  x-transition:leave-start="opacity-100 max-h-xl"
                  x-transition:leave-end="opacity-0 max-h-0"
                  class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                  aria-label="submenu"
                >
                  <li
                    class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                  >
                    <a class="w-full" 
                    href="{{ route('events.create') }}"
                    >
                      Add Event</a>
                  </li>
                  <li
                    class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                  >
                    <a class="w-full" 
                    href="{{ route('events.index') }}"
                    >
                      View Events
                    </a>
                  </li>
                </ul>
              </template>
            </li>
            {{-- <li class="relative px-6 py-3">
                @if($page == 'users')
                <span
                    class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                    aria-hidden="true"
                ></span>
                @endif
              <button
                class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                @click="toggleUsersMenu"
                aria-haspopup="true"
              >
                <span class="inline-flex items-center">
                  <i class="las la-users text-2xl"></i>
                  <span class="ml-4">Users</span>
                </span>
                <svg
                  class="w-4 h-4"
                  aria-hidden="true"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  ></path>
                </svg>
              </button>
              <template x-if="isUsersMenuOpen">
                <ul
                  x-transition:enter="transition-all ease-in-out duration-300"
                  x-transition:enter-start="opacity-25 max-h-0"
                  x-transition:enter-end="opacity-100 max-h-xl"
                  x-transition:leave="transition-all ease-in-out duration-300"
                  x-transition:leave-start="opacity-100 max-h-xl"
                  x-transition:leave-end="opacity-0 max-h-0"
                  class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900"
                  aria-label="submenu"
                >
                  <li
                    class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                  >
                    <a class="w-full" 
                    >Invite User</a>
                  </li>
                  <li
                    class="px-2 py-1 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                  >
                    <a class="w-full" 
                    >
                      View Users
                    </a>
                  </li>
                </ul>
              </template>
              
            </li> --}}
            @if(auth()->user()->hasRole('admin'))
            <li class="relative px-6 py-3">
              @if($page == 'users')
              <span
                class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                aria-hidden="true"
              ></span>
              @endif
              <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="{{ route('users.index') }}"
              >
                <i class="las la-users text-2xl"></i>
                <span class="ml-4">Users</span>
              </a>
            </li>
            @endisset
            @if(auth()->user()->hasRole('admin'))
            <li class="relative px-6 py-3">
              @if($page == 'polls')
              <span
                class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                aria-hidden="true"
              ></span>
              @endif
              <a
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                href="{{ route('polls.index') }}"
              >
                <i class="las la-poll text-2xl"></i>
                <span class="ml-4">Polls</span>
              </a>
            </li>
            @endisset
          </ul>
          
          <div class="px-6 my-6">
            <a
            href="{{ route('logout') }}"
              class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
            >
              Logout
              <i class="ml-2 las la-sign-out-alt text-xl"></i>
          </a>
          </div>
        </div>