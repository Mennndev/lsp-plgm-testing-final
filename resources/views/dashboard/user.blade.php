<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard LSP - Pengguna</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap"
          rel="stylesheet">

    {{-- CSS khusus dashboard user --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard-user.css') }}">
</head>
<body>
<div id="wrapper">
    {{-- SIDEBAR --}}
    <div id="sidebar-wrapper">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo LSP"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22160%22 height=%2260%22%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2216%22 fill=%22%234e73df%22%3ELSP Logo%3C/text%3E%3C/svg%3E'">
        </div>
        <ul class="sidebar-nav">
            <li>
                <a href="#" class="active menu-link" data-target="beranda">
                    <i class="fas fa-tachometer-alt"></i> Beranda
                </a>
            </li>
            <li>
                <a href="#" class="menu-link" data-target="pengajuan">
                    <i class="far fa-file-lines"></i> Pengajuan Skema
                </a>
            </li>
            <li>
                <a href="#" class="menu-link" data-target="riwayat">
                    <i class="fa fa-history"></i> Riwayat Asesmen
                </a>
            </li>
            {{-- Live Chat Menu --}}
            <li>
                <a href="{{ route('chat.index') }}">
                    <i class="bi bi-chat-dots"></i> Live Chat
                </a>
            </li>
        </ul>
    </div>

    {{-- PAGE CONTENT --}}
    <div id="page-content-wrapper">
        {{-- TOP NAVBAR --}}
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
            <div class="container-fluid">
                {{-- Hamburger Toggle --}}
                <button class="btn btn-link text-dark" type="button" id="sidebarToggle">
                    <i class="fas fa-bars fs-5"></i>
                </button>

                {{-- Right Side --}}
                <ul class="navbar-nav ms-auto d-flex flex-row align-items-center">
                    {{-- Notification Dropdown --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-bell"></i>
                            @if(($notificationCount ?? 0) > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $notificationCount }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 320px;">
                            <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
                                <strong>Notifikasi</strong>
                                @if(($notificationCount ?? 0) > 0)
                                    <form action="{{ route('notifications.readAll') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none">Tandai semua dibaca</button>
                                    </form>
                                @endif
                            </li>
                            <li><hr class="dropdown-divider m-0"></li>
                            <li class="notification-body" style="max-height: 300px; overflow-y: auto;">
                                @forelse($latestNotifications ?? [] as $notif)
                                    <a href="{{ route('notifications.read', $notif->id) }}"
                                       class="dropdown-item d-flex align-items-start py-2 {{ $notif->is_read ? '' : 'bg-light' }}">
                                        <div class="notification-icon me-3 text-{{ $notif->type ?? 'primary' }}">
                                            <i class="bi {{ $notif->icon ?? 'bi-bell' }} fs-5"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 fw-semibold small">{{ $notif->title }}</p>
                                            <p class="mb-1 text-muted small">{{ Str::limit($notif->message, 50) }}</p>
                                            <small class="text-muted">{{ $notif->time_ago ?? $notif->created_at->diffForHumans() }}</small>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-bell-slash fs-3 mb-2 d-block"></i>
                                        <p class="mb-0">Tidak ada notifikasi</p>
                                    </div>
                                @endforelse
                            </li>
                            @if(isset($latestNotifications) && $latestNotifications->count() > 0)
                                <li><hr class="dropdown-divider m-0"></li>
                                <li class="text-center py-2">
                                    <a href="{{ route('notifications.index') }}" class="text-decoration-none">Lihat Semua Notifikasi</a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    {{-- User Dropdown --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nama ?? $user->name ?? 'User') }}&background=4e73df&color=fff&size=32"
                                 alt="User" class="rounded-circle me-2" width="32" height="32">
                            <span class="d-none d-md-inline">{{ $user->nama ?? $user->name ?? 'User' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('ProfileUser.edit') }}">
                                    <i class="bi bi-person me-2"></i> Profil Saya
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- MAIN CONTENT --}}
        <div class="main-content">
            {{-- BERANDA --}}
            <div id="beranda" class="content-section active">
                <div class="page-header page-header-beranda">
                    <h1><i class="fas fa-home"></i> Beranda</h1>
                    <p>Ringkasan jadwal asesmen Anda.</p>
                </div>

                {{-- Quick Actions Section --}}
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm hover-card">
                            <div class="card-body text-center">
                                <i class="bi bi-chat-dots text-primary chat-icon"></i>
                                <h5 class="card-title mt-3">Live Chat</h5>
                                <p class="card-text text-muted">Hubungi admin untuk bantuan dan pertanyaan</p>
                                <a href="{{ route('chat.index') }}" class="btn btn-primary">
                                    <i class="bi bi-chat-left-dots me-2"></i>Buka Live Chat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row filter-row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="asesmenSearch"
                               placeholder="Cari asesmen">
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="asesmenStatusFilter">
                            <option value="">Semua Status</option>
                            <option>Terjadwal</option>
                            <option>Selesai</option>
                            <option>Ditunda</option>
                            <option>Dibatalkan</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive table-custom">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Asesmen</th>
                            <th>Skema Sertifikasi</th>
                            <th>TUK</th>
                            <th>Alamat</th>
                            <th>Tanggal Asesmen</th>
                            <th>Link Virtual Meeting</th>
                            <th>Asesor</th>
                            <th>Jenis Bukti</th>
                            <th>Rekomendasi</th>
                            <th>Status</th>
                            <th>Status Asesmen</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody id="asesmenTableBody">
                        @forelse($asesmenList as $asesmen)
                            <tr data-search="{{ \Illuminate\Support\Str::lower(($asesmen->kode ?? '') . ' ' . ($asesmen->skema_nama ?? '') . ' ' . ($asesmen->tuk_nama ?? '') . ' ' . ($asesmen->asesor_nama ?? '')) }}"
                                data-status="{{ $asesmen->status_asesmen ?? '' }}">
                                <td>{{ $asesmen->kode ?? '-' }}</td>
                                <td>{{ $asesmen->skema_nama ?? '-' }}</td>
                                <td>{{ $asesmen->tuk_nama ?? '-' }}</td>
                                <td>{{ $asesmen->tuk_alamat ?? '-' }}</td>
                                <td>{{ $asesmen->tanggal_asesmen ?? '-' }}</td>
                                <td>
                                    @if(!empty($asesmen->link_meeting))
                                        <a href="{{ $asesmen->link_meeting }}" target="_blank">Link</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $asesmen->asesor_nama ?? '-' }}</td>
                                <td>{{ $asesmen->jenis_bukti ?? '-' }}</td>
                                <td>{{ $asesmen->rekomendasi ?? '-' }}</td>
                                <td>{{ $asesmen->status ?? '-' }}</td>
                                <td>{{ $asesmen->status_asesmen ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('pengajuan.show', $asesmen->pengajuan_id) }}" class="btn btn-xs btn-primary btn-custom">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center empty-table">
                                    <i class="fa fa-inbox empty-icon"></i>
                                    <p>Tidak ada data</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PENGAJUAN SKEMA --}}
            <div id="pengajuan" class="content-section">
                <div class="page-header page-header-pengajuan">
                    <div class="pull-left">
                        <h1><i class="fa fa-file-text-o"></i> Pengajuan Skema</h1>
                        <p>Daftar skema sertifikasi yang diajukan.</p>
                    </div>
                <div class="pull-right">
                    <a href="{{ route('pengajuan.pilih-skema') }}" class="btn btn-page-header">
                        <i class="fa fa-plus"></i> Ajukan Skema Baru
                    </a>
                </div>
                    <div class="clearfix"></div>
                </div>

                <div class="table-responsive table-custom">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="35%">Nama Skema</th>
                            <th width="15%">Tanggal Pengajuan</th>
                            <th width="15%">Status Dokumen</th>
                            <th width="15%">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($pengajuanList as $index => $pengajuan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $pengajuan->program->nama ?? '-' }}</td>
                                <td>{{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <span class="status-badge {{ $pengajuan->status }}">
                                        {{ $pengajuan->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('pengajuan.show', $pengajuan->id) }}" class="btn btn-success btn-xs btn-custom">Lihat Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center empty-table">
                                    <i class="fa fa-inbox empty-icon"></i>
                                    <p>Belum ada pengajuan skema</p>
                                    <a href="{{ route('pengajuan.pilih-skema') }}" class="btn btn-primary btn-sm">Ajukan Skema Sekarang</a>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- RIWAYAT ASESMEN --}}
            <div id="riwayat" class="content-section">
                <div class="page-header page-header-riwayat">
                    <div>
                        <h1><i class="fa fa-history"></i> Riwayat Asesmen</h1>
                        <p>Sertifikat yang sudah diterbitkan oleh admin.</p>
                    </div>
                </div>

                {{-- Search --}}
                <div class="row filter-row">
                    <div class="col-md-5">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   id="riwayatSearch"
                                   placeholder="Cari skema sertifikasi">
                            <button class="btn btn-outline-secondary" type="button" id="riwayatSearchButton">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive table-custom">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Skema Sertifikasi</th>
                            <th>Jenis Bukti</th>
                            <th>Nomor Sertifikat</th>
                            <th>Tanggal Berlaku</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody id="riwayatTableBody">
                        @forelse($riwayatList as $riwayat)
                        <tr data-search="{{ \Illuminate\Support\Str::lower(($riwayat->nama_skema ?? '') . ' ' . ($riwayat->jenis_bukti ?? '') . ' ' . ($riwayat->nomor_sertifikat ?? '') . ' ' . ($riwayat->status ?? '')) }}">
                            <td>{{ $riwayat->nama_skema ?? '-' }}</td>
                            <td>{{ $riwayat->jenis_bukti ?? '-' }}</td>
                            <td>{{ $riwayat->nomor_sertifikat ?? '-' }}</td>
                            <td>{{ $riwayat->tanggal_berlaku ?? '-' }}</td>
                            <td>{{ $riwayat->status ?? '-' }}</td>
                            <td>
                                @if(!empty($riwayat->file_sertifikat))
                                    <a href="{{ asset('storage/' . $riwayat->file_sertifikat) }}" target="_blank" class="btn btn-sm btn-primary">
                                        Download
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center empty-table">
                                <i class="fa fa-inbox empty-icon"></i>
                                <p>Belum ada sertifikat yang diterbitkan.</p>
                            </td>
                        </tr>
                    @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

