<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap');
</style>
</head>
<body>
    <div id="app">
        @include('partials.header') 
        @include('partials.heroSection')
        {{-- @include('partials.footer')  --}}
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
<script src="/resources/js/header.js"></script>

</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const locale = document.documentElement.lang;
        if (locale === 'ar') {
            document.body.classList.add('rtl');
        } else {
            document.body.classList.remove('rtl');
        }
    })
