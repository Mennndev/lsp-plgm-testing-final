@extends('layouts.admin')

@section('title', 'Data Asesi')

@section('content')
<div id="page-content-wrapper">
    <nav class="admin-navbar">
        <div class="nav-title">Data Asesi</div>

        <div class="admin-user">
            <span class="text-muted d-none d-sm-inline">
                {{ now()->format('d M Y') }}
            </span>

            <div class="dropdown">
                <a class="dropdown-toggle text-decoration-none" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ auth()->user()->nama ?? 'Administrator' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-gear me-1"></i> Pengaturan Akun
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    </li>
                </ul>
                <form id="logout-form" class="d-none" method="POST" action="{{ route('logout') }}">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <div class="admin-content">
        <section class="admin-stats mb-4">
            <div class="admin-card">
                <div class="info">
                    <h6>Total Asesi</h6>
                    <h3>{{ $totalAsesi ?? 0 }}</h3>
                    <small class="text-muted">Seluruh akun yang terdaftar</small>
                </div>
                <div class="icon">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </section>

        <div class="admin-table">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="mb-2 mb-md-0">Daftar Asesi</h5>

                <form method="GET" action="{{ route('admin.asesi.index') }}" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari nama/email/skema..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Asesi</th>
                        <th>Email</th>
                        <th>Skema</th>
                        <th>Jadwal</th>
                        <th>Kota</th>
                        <th style="width: 130px;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($asesiList as $index => $item)
                        <tr>
                            <td>{{ ($asesiList->currentPage() - 1) * $asesiList->perPage() + $index + 1 }}</td>
                            <td>{{ $item->user->nama ?? ($item->nama ?? '-') }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->program->nama ?? $item->skema ?? '-' }}</td>
                            <td>{{ $item->jadwal ?? '-' }}</td>
                            <td>{{ $item->kota ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.asesi.show', $item->id) }}"
                                   class="btn btn-sm btn-outline-secondary mb-1">
                                    <i class="bi bi-eye"></i> Detail
                                </a>

                                @if($item->ktp_path)
                                    <a href="{{ asset('storage/'.$item->ktp_path) }}"
                                       class="btn btn-sm btn-outline-primary mb-1" target="_blank">
                                        <i class="bi bi-credit-card-2-front"></i>
                                    </a>
                                @endif

                                @if($item->ttd_path)
                                    <a href="{{ asset('storage/'.$item->ttd_path) }}"
                                       class="btn btn-sm btn-outline-success mb-1" target="_blank">
                                        <i class="bi bi-pen"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Belum ada data asesi.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($asesiList, 'links'))
                <div class="mt-3">
                    {{ $asesiList->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
