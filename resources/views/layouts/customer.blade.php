<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DMT Print - layanan digital printing cepat dan berkualitas.">
    <title>@yield('title', 'DMT Print')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="customer-body">
    @include('partials.customer-header')
    @hasSection('breadcrumb')
        <div class="breadcrumb-bar"><div class="site-container">@yield('breadcrumb')</div></div>
    @endif
    <main>@yield('content')</main>
    @include('partials.customer-footer')
</body>
</html>
