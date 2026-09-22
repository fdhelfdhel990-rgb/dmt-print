<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - DMT Print</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar" data-admin-sidebar>
            <a href="{{ route('admin.dashboard') }}" class="admin-brand"><span class="brand-mark">d</span><span><b>DMT</b> Print<small>Panel Admin</small></span></a>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span>⌂</span>Beranda</a>
                <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}"><span>▤</span>Pesanan</a>
                <a href="{{ route('admin.products') }}" class="{{ request()->routeIs('admin.products*','admin.stock') ? 'active' : '' }}"><span>□</span>Produk & Stok</a>
                <a href="{{ route('admin.cashbook') }}" class="{{ request()->routeIs('admin.cashbook') ? 'active' : '' }}"><span>Rp</span>Buku Kas</a>
                <a href="{{ route('admin.appearance') }}" class="{{ request()->routeIs('admin.appearance') ? 'active' : '' }}"><span>◫</span>Tampilan Web</a>
                <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}"><span>⚙</span>Pengaturan</a>
            </nav>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="admin-logout" type="submit">Keluar</button></form>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar"><button class="admin-menu-button" type="button" data-admin-menu aria-label="Buka sidebar">☰</button><div><span class="muted">Darul Muttaqien Printing</span></div><div class="admin-user"><span class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span><div><b>{{ auth()->user()->name }}</b><small>Admin</small></div></div></header>
            <main class="admin-content">@yield('content')</main>
        </div>
    </div>
</body>
</html>
