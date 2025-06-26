<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <script src="{{ mix('js/app.js') }}" defer></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
 <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .swiper-pagination-bullet-active { background-color: #10B981 !important; /* Tailwind green-500 */ }
        .swiper-button-next, .swiper-button-prev { color: #10B981 !important; /* Tailwind green-500 */ }
        .swiper-button-next, .swiper-button-prev {
            top: 50%; transform: translateY(-50%); margin-top: 0; width: 44px; height: 44px;
        }
        @media (max-width: 639px) { /* Below sm breakpoint */
            .swiper-button-next, .swiper-button-prev { display: none; }
        }
    </style>
    
</head>

<body style="  font-family: 'Cairo', sans-serif;">
    <div id="app">
        @include('partials.header')
        @if (Request::is('/'))
            @include('partials.heroSection')
        @endif
        @yield('content')

        @include('partials.footer')
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src = "/resources/js/header.js" > </script>
<script src="https://cdn.jsdelivr.net/npm/preline@latest/dist/preline.js"></script>

<script>
    // Ensure Preline components are initialized after the DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        if (window.HSStaticMethods) {
            HSStaticMethods.autoInit();
        } else {
            console.warn("Preline's HSStaticMethods not found. Dropdown might not initialize.");
        }
    });
</script>
</body>

</html>