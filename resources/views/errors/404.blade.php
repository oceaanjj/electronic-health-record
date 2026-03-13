<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>403 Forbidden</title>
    @vite(['resources/css/app.css'])
</head>

<body class="flex-1 bg-white">
    <div class="flex min-h-screen flex-row items-center justify-center gap-30 space-y-6">
        <div>
            <img class="h-auto w-150" src="img/others/404error.png" alt="404 Not Found" />
        </div>

        <div>
            <p class="text-dark-red font-creato-black text-center text-[220px] leading-none font-black">404</p>
            <p class="text-dark-red font-creato-black pb-10 text-center text-[60px] leading-none font-bold">
                Not found
            </p>
            <p class="font-creato-bold text-center text-[18px] leading-7">
                Sorry, but the page you are looking for doesn't exist.
            </p>
            <p class="font-creato-bold text-center text-[18px] leading-7">
                Please check your credentials or contact the administrator.
            </p>

            <div class="mt-4 flex justify-center">
                <a href="{{ route('home') }}" class="button-default mt-5 w-[270px] text-center">
                    RETURN TO HOME PAGE
                </a>
            </div>
        </div>
    </div>
</body>

</html>