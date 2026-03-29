<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>404 Not Found</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css'])
</head>

<body class="flex-1 bg-white">
    <div class="flex min-h-screen flex-col lg:flex-row items-center justify-center gap-10 lg:gap-30 px-6">
        <div class="flex justify-center">
            <img class="h-auto w-40 md:w-60 lg:w-150" src="{{ asset('img/others/404error.png') }}" alt="404 Not Found" />
        </div>

        <div class="text-center">
            <p class="text-dark-red font-creato-black text-[120px] md:text-[180px] lg:text-[220px] leading-none font-black">404</p>
            <p class="text-dark-red font-creato-black pb-10 text-[40px] lg:text-[60px] leading-none font-bold">
                Not found
            </p>
            <p class="font-creato-bold text-[18px] leading-7">
                Sorry, but the page you are looking for doesn't exist.
            </p>
            <p class="font-creato-bold text-[18px] leading-7">
                Please check your credentials or contact the administrator.
            </p>

            <div class="mt-4 flex justify-center">
                <a href="{{ route('landing') }}" class="button-default mt-5 w-[270px] text-center">
                    RETURN TO HOME PAGE
                </a>
            </div>
        </div>
    </div>
</body>

</html>