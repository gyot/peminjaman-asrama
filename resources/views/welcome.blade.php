<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Aplikasi Peminjaman Asrama">
    <meta name="author" content="BPMP Provinsi NTB">
    <title>Aplikasi Peminjaman Asrama</title>
    {{-- <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet"> --}}
    {{-- Load file CSS dan JS hasil build --}}
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    {{-- <link rel="stylesheet" href="{{ asset('build/assets/app-DFieIcXq.css') }}">
    <script type="module" src="{{ asset('build/assets/app-C3dTeOKN.js') }}"></script> --}}
</head>
<body>
    <div id="app"></div>
</body>
</html>