{{-- Bootstrap 5 Bundle (includes Popper) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const menuToggle = document.getElementById('menu-toggle');
        const wrapper = document.getElementById('wrapper');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                wrapper.classList.toggle('toggled');
            });
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                wrapper.classList.toggle('toggled');
            });
        }

        // Menu link active state
        const menuLinks = document.querySelectorAll('.menu-link');
        const contentSections = document.querySelectorAll('.content-section');
        const asesmenSearch = document.getElementById('asesmenSearch');
        const asesmenStatusFilter = document.getElementById('asesmenStatusFilter');
        const riwayatSearch = document.getElementById('riwayatSearch');
        const riwayatSearchButton = document.getElementById('riwayatSearchButton');

        menuLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('data-target');

                // Update active class
                menuLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                // Show/hide content sections
                contentSections.forEach(function(section) {
                    section.classList.toggle('active', section.id === target);
                });

                // Close sidebar on mobile
                if (window.innerWidth < 768) {
                    wrapper.classList.remove('toggled');
                }
            });
        });

        function updateEmptyFilterRow(tbody, colspan, visibleCount, message) {
            let filterEmptyRow = tbody.querySelector('[data-filter-empty]');

            if (!filterEmptyRow) {
                filterEmptyRow = document.createElement('tr');
                filterEmptyRow.setAttribute('data-filter-empty', 'true');
                filterEmptyRow.innerHTML = `<td colspan="${colspan}" class="text-center empty-table"><i class="fa fa-search empty-icon"></i><p>${message}</p></td>`;
                tbody.appendChild(filterEmptyRow);
            }

            filterEmptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }

        function filterAsesmenTable() {
            const tbody = document.getElementById('asesmenTableBody');

            if (!tbody) {
                return;
            }

            const rows = tbody.querySelectorAll('tr[data-search]');
            const searchValue = (asesmenSearch?.value || '').trim().toLowerCase();
            const statusValue = asesmenStatusFilter?.value || '';
            let visibleCount = 0;

            rows.forEach(function(row) {
                const matchesSearch = !searchValue || row.dataset.search.includes(searchValue);
                const matchesStatus = !statusValue || row.dataset.status === statusValue;
                const isVisible = matchesSearch && matchesStatus;

                row.style.display = isVisible ? '' : 'none';
                if (isVisible) {
                    visibleCount++;
                }
            });

            if (rows.length > 0) {
                updateEmptyFilterRow(tbody, 12, visibleCount, 'Tidak ada asesmen yang cocok dengan filter.');
            }
        }

        function filterRiwayatTable() {
            const tbody = document.getElementById('riwayatTableBody');

            if (!tbody) {
                return;
            }

            const rows = tbody.querySelectorAll('tr[data-search]');
            const searchValue = (riwayatSearch?.value || '').trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach(function(row) {
                const isVisible = !searchValue || row.dataset.search.includes(searchValue);

                row.style.display = isVisible ? '' : 'none';
                if (isVisible) {
                    visibleCount++;
                }
            });

            if (rows.length > 0) {
                updateEmptyFilterRow(tbody, 6, visibleCount, 'Tidak ada sertifikat yang cocok dengan pencarian.');
            }
        }

        asesmenSearch?.addEventListener('input', filterAsesmenTable);
        asesmenStatusFilter?.addEventListener('change', filterAsesmenTable);
        riwayatSearch?.addEventListener('input', filterRiwayatTable);
        riwayatSearchButton?.addEventListener('click', filterRiwayatTable);

        // Close sidebar when clicking outside (mobile)
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 768) {
                const sidebar = document.getElementById('sidebar-wrapper');
                const toggleBtn = document.getElementById('sidebarToggle');
                if (sidebar && toggleBtn && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    wrapper.classList.remove('toggled');
                }
            }
        });
    });
</script>

{{-- Live Chat Widget Button --}}
<div id="chatWidget">
    <a href="{{ route('chat.index') }}" class="btn btn-primary btn-lg rounded-circle chat-widget-button"
       title="Live Chat">
        <i class="bi bi-chat-dots"></i>
    </a>
</div>

</body>
</html>
