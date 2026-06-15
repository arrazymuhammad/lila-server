<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LILA</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-white text-gray-900">

    @include('layouts.partials.landing-navbar')

    @yield('content')

    @include('layouts.partials.landing-footer')

</body>

</html>
