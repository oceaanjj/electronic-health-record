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
            <img class="h-auto w-120" src="img/others/403error.png" alt="403 Forbidden" />
        </div>

        <div>
            <p class="text-dark-red font-creato-black text-center text-[220px] leading-none font-black">403</p>
            <p class="text-dark-red font-creato-black pb-10 text-center text-[60px] leading-none font-bold">
                Forbidden
            </p>
            <p class="font-creato-bold text-center text-[18px] leading-7">
                You do not have permission to access this page.
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