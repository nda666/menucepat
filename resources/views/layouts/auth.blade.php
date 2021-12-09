<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? '' }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    {{-- <script src="{{ mix('js/bundle.js') }}"></script> --}}


    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('js/app.js') }}" defer></script>

    @yield('css')

</head>

<body class="antialiased">

    <div class="h-screen bg-white relative flex flex-col space-y-10 justify-center items-center">
        <div class="w-96 mx-auto h-screen ">

            @yield('content')
        </div>
    </div>
    @yield('script')
</body>



</html>
