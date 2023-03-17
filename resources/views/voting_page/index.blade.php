<!DOCTYPE html>
<html lang="en" class="w-full h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- @vite('resources/css/app.css') --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=1.0.0" />
    <title>
        {{ isset($poll)?$poll->name." (Poll)":"Egycon Voting!" }}
    </title>
    <style>
        /* add font  */
        @font-face {
            font-family: 'BADABB';
            font-style: normal;
            src: url("{{ asset('fonts/BADABB.ttf') }}") format('truetype');
        }
        body{
            background-image: url("{{ asset('images/bg.jpg') }}");
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-position: center;
            font-family: 'BADABB', Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<body class="w-full h-full">
    @php
        $voting_max = 5;
    @endphp
  
    @if(Session::has('status'))
      <div class="top-8 absolute left-1/2 transform -translate-x-1/2 w-64 h-32 mx-auto">
        <img src="{{ asset('Egycon_Polls.png') }}" class="w-full h-full object-contain" alt="">
    </div>
        <div class="w-full h-full flex items-center justify-center">
            <div class="flex items-center justify-center flex-col w-full px-2 mb-16">
                <p class="text-white text-5xl text-center my-2">{{ Session::get('status') }}</p>
            </div>
        </div>
          <div class="flex justify-center w-48 h-48 mx-auto absolute bottom-0 left-1/2 transform -translate-x-1/2">
                <img src="{{ asset('powered-by text.png') }}" class="w-full h-full object-contain"  alt="">
        </div>
    @else
        <div class="my-8 w-64 h-32 mx-auto">
            <img src="{{ asset('Egycon_Polls.png') }}" class="w-full h-full object-contain" alt="">
        </div>
        <form method="POST">
            @csrf 
            @foreach ($poll_data as $poll_d)
                <div class="flex items-center justify-center flex-col w-full px-2 mb-16">
                    @foreach ($poll_d->poll_data_lines as $poll_data_line)
                        @if ($poll_data_line->poll_line->type == "text")
                            <p class="text-white text-4xl my-2">{{ $poll_data_line->value }}</p>
                        @endif
                        @if ($poll_data_line->poll_line->type == "file")
                            <div class="w-96 max-w-full h-96 bg-black">
                                <img class="my-2 w-full h-full object-contain object-center" src="{{ asset('images/' . $poll_data_line->value) }}" alt="">
                            </div>
                        @endif
                    @endforeach
                    <div class="flex space-x-2"> 
                        @for($i = 0; $i<$voting_max; $i++)
                            <label class="w-16 h-16">
                                <input type="radio" required class="peer hidden" name="vote_{{ $poll_d->id }}" id="vote_{{ $poll_d->id }}_{{ $i }}" value="{{ $i+1 }}">
                                <div class="w-full h-full text-4xl rounded-md flex peer-checked:border-2 ring-0 ring-transparent peer-checked:ring-green-500 peer-checked:ring-2 transition-all border-0 border-transparent peer-checked:border-green-700 items-center justify-center peer-checked:bg-green-500 text-black bg-egycon-magenta">
                                    {{ $i+1 }}
                                </div>
                            </label>
                        @endfor
                    </div>
                </div>
            @endforeach
            <div class="flex items-center justify-center flex-col w-full px-2 mb-16">
                <button type="submit" onclick="alert_if_not_all_voted()"  class="bg-green-600 text-4xl text-white py-2 px-8 rounded-md">
                    Submit your vote
                </button>
            </div>
            <div>
            </div>
        </form>
          <div class="flex justify-center w-48 h-48 mx-auto">
                <img src="{{ asset('powered-by text.png') }}" class="w-full h-full object-contain"  alt="">
        </div>
    @endif
      
<script>
        function check_all_rating_voted(){
            var all_voted = true;
            var set = [];
            var all_radio = document.querySelectorAll('input[type="radio"]');
            all_radio.forEach(function(radio){
                if(!set.includes(radio.name)){
                    set.push(radio.name);
                }
            });
            set.forEach(function(set_name){
                var set_radio = document.querySelector('input[name="' + set_name + '"]:checked');
                if(set_radio == null){
                    all_voted = false;
                }
            });
            return all_voted;
        }

        function alert_if_not_all_voted(){
            if(!check_all_rating_voted()){
                alert('Please vote for all the items');
            }
        }
    </script>
</body>
</html>