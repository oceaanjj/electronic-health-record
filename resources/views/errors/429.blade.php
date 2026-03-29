<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>429 Too Many Requests</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css'])
</head>

<body class="flex-1 bg-white">
    <div class="flex min-h-screen flex-col lg:flex-row items-center justify-center gap-10 lg:gap-30 px-6">
        <div class="flex justify-center">
            <img class="h-auto w-40 md:w-60 lg:w-120" src="{{ asset('img/others/403error.png') }}" alt="429 Too Many Requests" />
        </div>

        <div class="text-center">
            <p class="text-dark-red font-creato-black text-[120px] md:text-[180px] lg:text-[220px] leading-none font-black">429</p>
            <p class="text-dark-red font-creato-black pb-10 text-[40px] lg:text-[60px] leading-none font-bold">
                Wait a Minute
            </p>
            <p class="font-creato-bold text-[18px] leading-7">
                You've made too many requests in a short amount of time.
            </p>
            <p id="wait-message" class="font-creato-bold text-[18px] leading-7 text-dark-red">
                Please wait <span id="timer">--:--</span> before trying again.
            </p>

            <div class="mt-4 flex justify-center">
                <a href="{{ route('login') }}" class="button-default mt-5 w-[270px] text-center">
                    RETURN TO LOGIN PAGE
                </a>
            </div>
        </div>
    </div>

    @php
        $seconds = 0;
        try {
            $seconds = $exception->getHeaders()['Retry-After'] ?? 0;
        } catch (\Exception $e) {
            $seconds = 60; // Default fallback
        }
    @endphp

    <script>
        let seconds = {{ $seconds }};
        const timerElement = document.getElementById('timer');
        const waitMessage = document.getElementById('wait-message');

        function updateTimer() {
            if (seconds <= 0) {
                waitMessage.innerHTML = "You can now try again!";
                waitMessage.classList.remove('text-dark-red');
                waitMessage.classList.add('text-green-600');
                return;
            }

            const hrs = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            let display = "";
            if (hrs > 0) {
                display += (hrs < 10 ? '0' + hrs : hrs) + ":";
            }
            display += (mins < 10 ? '0' + mins : mins) + ":" + (secs < 10 ? '0' + secs : secs);
            
            timerElement.innerText = display;
            
            seconds--;
            setTimeout(updateTimer, 1000);
        }

        updateTimer();
    </script>
</body>

</html>
