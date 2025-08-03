<!-- resources/views/layouts/master.blade.php -->

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body style="font-family: 'Cairo', sans-serif;">
    <div id="app">
        {{-- HEADER --}}
        @yield('header')

        {{-- HERO --}}
        @yield('hero')

        {{-- PAGE CONTENT --}}
        @yield('content')

        {{-- FOOTER --}}
        @include('partials.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/resources/js/header.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/preline@latest/dist/preline.js"></script>
</body>
</html>
