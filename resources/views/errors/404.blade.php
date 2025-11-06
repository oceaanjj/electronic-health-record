<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    @vite(['./resources/css/app.css'])

</head>

<body class="bg-white flex-1">
    <div class="flex flex-row items-center justify-center min-h-screen space-y-6 gap-30">
        <div>
            <img class="w-120 h-auto" src="img/others/404error.png" alt="404 Not Found">
        </div>

        <div>
            <p class="text-[220px] text-dark-red font-creato-black leading-none text-center">404</p>
            <p class="text-[60px] text-dark-red font-creato-black leading-none pb-10 text-center">Not found</p>
            <p class="text-[18px] font-creato-bold text-center leading-7">Sorry, but the page you are looking for
                doess't exist.</p>
            <p class="text-[18px] font-creato-bold text-center leading-7">Please check your credentials or contact the
                administrator.</p>

            <div class="mt-4 flex justify-center">
                <a href="{{ route('home') }}"
                    class="font-creato-bold text-[15px] text-white px-6 py-3 bg-ehr rounded-full shadow-md
                     hover:bg-dark-yellow hover:font-creato-bold hover:text-ehr hover:shadow-lg transition ease-in-out duration-200">
                    RETURN TO HOME PAGE
                </a>
            </div>

        </div>
    </div>
</body>

</html>