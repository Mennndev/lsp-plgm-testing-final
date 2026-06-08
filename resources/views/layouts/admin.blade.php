<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - LSP PLGM')</title>

    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">

    @stack('styles')
</head>

<body class="admin-body">
<div id="wrapper">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="LSP PLGM">
        </div>

        <ul class="sidebar-nav">
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.program-pelatihan.*') ? 'active' : '' }}">
                <a href="{{ route('admin.program-pelatihan.index') }}">
                    <i class="bi bi-journal-text"></i> Program Pelatihan
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.asesi.*') ? 'active' : '' }}">
                <a href="{{ route('admin.asesi.index') }}">
                    <i class="bi bi-people"></i> Data Asesi
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.pengajuan.*') ? 'active' : '' }}">
                <a href="{{ route('admin.pengajuan.index') }}">
                    <i class="bi bi-file-earmark-text"></i> Pengajuan Sertifikasi
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.jadwal-asesmen.*') ? 'active' : '' }}">
                <a href="{{ route('admin.jadwal-asesmen.index') }}">
                    <i class="bi bi-calendar2-week"></i> Jadwal Asesmen
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.sertifikat.*') ? 'active' : '' }}">
                <a href="{{ route('admin.sertifikat.index') }}">
                    <i class="bi bi-award"></i> Sertifikat
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}">
                <a href="{{ route('admin.pembayaran.index') }}">
                    <i class="bi bi-credit-card"></i> Pembayaran
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <a href="{{ route('admin.laporan.index') }}">
                    <i class="bi bi-bar-chart"></i> Laporan
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
                <a href="{{ route('admin.chat.index') }}">
                    <i class="bi bi-chat-dots"></i> Live Chat
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                <a href="{{ route('admin.berita.index') }}">
                    <i class="bi bi-newspaper"></i> Berita
                </a>
            </li>
        </ul>
    </aside>

    <main id="page-content-wrapper">
        <nav class="admin-navbar">
            <button id="menu-toggle" class="btn" type="button" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>

            <div class="admin-user">
                <span class="text-muted me-3">Panel Admin LSP PLGM</span>

                <div class="dropdown">
                    <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                        {{ auth()->user()->nama ?? auth()->user()->name ?? 'Administrator' }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-gear"></i> Pengaturan Akun
                        </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <section class="admin-content">
            @yield('content')
        </section>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

<script>
    const menuToggle = document.getElementById('menu-toggle');

    if (menuToggle) {
        menuToggle.onclick = function () {
            document.getElementById('wrapper').classList.toggle('toggled');
        };
    }
</script>

@stack('scripts')
</body>
</html>
