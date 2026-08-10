<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pilih Skema Sertifikasi | LSP PLGM</title>

    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #5b5ce2;
            --primary-dark: #4849c7;
            --secondary: #7c3aed;
            --surface: #ffffff;
            --page-bg: #f4f6fb;
            --text: #20283a;
            --muted: #667085;
            --border: #e6e9f2;
            --success-bg: #ecfdf3;
            --success-text: #027a48;
            --warning-bg: #fff8e8;
            --warning-text: #9a6700;
            --shadow-sm: 0 8px 24px rgba(31, 41, 55, 0.06);
            --shadow-md: 0 18px 45px rgba(79, 70, 229, 0.14);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--page-bg);
            color: var(--text);
        }

        button,
        input,
        a {
            font: inherit;
        }

        .page-shell {
            min-height: 100vh;
            background:
                radial-gradient(circle at 15% 10%, rgba(255, 255, 255, 0.14) 0 74px, transparent 75px),
                radial-gradient(circle at 88% 16%, rgba(255, 255, 255, 0.09) 0 110px, transparent 111px),
                linear-gradient(to bottom, transparent 0 330px, var(--page-bg) 330px 100%);
        }

        .page-hero {
            min-height: 330px;
            color: #fff;
            background: linear-gradient(120deg, #5d72e8 0%, #6859d6 50%, #7c4bb2 100%);
            position: relative;
            overflow: hidden;
        }

        .page-hero::before,
        .page-hero::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            pointer-events: none;
        }

        .page-hero::before {
            width: 360px;
            height: 360px;
            right: -90px;
            top: -160px;
        }

        .page-hero::after {
            width: 220px;
            height: 220px;
            left: -70px;
            bottom: -130px;
        }

        .content-container {
            width: min(1180px, calc(100% - 40px));
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .hero-inner {
            padding: 34px 0 84px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            min-height: 42px;
            padding: 0 16px;
            margin-bottom: 32px;
            color: #fff;
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.17);
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            backdrop-filter: blur(8px);
            transition: background .2s ease, transform .2s ease;
        }

        .back-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.18);
            transform: translateX(-2px);
        }

        .hero-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
        }

        .hero-heading {
            display: flex;
            align-items: center;
            gap: 20px;
            min-width: 0;
        }

        .hero-icon {
            width: 64px;
            height: 64px;
            flex: 0 0 64px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.19);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
            font-size: 30px;
        }

        .hero-copy h1 {
            margin: 0;
            color: #fff !important;
            font-size: clamp(30px, 3vw, 42px);
            font-weight: 800;
            letter-spacing: -0.8px;
            line-height: 1.15;
        }

        .hero-copy p {
            margin: 9px 0 0;
            color: rgba(255, 255, 255, 0.84);
            font-size: 16px;
            line-height: 1.6;
        }

        .hero-stat {
            min-width: 158px;
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 16px 18px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
        }

        .hero-stat-icon {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.13);
            font-size: 20px;
        }

        .hero-stat strong {
            display: block;
            color: #fff;
            font-size: 24px;
            line-height: 1;
        }

        .hero-stat span {
            display: block;
            margin-top: 5px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 12px;
            font-weight: 600;
        }

        .main-content {
            padding: 0 0 58px;
        }

        .controls-card {
            margin-top: -44px;
            margin-bottom: 24px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--surface);
            border: 1px solid rgba(230, 233, 242, 0.85);
            border-radius: 18px;
            box-shadow: 0 16px 40px rgba(28, 37, 54, 0.11);
        }

        .search-wrapper {
            min-width: 0;
            flex: 1 1 auto;
            position: relative;
        }

        .search-wrapper > i {
            position: absolute;
            left: 17px;
            top: 50%;
            transform: translateY(-50%);
            color: #98a2b3;
            font-size: 18px;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            height: 48px;
            padding: 0 18px 0 48px;
            border: 1px solid var(--border);
            border-radius: 13px;
            outline: none;
            background: #f9fafc;
            color: var(--text);
            font-size: 14px;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .search-input::placeholder {
            color: #98a2b3;
        }

        .search-input:focus {
            background: #fff;
            border-color: rgba(91, 92, 226, 0.65);
            box-shadow: 0 0 0 4px rgba(91, 92, 226, 0.1);
        }

        .filters {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn {
            height: 42px;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid var(--border);
            border-radius: 11px;
            background: #fff;
            color: #475467;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
            transition: color .2s ease, border-color .2s ease, background .2s ease, transform .2s ease;
        }

        .filter-btn:hover {
            color: var(--primary);
            border-color: rgba(91, 92, 226, 0.35);
            background: #f7f7ff;
        }

        .filter-btn.active {
            color: #fff;
            border-color: var(--primary);
            background: var(--primary);
            box-shadow: 0 6px 14px rgba(91, 92, 226, 0.2);
        }

        .results-head {
            min-height: 32px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .results-title {
            margin: 0;
            color: var(--text);
            font-size: 17px;
            font-weight: 750;
        }

        .results-meta {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .skema-card {
            min-width: 0;
            min-height: 272px;
            display: flex;
            flex-direction: column;
            padding: 22px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
            transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
        }

        .skema-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, #667eea, #7c3aed);
            opacity: 0;
            transition: opacity .2s ease;
        }

        .skema-card:hover {
            transform: translateY(-4px);
            border-color: rgba(91, 92, 226, 0.28);
            box-shadow: var(--shadow-md);
        }

        .skema-card:hover::before {
            opacity: 1;
        }

        .card-top {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .card-icon {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: #f0f1ff;
            color: var(--primary);
            font-size: 22px;
        }

        .card-heading {
            min-width: 0;
            flex: 1;
        }

        .skema-name {
            min-height: 48px;
            margin: 0;
            color: var(--text);
            font-size: 17px;
            font-weight: 750;
            line-height: 1.42;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .kode-badge {
            display: inline-flex;
            align-items: center;
            margin-top: 9px;
            padding: 5px 9px;
            border-radius: 8px;
            background: #f4f3ff;
            color: #6941c6;
            border: 1px solid #e9e5ff;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .3px;
        }

        .card-description {
            margin: 18px 0 20px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-footer-row {
            margin-top: auto;
            padding-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-top: 1px solid #f0f1f5;
        }

        .status-badge {
            min-height: 34px;
            padding: 0 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 9px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }

        .status-available {
            color: var(--success-text);
            background: var(--success-bg);
        }

        .status-submitted {
            color: var(--warning-text);
            background: var(--warning-bg);
        }

        .btn-ajukan {
            min-height: 38px;
            padding: 0 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid transparent;
            border-radius: 10px;
            color: #fff;
            background: var(--primary);
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
            transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
        }

        .btn-ajukan:hover {
            color: #fff;
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 7px 16px rgba(91, 92, 226, 0.22);
        }

        .empty-state {
            padding: 54px 24px;
            text-align: center;
            background: var(--surface);
            border: 1px dashed #d7dce8;
            border-radius: 18px;
            box-shadow: var(--shadow-sm);
        }

        .empty-state-icon {
            width: 62px;
            height: 62px;
            margin: 0 auto 16px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            color: var(--primary);
            background: #f0f1ff;
            font-size: 27px;
        }

        .empty-state h3 {
            margin: 0 0 7px;
            color: var(--text);
            font-size: 18px;
            font-weight: 800;
        }

        .empty-state p {
            max-width: 520px;
            margin: 0 auto;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
        }

        @media (max-width: 991.98px) {
            .page-shell {
                background: linear-gradient(to bottom, transparent 0 350px, var(--page-bg) 350px 100%);
            }

            .page-hero {
                min-height: 350px;
            }

            .hero-inner {
                padding-bottom: 82px;
            }

            .controls-card {
                align-items: stretch;
                flex-direction: column;
            }

            .filters {
                overflow-x: auto;
                padding-bottom: 2px;
                scrollbar-width: none;
            }

            .filters::-webkit-scrollbar {
                display: none;
            }

            .cards-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .content-container {
                width: min(100% - 28px, 1180px);
            }

            .hero-inner {
                padding: 24px 0 78px;
            }

            .back-link {
                margin-bottom: 26px;
            }

            .hero-row {
                align-items: flex-start;
                flex-direction: column;
                gap: 20px;
            }

            .hero-heading {
                align-items: flex-start;
                gap: 14px;
            }

            .hero-icon {
                width: 54px;
                height: 54px;
                flex-basis: 54px;
                border-radius: 15px;
                font-size: 25px;
            }

            .hero-copy h1 {
                font-size: 29px;
                letter-spacing: -0.5px;
            }

            .hero-copy p {
                margin-top: 7px;
                font-size: 14px;
            }

            .hero-stat {
                min-width: 0;
                padding: 12px 14px;
            }

            .controls-card {
                margin-top: -40px;
                padding: 12px;
                border-radius: 16px;
            }

            .results-head {
                align-items: flex-start;
                flex-direction: column;
                gap: 3px;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .page-shell {
                background: linear-gradient(to bottom, transparent 0 385px, var(--page-bg) 385px 100%);
            }

            .page-hero {
                min-height: 385px;
            }

            .hero-heading {
                width: 100%;
            }

            .hero-copy h1 {
                font-size: 27px;
            }

            .filter-btn {
                height: 40px;
                padding: 0 12px;
            }

            .skema-card {
                min-height: 0;
                padding: 18px;
            }

            .card-footer-row {
                align-items: stretch;
                flex-direction: column;
            }

            .status-badge,
            .btn-ajukan {
                width: 100%;
                justify-content: center;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <header class="page-hero">
            <div class="content-container hero-inner">
                <a href="{{ route('dashboard.user') }}" class="back-link">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Dashboard</span>
                </a>

                <div class="hero-row">
                    <div class="hero-heading">
                        <div class="hero-icon" aria-hidden="true">
                            <i class="bi bi-patch-check"></i>
                        </div>
                        <div class="hero-copy">
                            <h1>Daftar Skema Sertifikasi</h1>
                            <p>Pilih skema sertifikasi yang paling sesuai dengan kebutuhan kompetensi Anda.</p>
                        </div>
                    </div>

                    <div class="hero-stat" aria-label="Total skema sertifikasi">
                        <div class="hero-stat-icon" aria-hidden="true">
                            <i class="bi bi-collection"></i>
                        </div>
                        <div>
                            <strong>{{ $programs->count() }}</strong>
                            <span>Total Skema</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="content-container">
                <section class="controls-card" aria-label="Pencarian dan filter skema">
                    <div class="search-wrapper">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <input
                            type="search"
                            id="searchSkema"
                            class="search-input"
                            placeholder="Cari nama atau kode skema..."
                            autocomplete="off"
                            aria-label="Cari skema sertifikasi">
                    </div>

                    <div class="filters" role="group" aria-label="Filter status skema">
                        <button type="button" class="filter-btn active" data-filter="all" aria-pressed="true">
                            <i class="bi bi-grid"></i>
                            Semua
                        </button>
                        <button type="button" class="filter-btn" data-filter="available" aria-pressed="false">
                            <i class="bi bi-check-circle"></i>
                            Tersedia
                        </button>
                        <button type="button" class="filter-btn" data-filter="submitted" aria-pressed="false">
                            <i class="bi bi-clock-history"></i>
                            Sudah Diajukan
                        </button>
                    </div>
                </section>

                <div class="results-head">
                    <h2 class="results-title">Skema Sertifikasi</h2>
                    <p class="results-meta" id="resultsMeta">
                        Menampilkan {{ $programs->count() }} dari {{ $programs->count() }} skema
                    </p>
                </div>

                <div class="cards-grid" id="cardsGrid">
                    @forelse($programs as $program)
                        @php
                            $isSubmitted = in_array($program->id, $pengajuanUser);
                        @endphp

                        <article class="skema-card" data-status="{{ $isSubmitted ? 'submitted' : 'available' }}">
                            <div class="card-top">
                                <div class="card-icon" aria-hidden="true">
                                    <i class="bi bi-award"></i>
                                </div>

                                <div class="card-heading">
                                    <h3 class="skema-name">{{ $program->nama }}</h3>
                                    <span class="kode-badge">{{ $program->kode_skema }}</span>
                                </div>
                            </div>

                            <p class="card-description">
                                {{ $program->deskripsi_singkat ?? 'Program sertifikasi kompetensi profesional yang disusun sesuai standar kompetensi dan kebutuhan industri.' }}
                            </p>

                            <div class="card-footer-row">
                                @if($isSubmitted)
                                    <span class="status-badge status-submitted">
                                        <i class="bi bi-clock-history"></i>
                                        Sudah Diajukan
                                    </span>
                                @else
                                    <span class="status-badge status-available">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Tersedia
                                    </span>

                                    <a href="{{ route('pengajuan.create', $program->id) }}" class="btn-ajukan">
                                        Ajukan
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="empty-state" style="grid-column: 1 / -1;">
                            <div class="empty-state-icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <h3>Belum Ada Skema Tersedia</h3>
                            <p>Saat ini belum ada skema sertifikasi yang dapat ditampilkan. Silakan cek kembali nanti.</p>
                        </div>
                    @endforelse
                </div>

                <div id="noResults" class="empty-state" style="display: none;">
                    <div class="empty-state-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3>Skema Tidak Ditemukan</h3>
                    <p>Coba gunakan kata kunci lain atau ubah filter status untuk melihat skema yang tersedia.</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        (() => {
            const searchInput = document.getElementById('searchSkema');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const cards = Array.from(document.querySelectorAll('.skema-card'));
            const cardsGrid = document.getElementById('cardsGrid');
            const noResults = document.getElementById('noResults');
            const resultsMeta = document.getElementById('resultsMeta');
            const totalCards = cards.length;

            let currentSearchTerm = '';
            let currentFilter = 'all';

            function applyFiltersAndSearch() {
                let visibleCount = 0;

                cards.forEach((card) => {
                    const text = card.textContent.toLowerCase();
                    const status = card.dataset.status;
                    const matchesSearch = !currentSearchTerm || text.includes(currentSearchTerm);
                    const matchesFilter = currentFilter === 'all' || currentFilter === status;
                    const isVisible = matchesSearch && matchesFilter;

                    card.style.display = isVisible ? '' : 'none';

                    if (isVisible) {
                        visibleCount += 1;
                    }
                });

                if (totalCards > 0 && visibleCount === 0) {
                    cardsGrid.style.display = 'none';
                    noResults.style.display = 'block';
                } else {
                    cardsGrid.style.display = 'grid';
                    noResults.style.display = 'none';
                }

                if (resultsMeta) {
                    resultsMeta.textContent = `Menampilkan ${visibleCount} dari ${totalCards} skema`;
                }
            }

            searchInput?.addEventListener('input', (event) => {
                currentSearchTerm = event.target.value.trim().toLowerCase();
                applyFiltersAndSearch();
            });

            filterButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    filterButtons.forEach((item) => {
                        item.classList.remove('active');
                        item.setAttribute('aria-pressed', 'false');
                    });

                    button.classList.add('active');
                    button.setAttribute('aria-pressed', 'true');
                    currentFilter = button.dataset.filter;
                    applyFiltersAndSearch();
                });
            });
        })();
    </script>
</body>
</html>
