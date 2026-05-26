<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Scan Aset</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SB ADMIN CSS (INI WAJIB) --}}
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">

</head>

<body class="bg-light">

    <div class="container py-4" style="max-width:500px;">
        @yield('content')
    </div>

    {{-- JS --}}
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>