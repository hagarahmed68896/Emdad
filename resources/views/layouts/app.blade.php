<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Page Title --}}
    <title>@yield('page_title', 'الرئيسية')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">

    {{-- Styles --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <script src="{{ mix('js/app.js') }}" defer></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

    {{-- Scripts --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    {{-- Inline Custom Styles --}}
    <style>
        .swiper-pagination-bullet-active {
            background-color: #10B981 !important;
        }
        .swiper-button-next, .swiper-button-prev {
            color: #10B981 !important;
            top: 50%;
            transform: translateY(-50%);
            margin-top: 0;
            width: 44px;
            height: 44px;
        }
        @media (max-width: 639px) {
            .swiper-button-next, .swiper-button-prev {
                display: none;
            }
        }
    </style>

    {{-- Extra head for specific pages --}}
    @stack('head')
</head>

<body style="font-family: 'Cairo', sans-serif;">
    <div id="app">

        {{-- HEADER --}}
        @auth
            @if(Auth::user()->account_type === 'supplier')
                @include('supplier.header_supplier')
            @else
                @include('partials.header')
            @endif
        @else
            @include('partials.header')
        @endauth

        {{-- HERO SECTION (Only for Home Page) --}}
        @if (Request::is('/') || Request::is('supplier'))
            @auth
                @if(Auth::user()->account_type === 'supplier')
                    @include('supplier.heroSection_supplier')
                @else
                    @include('partials.heroSection')
                @endif
            @else
                @include('partials.heroSection')
            @endauth
        @endif

        {{-- MAIN PAGE CONTENT --}}
        @yield('content')

        {{-- FOOTER --}}
        @include('partials.footer')

    </div>

@if (!request()->routeIs('messages.index'))

    <!-- Floating Message Button -->
<a href="{{ route('messages.index') }}" 
   class="fixed top-[40%] rtl:left-0 flex-col gap-1 z-50 border rounded-sm px-2 py-4 shadow-lg hover:bg-[#185D31] hover:text-white transition">
   <!-- Icon -->
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
</svg>
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
</svg>
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
</svg>


</a>
@endif

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/resources/js/header.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/preline@latest/dist/preline.js"></script>

    {{-- Inline Script to Initialize Preline --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.HSStaticMethods) {
                HSStaticMethods.autoInit();
            } else {
                console.warn("Preline's HSStaticMethods not found. Dropdown might not initialize.");
            }
        });
    </script>

    {{-- Extra scripts for specific pages --}}
    @stack('scripts')

</body>
</html>
