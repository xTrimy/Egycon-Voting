@extends('layouts.app')
@section('page')
events
@endsection
@section('title')
Voting Settings - {{ $event->name }}
@endsection
@section('content')

<main class="h-full pb-16 overflow-y-auto">
    <div class="container px-6 mx-auto grid">
        <div class="flex justify-between items-center">
            <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
                Voting Settings - {{ $event->name }}
            </h2>
            <a href="{{ route('events.index') }}" class="px-4 py-2 text-sm font-medium leading-5 text-gray-600 transition-colors duration-150 border border-gray-300 rounded-lg dark:text-gray-400 dark:border-gray-600 hover:text-gray-500 focus:outline-none focus:shadow-outline-blue">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Events
            </a>
        </div>

        @if(Session::has('success'))
        <div class="flex items-center justify-between px-4 p-2 mb-8 text-sm font-semibold text-green-600 bg-green-100 rounded-lg focus:outline-none focus:shadow-outline-purple">
            <div class="flex items-center">
                <i class="fas fa-check mr-2"></i>
                <span>{{ Session::get('success') }}</span>
            </div>
        </div>
        @endif

        @if(Session::has('error'))
        <div class="flex items-center justify-between px-4 p-2 mb-8 text-sm font-semibold text-red-600 bg-red-100 rounded-lg focus:outline-none focus:shadow-outline-purple">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>{{ Session::get('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Current Status -->
        <div class="px-4 py-3 mb-6 rounded-lg border {{ $event->isJudgeVotingEnabled() ? 'bg-green-50 border-green-200 dark:bg-green-900 dark:border-green-700' : 'bg-red-50 border-red-200 dark:bg-red-900 dark:border-red-700' }}">
            <div class="flex items-center">
                <i class="fas {{ $event->isJudgeVotingEnabled() ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }} text-2xl mr-3"></i>
                <div>
                    <h3 class="text-lg font-semibold {{ $event->isJudgeVotingEnabled() ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                        Judge Voting is Currently {{ $event->isJudgeVotingEnabled() ? 'Enabled' : 'Disabled' }}
                    </h3>
                    <p class="{{ $event->isJudgeVotingEnabled() ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} text-sm">
                        {{ $event->getVotingStatusMessage() }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Voting Settings Form -->
        <form method="POST" action="{{ route('events.voting.update', $event) }}" class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
            @csrf

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
                    <i class="fas fa-cog text-purple-500 mr-2"></i>
                    Judge Voting Configuration
                </h3>
            </div>

            @if($errors->any())
                <div class="mb-4">
                    {!! implode('', $errors->all('<div class="text-red-500 text-sm">:message</div>')) !!}
                </div>
            @endif

            <!-- Enable/Disable Toggle -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="hidden" name="judge_voting_enabled" value="0">
                    <input type="checkbox" name="judge_voting_enabled" value="1" {{ $event->judge_voting_enabled ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-purple-600 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2">
                    <span class="ml-2 text-gray-700 dark:text-gray-300 font-medium">
                        Enable Judge Voting for this Event
                    </span>
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    When disabled, judges cannot submit or edit votes for cosplayers in this event
                </p>
            </div>

            <!-- Voting Time Restrictions -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">
                    <i class="fas fa-clock text-blue-500 mr-2"></i>
                    Optional: Time Restrictions
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    Leave empty to allow voting at any time when enabled
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Voting Starts At
                        </label>
                        <input type="datetime-local" name="voting_starts_at"
                               value="{{ $event->voting_starts_at ? $event->voting_starts_at->format('Y-m-d\TH:i') : '' }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Judges cannot vote before this time
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Voting Ends At
                        </label>
                        <input type="datetime-local" name="voting_ends_at"
                               value="{{ $event->voting_ends_at ? $event->voting_ends_at->format('Y-m-d\TH:i') : '' }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Judges cannot vote after this time
                        </p>
                    </div>
                </div>
            </div>


            <!-- Statistics -->
            <div class="mb-6 p-4 bg-purple-50 dark:bg-purple-900 rounded-lg">
                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">
                    <i class="fas fa-chart-bar text-purple-500 mr-2"></i>
                    Voting Statistics
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $event->cosplayers->count() }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Cosplayers</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $event->users->count() }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Judges</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $event->votes->count() }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Total Votes</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $event->cosplayers->where('votes_count', '>', 0)->count() }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Voted On</div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    <i class="fas fa-save mr-2"></i>
                    Update Voting Settings
                </button>
            </div>
        </form>
    </div>
</main>


@endsection
